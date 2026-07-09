<?php
namespace App\Models;

use CodeIgniter\Model;

class DetailPembelianModel extends Model
{
    protected $table      = 'detail_pembelian';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pembelian_id', 'produk_id', 'harga_beli', 'qty', 'subtotal'];

    public function getByPembelian($pembelianId): array
    {
        return $this->db->table('detail_pembelian dp')
            ->select('dp.*, p.nama as nama_produk, p.kode, p.satuan, p.stok as stok_saat_ini')
            ->join('produk p', 'p.id = dp.produk_id')
            ->where('dp.pembelian_id', $pembelianId)
            ->get()->getResultArray();
    }
}
