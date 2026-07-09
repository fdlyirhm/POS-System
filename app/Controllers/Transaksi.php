<?php
// app/Controllers/Transaksi.php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use App\Models\ProdukModel;
use App\Models\StokLogModel;
use App\Models\HutangModel;
use App\Models\DiskonModel;

class Transaksi extends BaseController
{
    protected $transaksiModel;
    protected $detailModel;
    protected $produkModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->detailModel    = new DetailTransaksiModel();
        $this->produkModel    = new ProdukModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $status  = $this->request->getGet('status');
        $dari    = $this->request->getGet('dari') ?? date('Y-m-01');
        $sampai  = $this->request->getGet('sampai') ?? date('Y-m-d');

        return view('transaksi/index', [
            'title'     => 'Daftar Transaksi',
            'transaksi' => $this->transaksiModel->getTransaksi($keyword, $status, $dari, $sampai),
            'keyword'   => $keyword,
            'status'    => $status,
            'dari'      => $dari,
            'sampai'    => $sampai,
        ]);
    }

    public function pos()
    {
        return view('transaksi/pos', [
            'title'        => 'Kasir POS — ' . ($this->toko['nama_toko'] ?? 'Toko Sembako'),
            'pajak_persen' => (float)($this->toko['pajak_persen'] ?? 11),
        ]);
    }

    public function cariBarcode($barcode)
    {
        $produk = $this->produkModel
            ->where('barcode', $barcode)
            ->where('status', 'aktif')
            ->first();

        return $this->response->setJSON(
            $produk
            ? ['status' => true,  'data'    => $produk]
            : ['status' => false, 'message' => 'Produk tidak ditemukan']
        );
    }

    public function cariKode($kode)
    {
        $produk = $this->produkModel
            ->groupStart()
                ->like('kode', $kode)
                ->orLike('nama', $kode)
                ->orLike('barcode', $kode)
            ->groupEnd()
            ->where('status', 'aktif')
            ->findAll(10);

        return $this->response->setJSON(['status' => true, 'data' => $produk]);
    }

    public function simpan()
    {
        $items         = json_decode($this->request->getPost('items'), true);
        $bayar         = (float)$this->request->getPost('bayar');
        $metode        = $this->request->getPost('metode_bayar');
        $diskonId      = $this->request->getPost('diskon_id');
        $diskonNominal = (float)$this->request->getPost('diskon_nominal');
        $pelanggan     = $this->request->getPost('pelanggan') ?: 'Umum';

        if (empty($items)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Keranjang kosong']);
        }

        $subtotal = array_sum(array_map(fn($i) => $i['harga'] * $i['qty'], $items));

        // Pajak dari pengaturan (bukan hardcode)
        $pajakPersen   = (float)($this->toko['pajak_persen'] ?? 11);
        $setelahDiskon = max(0, $subtotal - $diskonNominal);
        $pajak         = round($setelahDiskon * $pajakPersen / 100);
        $total         = $setelahDiskon + $pajak;
        $kembalian     = $metode === 'tunai' ? max(0, $bayar - $total) : 0;

        $noTrx       = $this->transaksiModel->generateNoTransaksi();
        $transaksiId = $this->transaksiModel->insert([
            'no_transaksi' => $noTrx,
            'tanggal'      => date('Y-m-d H:i:s'),
            'user_id'      => session()->get('user_id'),
            'pelanggan'    => $pelanggan,
            'subtotal'     => $subtotal,
            'diskon'       => $diskonNominal,
            'pajak'        => $pajak,
            'total'        => $total,
            'bayar'        => $bayar,
            'kembalian'    => $kembalian,
            'metode_bayar' => $metode,
            'status'       => $metode === 'kredit' ? 'kredit' : 'lunas',
        ]);

        $stokLog = new StokLogModel();
        foreach ($items as $item) {
            $this->detailModel->insert([
                'transaksi_id' => $transaksiId,
                'produk_id'    => $item['produk_id'],
                'harga'        => $item['harga'],
                'qty'          => $item['qty'],
                'diskon'       => 0,
                'subtotal'     => $item['harga'] * $item['qty'],
            ]);

            $produk   = $this->produkModel->find($item['produk_id']);
            $stokBaru = max(0, $produk['stok'] - $item['qty']);
            $this->produkModel->update($item['produk_id'], ['stok' => $stokBaru]);

            $stokLog->insert([
                'produk_id'    => $item['produk_id'],
                'user_id'      => session()->get('user_id'),
                'jenis'        => 'keluar',
                'jumlah'       => $item['qty'],
                'stok_sebelum' => $produk['stok'],
                'stok_sesudah' => $stokBaru,
                'keterangan'   => 'Penjualan ' . $noTrx,
            ]);
        }

        // Kurangi kuota diskon
        if ($diskonId) {
            (new DiskonModel())->pakaiDiskon((int)$diskonId);
        }

        // Buat hutang otomatis jika kredit
        if ($metode === 'kredit') {
            (new HutangModel())->insert([
                'transaksi_id' => $transaksiId,
                'pelanggan'    => $pelanggan,
                'telepon'      => $this->request->getPost('telepon_pelanggan') ?? null,
                'total_hutang' => $total,
                'total_bayar'  => 0,
                'sisa_hutang'  => $total,
                'jatuh_tempo'  => date('Y-m-d', strtotime('+30 days')),
                'status'       => 'belum_lunas',
            ]);
        }

        return $this->response->setJSON([
            'status'       => true,
            'message'      => 'Transaksi berhasil',
            'no_transaksi' => $noTrx,
            'transaksi_id' => $transaksiId,
            'kembalian'    => $kembalian,
        ]);
    }

    public function detail($id)
    {
        $transaksi = $this->transaksiModel->getDetail($id);
        if (!$transaksi) return redirect()->to('/transaksi')->with('error', 'Tidak ditemukan');

        return view('transaksi/detail', [
            'title'     => 'Detail Transaksi',
            'transaksi' => $transaksi,
            'detail'    => $this->detailModel->getByTransaksi($id),
        ]);
    }

    public function struk($id)
    {
        $transaksi = $this->transaksiModel->getDetail($id);
        if (!$transaksi) return redirect()->to('/transaksi');

        return view('transaksi/struk', [
            'transaksi' => $transaksi,
            'detail'    => $this->detailModel->getByTransaksi($id),
            'toko'      => $this->toko,
        ]);
    }

    public function batal($id)
    {
        $this->transaksiModel->update($id, ['status' => 'batal']);
        return redirect()->to('/transaksi')->with('success', 'Transaksi dibatalkan.');
    }
}
