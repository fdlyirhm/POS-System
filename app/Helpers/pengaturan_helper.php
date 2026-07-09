<?php
// app/Helpers/pengaturan_helper.php
//
// Cara aktifkan: tambahkan 'pengaturan' ke $helpers di BaseController
// atau di app/Config/Autoload.php pada $helpers array
//
// Cara pakai di view / controller:
//   setting('nama_toko')               → "Toko Sembako Berkah"
//   setting('pajak_persen', 11)        → 11
//   format_rupiah(65000)               → "Rp 65.000"
//   format_rupiah(65000, false)        → "65.000"
//   hitung_pajak(100000)               → 11000
//   hitung_total(100000, 5000)         → 105.050  (subtotal-diskon + pajak)

use App\Models\PengaturanModel;

if (!function_exists('setting')) {
    /**
     * Ambil nilai pengaturan toko berdasarkan key.
     * Menggunakan static cache agar hanya 1x query per request.
     */
    function setting(string $key, $default = null)
    {
        static $cache = null;

        if ($cache === null) {
            try {
                $model = new PengaturanModel();
                $cache = $model->getAll();
            } catch (\Throwable $e) {
                $cache = [];
            }
        }

        return $cache[$key] ?? $default;
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format angka ke format Rupiah.
     * Symbol diambil dari pengaturan 'mata_uang' (default: Rp).
     *
     * @param float $angka
     * @param bool  $withSymbol  true = "Rp 65.000", false = "65.000"
     */
    function format_rupiah(float $angka, bool $withSymbol = true): string
    {
        $symbol = $withSymbol ? (setting('mata_uang', 'Rp') . ' ') : '';
        return $symbol . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('hitung_pajak')) {
    /**
     * Hitung pajak (PPN) dari subtotal.
     * Persentase diambil dari pengaturan 'pajak_persen' (default: 11).
     */
    function hitung_pajak(float $subtotal): float
    {
        $persen = (float) setting('pajak_persen', 11);
        if ($persen <= 0) return 0;
        return round($subtotal * $persen / 100);
    }
}

if (!function_exists('hitung_total')) {
    /**
     * Hitung total akhir: (subtotal - diskon) + pajak.
     */
    function hitung_total(float $subtotal, float $diskon = 0): float
    {
        $setelahDiskon = max(0, $subtotal - $diskon);
        return $setelahDiskon + hitung_pajak($setelahDiskon);
    }
}

if (!function_exists('nama_toko')) {
    function nama_toko(): string
    {
        return setting('nama_toko', 'Toko Sembako');
    }
}

if (!function_exists('pajak_persen')) {
    function pajak_persen(): float
    {
        return (float) setting('pajak_persen', 11);
    }
}
