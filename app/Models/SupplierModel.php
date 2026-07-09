<?php
namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table         = 'supplier';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['kode', 'nama', 'alamat', 'telepon', 'email', 'kontak_person'];
}
