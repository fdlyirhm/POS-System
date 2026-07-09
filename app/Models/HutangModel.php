<?php
namespace App\Models;

use CodeIgniter\Model;

class HutangModel extends Model
{
    protected $table      = 'hutang';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'transaksi_id', 'pelanggan', 'telepon', 'total_hutang',
        'total_bayar', 'sisa_hutang', 'jatuh_tempo', 'status', 'catatan'
    ];

    public function getAll($keyword = null, $status = null): array
    {
        $builder = $this->db->table('hutang h')
            ->select('h.*, t.no_transaksi')
            ->join('transaksi t', 't.id = h.transaksi_id')
            ->orderBy('h.status', 'ASC')
            ->orderBy('h.jatuh_tempo', 'ASC');

        if ($keyword) {
            $builder->groupStart()
                ->like('h.pelanggan', $keyword)
                ->orLike('h.telepon', $keyword)
                ->orLike('t.no_transaksi', $keyword)
                ->groupEnd();
        }
        if ($status) $builder->where('h.status', $status);

        return $builder->get()->getResultArray();
    }

    public function getByTransaksi($transaksiId): ?array
    {
        return $this->where('transaksi_id', $transaksiId)->first();
    }

    public function totalHutangAktif(): float
    {
        $result = $this->selectSum('sisa_hutang')->where('status', 'belum_lunas')->first();
        return (float)($result['sisa_hutang'] ?? 0);
    }

    public function jumlahHutangJatuhTempo(): int
    {
        return $this->where('status', 'belum_lunas')
                    ->where('jatuh_tempo <=', date('Y-m-d'))
                    ->countAllResults();
    }
}
