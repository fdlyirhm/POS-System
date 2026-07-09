<?php
namespace App\Models;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $table      = 'pembelian';
    protected $primaryKey = 'id';
    protected $allowedFields = ['no_pembelian', 'tanggal', 'supplier_id', 'user_id', 'total', 'status', 'catatan'];

    public function generateNoPembelian(): string
    {
        $last = $this->selectMax('id')->first();
        $next = ($last['id'] ?? 0) + 1;
        return 'PBL-' . date('Ymd') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function getAll($keyword = null, $status = null, $dari = null, $sampai = null): array
    {
        $builder = $this->db->table('pembelian p')
            ->select('p.*, s.nama as nama_supplier, u.nama as nama_user')
            ->join('supplier s', 's.id = p.supplier_id')
            ->join('users u', 'u.id = p.user_id')
            ->orderBy('p.tanggal', 'DESC');

        if ($keyword) {
            $builder->groupStart()
                ->like('p.no_pembelian', $keyword)
                ->orLike('s.nama', $keyword)
                ->groupEnd();
        }
        if ($status)  $builder->where('p.status', $status);
        if ($dari)    $builder->where('p.tanggal >=', $dari);
        if ($sampai)  $builder->where('p.tanggal <=', $sampai);

        return $builder->get()->getResultArray();
    }

    public function getDetail($id): ?array
    {
        return $this->db->table('pembelian p')
            ->select('p.*, s.nama as nama_supplier, s.telepon as telp_supplier, u.nama as nama_user')
            ->join('supplier s', 's.id = p.supplier_id')
            ->join('users u', 'u.id = p.user_id')
            ->where('p.id', $id)
            ->get()->getRowArray();
    }
}
