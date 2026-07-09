<?php

namespace App\Controllers;

use App\Models\HutangModel;
use App\Models\TransaksiModel;
use Config\Database; // Tambahkan ini

class Hutang extends BaseController
{
    protected $hutangModel;
    protected $db; // Deklarasikan properti db

    public function __construct()
    {
        $this->hutangModel = new HutangModel();
        // Inisialisasi database agar bisa menggunakan $this->db
        $this->db = Database::connect(); 
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $status  = $this->request->getGet('status');

        return view('hutang/index', [
            'title'           => 'Hutang Pelanggan',
            'hutang'          => $this->hutangModel->getAll($keyword, $status),
            'total_hutang'    => $this->hutangModel->totalHutangAktif(),
            'jatuh_tempo'     => $this->hutangModel->jumlahHutangJatuhTempo(),
            'keyword'         => $keyword,
            'status'          => $status,
        ]);
    }

    public function detail($id)
    {
        $hutang = $this->hutangModel->find($id);
        if (!$hutang) return redirect()->to('/hutang')->with('error', 'Data tidak ditemukan');

        // Perbaikan: Gunakan spasi untuk alias table, bukan titik (.)
        // 'pembayaran_hutang ph' artinya tabel pembayaran_hutang dengan alias ph
        $riwayat = $this->db->table('pembayaran_hutang ph')
            ->select('ph.*, u.nama as nama_user')
            ->join('users u', 'u.id = ph.user_id')
            ->where('ph.hutang_id', $id)
            ->orderBy('ph.tanggal', 'DESC')
            ->get()->getResultArray();

        return view('hutang/detail', [
            'title'   => 'Detail Hutang',
            'hutang'  => $hutang,
            'riwayat' => $riwayat,
        ]);
    }

    public function bayar($id)
    {
        $hutang  = $this->hutangModel->find($id);
        $jumlah  = (float)$this->request->getPost('jumlah');

        if (!$hutang || $jumlah <= 0) {
            return redirect()->back()->with('error', 'Jumlah pembayaran tidak valid.');
        }

        $totalBayarBaru = $hutang['total_bayar'] + $jumlah;
        $sisaBaru       = $hutang['total_hutang'] - $totalBayarBaru;
        $statusBaru     = $sisaBaru <= 0 ? 'lunas' : 'belum_lunas';

        // Update data utama di tabel hutang
        $this->hutangModel->update($id, [
            'total_bayar' => $totalBayarBaru,
            'sisa_hutang' => max(0, $sisaBaru),
            'status'      => $statusBaru,
        ]);

        // Simpan riwayat pembayaran ke tabel pembayaran_hutang
        $this->db->table('pembayaran_hutang')->insert([
            'hutang_id' => $id,
            'user_id'   => session()->get('user_id'),
            'jumlah'    => $jumlah,
            'tanggal'   => date('Y-m-d H:i:s'),
            'catatan'   => $this->request->getPost('catatan'),
        ]);

        $pesan = $statusBaru === 'lunas' ? 'Hutang lunas!' : 'Pembayaran berhasil dicatat.';
        return redirect()->to('/hutang/detail/' . $id)->with('success', $pesan);
    }
}