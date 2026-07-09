<?php
namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\ProdukModel;
use App\Models\HutangModel;
use App\Models\PembelianModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $transaksiModel = new TransaksiModel();
        $produkModel    = new ProdukModel();
        $hutangModel    = new HutangModel();
        $pembelianModel = new PembelianModel();

        $today = date('Y-m-d');

        $data = [
            'title'             => 'Dashboard',
            'penjualan_hari'    => $transaksiModel->penjualanHariIni($today),
            'total_transaksi'   => $transaksiModel->totalTransaksiHariIni($today),
            'stok_rendah'       => $produkModel->stokRendah(),
            'transaksi_terbaru' => $transaksiModel->transaksiTerbaru(5),
            'produk_terlaris'   => $transaksiModel->produkTerlarisBulanIni(),
            'total_hutang'      => $hutangModel->totalHutangAktif(),
            'hutang_jatuh_tempo'=> $hutangModel->jumlahHutangJatuhTempo(),
            'pembelian_pending' => count($pembelianModel->getAll(null, 'pending')),
        ];

        return view('dashboard/index', $data);
    }
}
