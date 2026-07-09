<?php
namespace App\Models;

use CodeIgniter\Model;

class StokOpnameModel extends Model
{
    protected $table      = 'stok_opname';
    protected $primaryKey = 'id';
    protected $allowedFields = ['no_opname', 'tanggal', 'user_id', 'catatan', 'status'];

    public function generateNoOpname(): string
    {
        $last = $this->selectMax('id')->first();
        $next = ($last['id'] ?? 0) + 1;
        return 'OPN-' . date('Ymd') . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function getAll(): array
    {
        return $this->db->table('stok_opname o')
            ->select('o.*, u.nama as nama_user')
            ->join('users u', 'u.id = o.user_id')
            ->orderBy('o.created_at', 'DESC')
            ->get()->getResultArray();
    }

    public function getDetail($id): array
    {
        return $this->db->table('detail_stok_opname d')
            ->select('d.*, p.nama as nama_produk, p.kode, p.satuan, k.nama as nama_kategori')
            ->join('produk p', 'p.id = d.produk_id')
            ->join('kategori k', 'k.id = p.kategori_id')
            ->where('d.opname_id', $id)
            ->orderBy('p.nama', 'ASC')
            ->get()->getResultArray();
    }
}
