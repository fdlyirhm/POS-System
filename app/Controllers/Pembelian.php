<?php
namespace App\Controllers;

use App\Models\PembelianModel;
use App\Models\DetailPembelianModel;
use App\Models\ProdukModel;
use App\Models\SupplierModel;
use App\Models\StokLogModel;

class Pembelian extends BaseController
{
    protected $pembelianModel;
    protected $detailModel;
    protected $produkModel;

    public function __construct()
    {
        $this->pembelianModel = new PembelianModel();
        $this->detailModel    = new DetailPembelianModel();
        $this->produkModel    = new ProdukModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $status  = $this->request->getGet('status');
        $dari    = $this->request->getGet('dari') ?? date('Y-m-01');
        $sampai  = $this->request->getGet('sampai') ?? date('Y-m-d');

        return view('pembelian/index', [
            'title'     => 'Pembelian / Restock',
            'pembelian' => $this->pembelianModel->getAll($keyword, $status, $dari, $sampai),
            'keyword'   => $keyword,
            'status'    => $status,
            'dari'      => $dari,
            'sampai'    => $sampai,
        ]);
    }

    public function buat()
    {
        return view('pembelian/buat', [
            'title'    => 'Buat Pembelian / Restock',
            'supplier' => (new SupplierModel())->orderBy('nama')->findAll(),
            'produk'   => $this->produkModel->getProduk(null, null, 'aktif'),
        ]);
    }

    public function simpan()
    {
        $produkIds  = $this->request->getPost('produk_id');
        $hargaBelis = $this->request->getPost('harga_beli');
        $qtys       = $this->request->getPost('qty');

        if (empty($produkIds)) {
            return redirect()->back()->with('error', 'Tambahkan minimal satu produk.');
        }

        $total = 0;
        foreach ($produkIds as $i => $pid) {
            $total += (float)$hargaBelis[$i] * (int)$qtys[$i];
        }

        $noPembelian = $this->pembelianModel->generateNoPembelian();
        $pembelianId = $this->pembelianModel->insert([
            'no_pembelian' => $noPembelian,
            'tanggal'      => $this->request->getPost('tanggal') ?? date('Y-m-d'),
            'supplier_id'  => $this->request->getPost('supplier_id'),
            'user_id'      => session()->get('user_id'),
            'total'        => $total,
            'status'       => 'pending',
            'catatan'      => $this->request->getPost('catatan'),
        ]);

        foreach ($produkIds as $i => $pid) {
            $this->detailModel->insert([
                'pembelian_id' => $pembelianId,
                'produk_id'    => $pid,
                'harga_beli'   => $hargaBelis[$i],
                'qty'          => $qtys[$i],
                'subtotal'     => (float)$hargaBelis[$i] * (int)$qtys[$i],
            ]);
        }

        return redirect()->to('/pembelian/detail/' . $pembelianId)
                         ->with('success', 'Pembelian ' . $noPembelian . ' berhasil dibuat.');
    }

    public function detail($id)
    {
        $pembelian = $this->pembelianModel->getDetail($id);
        if (!$pembelian) return redirect()->to('/pembelian')->with('error', 'Data tidak ditemukan');

        return view('pembelian/detail', [
            'title'     => 'Detail Pembelian',
            'pembelian' => $pembelian,
            'detail'    => $this->detailModel->getByPembelian($id),
        ]);
    }

    // Terima barang: tambah stok + update harga beli
    public function terima($id)
    {
        $pembelian = $this->pembelianModel->getDetail($id);
        if (!$pembelian || $pembelian['status'] !== 'pending') {
            return redirect()->to('/pembelian')->with('error', 'Pembelian tidak valid atau sudah diproses.');
        }

        $detail       = $this->detailModel->getByPembelian($id);
        $stokLogModel = new StokLogModel();

        foreach ($detail as $d) {
            $produk      = $this->produkModel->find($d['produk_id']);
            $stokBaru    = $produk['stok'] + $d['qty'];

            $this->produkModel->update($d['produk_id'], [
                'stok'       => $stokBaru,
                'harga_beli' => $d['harga_beli'],
            ]);

            $stokLogModel->insert([
                'produk_id'    => $d['produk_id'],
                'user_id'      => session()->get('user_id'),
                'jenis'        => 'masuk',
                'jumlah'       => $d['qty'],
                'stok_sebelum' => $produk['stok'],
                'stok_sesudah' => $stokBaru,
                'keterangan'   => 'Restock pembelian ' . $pembelian['no_pembelian'],
            ]);
        }

        $this->pembelianModel->update($id, ['status' => 'diterima']);
        return redirect()->to('/pembelian/detail/' . $id)
                         ->with('success', 'Barang diterima. Stok produk berhasil ditambahkan.');
    }

    public function batal($id)
    {
        $this->pembelianModel->update($id, ['status' => 'batal']);
        return redirect()->to('/pembelian')->with('success', 'Pembelian dibatalkan.');
    }
}
