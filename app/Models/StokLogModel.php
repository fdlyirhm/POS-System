<?php
namespace App\Models;

use CodeIgniter\Model;

class StokLogModel extends Model
{
    protected $table         = 'stok_log';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['produk_id', 'user_id', 'jenis', 'jumlah', 'stok_sebelum', 'stok_sesudah', 'keterangan'];
}
