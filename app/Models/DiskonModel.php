<?php
namespace App\Models;

use CodeIgniter\Model;

class DiskonModel extends Model
{
    protected $table      = 'diskon';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kode', 'nama', 'tipe', 'nilai', 'min_belanja',
        'berlaku_dari', 'berlaku_sampai', 'kuota', 'terpakai', 'status'
    ];

    public function cekKode(string $kode, float $subtotal): array
    {
        $diskon = $this->where('kode', strtoupper($kode))
                       ->where('status', 'aktif')
                       ->where('berlaku_dari <=', date('Y-m-d'))
                       ->where('berlaku_sampai >=', date('Y-m-d'))
                       ->first();

        if (!$diskon) {
            return ['valid' => false, 'pesan' => 'Kode diskon tidak valid atau sudah kadaluarsa'];
        }
        if ($subtotal < $diskon['min_belanja']) {
            return ['valid' => false, 'pesan' => 'Minimum belanja Rp ' . number_format($diskon['min_belanja'], 0, ',', '.') . ' untuk kode ini'];
        }
        if ($diskon['kuota'] !== null && $diskon['terpakai'] >= $diskon['kuota']) {
            return ['valid' => false, 'pesan' => 'Kuota diskon sudah habis'];
        }

        $nominal = $diskon['tipe'] === 'persen'
            ? round($subtotal * $diskon['nilai'] / 100)
            : (float)$diskon['nilai'];

        return [
            'valid'   => true,
            'diskon'  => $diskon,
            'nominal' => $nominal,
            'pesan'   => 'Diskon ' . ($diskon['tipe'] === 'persen' ? $diskon['nilai'] . '%' : 'Rp ' . number_format($diskon['nilai'], 0, ',', '.')) . ' berhasil diterapkan',
        ];
    }

    public function pakaiDiskon(int $id): void
    {
        $this->db->table('diskon')->where('id', $id)->increment('terpakai');
    }

    public function getAktif(): array
    {
        return $this->where('status', 'aktif')
                    ->where('berlaku_dari <=', date('Y-m-d'))
                    ->where('berlaku_sampai >=', date('Y-m-d'))
                    ->findAll();
    }
}
