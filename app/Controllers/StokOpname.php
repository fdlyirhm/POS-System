<?php
namespace App\Controllers;

use App\Models\StokOpnameModel;
use App\Models\DetailStokOpnameModel;
use App\Models\ProdukModel;
use App\Models\StokLogModel;

class StokOpname extends BaseController
{
    protected $opnameModel;
    protected $detailModel;
    protected $produkModel;

    public function __construct()
    {
        $this->opnameModel  = new StokOpnameModel();
        $this->detailModel  = new DetailStokOpnameModel();
        $this->produkModel  = new ProdukModel();
    }

    public function index()
    {
        return view('stok_opname/index', [
            'title'   => 'Stok Opname',
            'opname'  => $this->opnameModel->getAll(),
        ]);
    }

    public function buat()
    {
        $produk = $this->produkModel->getProduk(null, null, 'aktif');
        return view('stok_opname/buat', [
            'title'  => 'Buat Stok Opname Baru',
            'produk' => $produk,
        ]);
    }

    public function simpan()
    {
        $noOpname = $this->opnameModel->generateNoOpname();

        $opnameId = $this->opnameModel->insert([
            'no_opname' => $noOpname,
            'tanggal'   => $this->request->getPost('tanggal') ?? date('Y-m-d'),
            'user_id'   => session()->get('user_id'),
            'catatan'   => $this->request->getPost('catatan'),
            'status'    => 'draft',
        ]);

        $produkIds  = $this->request->getPost('produk_id');
        $stokFisiks = $this->request->getPost('stok_fisik');
        $keterangans = $this->request->getPost('keterangan_item');

        foreach ($produkIds as $i => $produkId) {
            $produk     = $this->produkModel->find($produkId);
            $stokFisik  = (int)($stokFisiks[$i] ?? 0);

            $this->detailModel->insert([
                'opname_id'   => $opnameId,
                'produk_id'   => $produkId,
                'stok_sistem' => $produk['stok'],
                'stok_fisik'  => $stokFisik,
                'keterangan'  => $keterangans[$i] ?? null,
            ]);
        }

        return redirect()->to('/stok-opname/detail/' . $opnameId)
                         ->with('success', 'Stok opname berhasil disimpan sebagai draft.');
    }

    public function detail($id)
    {
        $opname = $this->opnameModel->find($id);
        if (!$opname) return redirect()->to('/stok-opname')->with('error', 'Data tidak ditemukan');

        return view('stok_opname/detail', [
            'title'  => 'Detail Stok Opname',
            'opname' => $opname,
            'detail' => $this->opnameModel->getDetail($id),
        ]);
    }

    // Finalisasi: terapkan selisih ke stok nyata
    public function selesaikan($id)
    {
        $opname = $this->opnameModel->find($id);
        if (!$opname || $opname['status'] === 'selesai') {
            return redirect()->to('/stok-opname')->with('error', 'Opname tidak valid atau sudah diselesaikan.');
        }

        $detail      = $this->opnameModel->getDetail($id);
        $stokLogModel = new StokLogModel();

        foreach ($detail as $d) {
            if ($d['selisih'] !== 0) {
                // Update stok produk ke stok fisik
                $this->produkModel->update($d['produk_id'], ['stok' => $d['stok_fisik']]);

                // Catat ke stok log
                $stokLogModel->insert([
                    'produk_id'    => $d['produk_id'],
                    'user_id'      => session()->get('user_id'),
                    'jenis'        => 'koreksi',
                    'jumlah'       => abs($d['selisih']),
                    'stok_sebelum' => $d['stok_sistem'],
                    'stok_sesudah' => $d['stok_fisik'],
                    'keterangan'   => 'Koreksi stok opname ' . $opname['no_opname'],
                ]);
            }
        }

        $this->opnameModel->update($id, ['status' => 'selesai']);
        return redirect()->to('/stok-opname/detail/' . $id)
                         ->with('success', 'Stok opname selesai. Stok produk telah diperbarui.');
    }
}
