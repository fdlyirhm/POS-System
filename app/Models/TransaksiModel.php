<?php
// app/Models/TransaksiModel.php
namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table      = 'transaksi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'no_transaksi','tanggal','user_id','pelanggan',
        'subtotal','diskon','pajak','total','bayar','kembalian',
        'metode_bayar','status','catatan'
    ];

    public function generateNoTransaksi(): string
    {
        $last = $this->selectMax('id')->first();
        $next = ($last['id'] ?? 0) + 1;
        return 'TRX-' . date('Ymd') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function getTransaksi($keyword = null, $status = null, $dari = null, $sampai = null): array
    {
        $builder = $this->db->table('transaksi t')
            ->select('t.*, u.nama as nama_kasir')
            ->join('users u', 'u.id = t.user_id')
            ->orderBy('t.tanggal', 'DESC');

        if ($keyword) {
            $builder->groupStart()
                ->like('t.no_transaksi', $keyword)
                ->orLike('t.pelanggan', $keyword)
                ->groupEnd();
        }
        if ($status) $builder->where('t.status', $status);
        if ($dari)   $builder->where('DATE(t.tanggal) >=', $dari);
        if ($sampai) $builder->where('DATE(t.tanggal) <=', $sampai);

        return $builder->get()->getResultArray();
    }

    public function getDetail($id): ?array
    {
        return $this->db->table('transaksi t')
            ->select('t.*, u.nama as nama_kasir')
            ->join('users u', 'u.id = t.user_id')
            ->where('t.id', $id)
            ->get()->getRowArray();
    }

    public function penjualanHariIni($tanggal): float
    {
        $result = $this->selectSum('total')
                       ->where('DATE(tanggal)', $tanggal)
                       ->where('status !=', 'batal')
                       ->first();
        return (float)($result['total'] ?? 0);
    }

    public function totalTransaksiHariIni($tanggal): int
    {
        return $this->where('DATE(tanggal)', $tanggal)
                    ->where('status !=', 'batal')
                    ->countAllResults();
    }

    public function transaksiTerbaru($limit = 5): array
    {
        return $this->db->table('transaksi t')
            ->select('t.id, t.no_transaksi, t.tanggal, t.pelanggan, t.total, t.status, u.nama as kasir')
            ->join('users u', 'u.id = t.user_id')
            ->where('t.status !=', 'batal')
            ->orderBy('t.tanggal', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function produkTerlarisBulanIni(): array
    {
        return $this->produkTerlaris(date('Y-m-01'), date('Y-m-d'), 5);
    }

    public function produkTerlaris($dari, $sampai, $limit = 10): array
    {
        return $this->db->table('detail_transaksi dt')
            ->select('p.nama, p.satuan, k.nama as nama_kategori, SUM(dt.qty) as total_qty, SUM(dt.subtotal) as total_penjualan')
            ->join('produk p', 'p.id = dt.produk_id')
            ->join('kategori k', 'k.id = p.kategori_id')
            ->join('transaksi t', 't.id = dt.transaksi_id')
            ->where('DATE(t.tanggal) >=', $dari)
            ->where('DATE(t.tanggal) <=', $sampai)
            ->where('t.status !=', 'batal')
            ->groupBy('dt.produk_id')
            ->orderBy('total_qty', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    public function ringkasanHarian($tanggal): array
    {
        return $this->db->table('transaksi')
            ->select('COUNT(*) as jumlah_transaksi, SUM(subtotal) as subtotal, SUM(total) as total_penjualan, SUM(pajak) as total_pajak, SUM(diskon) as total_diskon')
            ->where('DATE(tanggal)', $tanggal)
            ->where('status !=', 'batal')
            ->get()->getRowArray() ?? [];
    }

    public function ringkasanBulanan($dari, $sampai): array
    {
        return $this->db->table('transaksi')
            ->select('COUNT(*) as jumlah_transaksi, SUM(subtotal) as subtotal, SUM(total) as total_penjualan, SUM(pajak) as total_pajak, SUM(diskon) as total_diskon')
            ->where('DATE(tanggal) >=', $dari)
            ->where('DATE(tanggal) <=', $sampai)
            ->where('status !=', 'batal')
            ->get()->getRowArray() ?? [];
    }

    public function penjualanPerKategori($dari, $sampai): array
    {
        return $this->db->table('detail_transaksi dt')
            ->select('k.nama as kategori, SUM(dt.qty) as total_qty, SUM(dt.subtotal) as total')
            ->join('produk p', 'p.id = dt.produk_id')
            ->join('kategori k', 'k.id = p.kategori_id')
            ->join('transaksi t', 't.id = dt.transaksi_id')
            ->where('DATE(t.tanggal) >=', $dari)
            ->where('DATE(t.tanggal) <=', $sampai)
            ->where('t.status !=', 'batal')
            ->groupBy('k.id')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();
    }

    public function grafikBulanan($dari, $sampai): array
    {
        return $this->db->table('transaksi')
            ->select('DATE(tanggal) as tanggal, SUM(total) as total')
            ->where('DATE(tanggal) >=', $dari)
            ->where('DATE(tanggal) <=', $sampai)
            ->where('status !=', 'batal')
            ->groupBy('DATE(tanggal)')
            ->orderBy('tanggal', 'ASC')
            ->get()->getResultArray();
    }
    public function penjualanPerJam(string $tanggal): array
    {
        $rows = $this->db->table('transaksi')
            ->select("HOUR(tanggal) as jam, SUM(total) as total, COUNT(*) as jumlah")
            ->where('DATE(tanggal)', $tanggal)
            ->where('status !=', 'batal')
            ->groupBy('HOUR(tanggal)')
            ->orderBy('jam', 'ASC')
            ->get()->getResultArray();

        // Isi jam yang tidak ada transaksi dengan 0, supaya grafik 24 jam lengkap
        $hasil = [];
        for ($h = 0; $h < 24; $h++) {
            $hasil[$h] = ['jam' => $h, 'total' => 0, 'jumlah' => 0];
        }
        foreach ($rows as $r) {
            $hasil[(int)$r['jam']] = $r;
        }
        return array_values($hasil);
    }
}
