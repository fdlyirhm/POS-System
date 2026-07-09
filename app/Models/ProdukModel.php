<?php
// app/Models/ProdukModel.php
namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table      = 'produk';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kode','barcode','nama','kategori_id','supplier_id',
        'harga_beli','harga_jual','stok','stok_minimum',
        'satuan','deskripsi','status'
    ];

    public function getProduk($keyword = null, $kategoriId = null, $status = null): array
    {
        $builder = $this->db->table('produk p')
            ->select('p.*, k.nama as nama_kategori, s.nama as nama_supplier')
            ->join('kategori k', 'k.id = p.kategori_id')
            ->join('supplier s', 's.id = p.supplier_id', 'left')
            ->orderBy('p.nama', 'ASC');

        if ($keyword) {
            $builder->groupStart()
                ->like('p.nama', $keyword)
                ->orLike('p.kode', $keyword)
                ->orLike('p.barcode', $keyword)
                ->groupEnd();
        }
        if ($kategoriId) $builder->where('p.kategori_id', $kategoriId);
        if ($status)     $builder->where('p.status', $status);
        else             $builder->where('p.status', 'aktif');

        return $builder->get()->getResultArray();
    }

    public function stokRendah(): array
    {
        return $this->db->table('produk p')
            ->select('p.*, k.nama as nama_kategori')
            ->join('kategori k', 'k.id = p.kategori_id')
            ->where('p.stok <= p.stok_minimum')
            ->where('p.status', 'aktif')
            ->orderBy('p.stok', 'ASC')
            ->get()->getResultArray();
    }
}
