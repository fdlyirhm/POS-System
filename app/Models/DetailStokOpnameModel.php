<?php
namespace App\Models;

use CodeIgniter\Model;

class DetailStokOpnameModel extends Model
{
    protected $table      = 'detail_stok_opname';
    protected $primaryKey = 'id';
    protected $allowedFields = ['opname_id', 'produk_id', 'stok_sistem', 'stok_fisik', 'keterangan'];
}
