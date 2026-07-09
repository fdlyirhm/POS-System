<?php
namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table         = 'detail_transaksi';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['transaksi_id', 'produk_id', 'harga', 'qty', 'diskon', 'subtotal'];

    public function getByTransaksi($transaksiId): array
    {
        return $this->db->table('detail_transaksi dt')
            ->select('dt.*, p.nama as nama_produk, p.kode, p.satuan')
            ->join('produk p', 'p.id = dt.produk_id')
            ->where('dt.transaksi_id', $transaksiId)
            ->get()->getResultArray();
    }
}
