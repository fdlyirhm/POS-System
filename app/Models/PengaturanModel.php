<?php
namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table      = 'pengaturan';
    protected $primaryKey = 'id';
    protected $allowedFields = ['key', 'value', 'keterangan'];

    public function getAll(): array
    {
        $rows   = $this->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }

    public function ambil(string $key, $default = null)
    {
        $row = $this->where('`key`', $key)->first();
        return $row ? $row['value'] : $default;
    }

    public function simpan(string $key, $value): void
    {
        $existing = $this->where('`key`', $key)->first();
        if ($existing) {
            $this->where('`key`', $key)->update(null, ['value' => $value]);
        } else {
            $this->insert(['key' => $key, 'value' => $value]);
        }
    }

    public function simpanSemua(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->simpan($key, $value);
        }
    }
}
