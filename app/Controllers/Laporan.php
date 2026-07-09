<?php
// =============================================
// app/Controllers/Laporan.php
// Terintegrasi pengaturan: nama toko, pajak dinamis di export
// =============================================
namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\PengaturanModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Laporan extends BaseController
{
    protected $transaksiModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->transaksiModel  = new TransaksiModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        return view('laporan/index', ['title' => 'Laporan']);
    }

    public function harian()
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        return view('laporan/harian', [
            'title'      => 'Laporan Harian',
            'tanggal'    => $tanggal,
            'ringkasan'  => $this->transaksiModel->ringkasanHarian($tanggal),
            'transaksi'  => $this->transaksiModel->getTransaksi(null, null, $tanggal, $tanggal),
            'pajak_persen' => (float) $this->pengaturanModel->ambil('pajak_persen', 11),
        ]);
    }

    public function bulanan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        [$tahun, $bln] = explode('-', $bulan);
        $dari   = "$tahun-$bln-01";
        $sampai = date('Y-m-t', strtotime($dari));

        return view('laporan/bulanan', [
            'title'        => 'Laporan Bulanan',
            'bulan'        => $bulan,
            'ringkasan'    => $this->transaksiModel->ringkasanBulanan($dari, $sampai),
            'perKategori'  => $this->transaksiModel->penjualanPerKategori($dari, $sampai),
            'grafik'       => $this->transaksiModel->grafikBulanan($dari, $sampai),
            'pajak_persen' => (float) $this->pengaturanModel->ambil('pajak_persen', 11),
        ]);
    }

    public function produk()
    {
        $dari   = $this->request->getGet('dari')   ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');

        return view('laporan/produk', [
            'title'       => 'Laporan Produk Terlaris',
            'dari'        => $dari,
            'sampai'      => $sampai,
            'produk'      => $this->transaksiModel->produkTerlaris($dari, $sampai),
            'perKategori' => $this->transaksiModel->penjualanPerKategori($dari, $sampai),
        ]);
    }

    public function export($type)
    {
        $dari   = $this->request->getGet('dari')   ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');
        $data   = $this->transaksiModel->getTransaksi(null, null, $dari, $sampai);
        $toko   = $this->pengaturanModel->getAll();

        if ($type === 'csv') {
            $this->exportCsv($data, $toko, "laporan_{$dari}_{$sampai}.csv");
        }
    }

    private function exportCsv(array $data, array $toko, string $filename): void
    {
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache');

        $output = fopen('php://output', 'w');
        // BOM untuk Excel agar bisa baca UTF-8
        fputs($output, "\xEF\xBB\xBF");

        // Header info toko
        fputcsv($output, ['Laporan Penjualan — ' . ($toko['nama_toko'] ?? 'Toko Sembako')]);
        fputcsv($output, ['Alamat: ' . ($toko['alamat'] ?? '')]);
        fputcsv($output, ['Telp: '   . ($toko['telepon'] ?? '')]);
        fputcsv($output, ['Dicetak: ' . date('d/m/Y H:i')]);
        fputcsv($output, []); // baris kosong

        // Header kolom
        fputcsv($output, ['No. Transaksi','Tanggal','Pelanggan','Kasir','Subtotal','Diskon','PPN','Total','Metode','Status']);

        foreach ($data as $row) {
            fputcsv($output, [
                $row['no_transaksi'],
                date('d/m/Y H:i', strtotime($row['tanggal'])),
                $row['pelanggan'],
                $row['nama_kasir'],
                $row['subtotal'],
                $row['diskon'],
                $row['pajak'],
                $row['total'],
                ucfirst($row['metode_bayar']),
                ucfirst($row['status']),
            ]);
        }

        // Footer total
        fputcsv($output, []);
        fputcsv($output, ['', '', '', 'TOTAL',
            array_sum(array_column($data, 'subtotal')),
            array_sum(array_column($data, 'diskon')),
            array_sum(array_column($data, 'pajak')),
            array_sum(array_column($data, 'total')),
            '', '',
        ]);

        fclose($output);
        exit;
    }
    public function exportPdfHarian()
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        $ringkasan = $this->transaksiModel->ringkasanHarian($tanggal);
        $transaksi = $this->transaksiModel->getTransaksi(null, null, $tanggal, $tanggal);
        $toko      = $this->pengaturanModel->getAll();

        // Data untuk grafik bar per jam
        $perJam = $this->transaksiModel->penjualanPerJam($tanggal); // lihat Model di bawah

        $html = view('laporan/pdf_harian', [
            'tanggal'   => $tanggal,
            'ringkasan' => $ringkasan,
            'transaksi' => $transaksi,
            'toko'      => $toko,
            'perJam'    => $perJam,
            'pajak_persen' => (float) $this->pengaturanModel->ambil('pajak_persen', 11),
        ]);

        return $this->streamPdf($html, 'Laporan-Harian-' . $tanggal . '.pdf');
    }

    // =================================================================
    // EXPORT PDF — LAPORAN BULANAN (grafik harian + pie kategori)
    // =================================================================
    public function exportPdfBulanan()
    {
        $bulan = $this->request->getGet('bulan') ?? date('Y-m');
        [$tahun, $bln] = explode('-', $bulan);
        $dari   = "$tahun-$bln-01";
        $sampai = date('Y-m-t', strtotime($dari));

        $ringkasan   = $this->transaksiModel->ringkasanBulanan($dari, $sampai);
        $grafik      = $this->transaksiModel->grafikBulanan($dari, $sampai);
        $perKategori = $this->transaksiModel->penjualanPerKategori($dari, $sampai);
        $toko        = $this->pengaturanModel->getAll();

        $html = view('laporan/pdf_bulanan', [
            'bulan'       => $bulan,
            'ringkasan'   => $ringkasan,
            'grafik'      => $grafik,
            'perKategori' => $perKategori,
            'toko'        => $toko,
        ]);

        return $this->streamPdf($html, 'Laporan-Bulanan-' . $bulan . '.pdf');
    }

    // =================================================================
    // EXPORT PDF — PRODUK TERLARIS (grafik horizontal bar)
    // =================================================================
    public function exportPdfProduk()
    {
        $dari   = $this->request->getGet('dari')   ?? date('Y-m-01');
        $sampai = $this->request->getGet('sampai') ?? date('Y-m-d');

        $produk = $this->transaksiModel->produkTerlaris($dari, $sampai, 10);
        $toko   = $this->pengaturanModel->getAll();

        $html = view('laporan/pdf_produk', [
            'dari'   => $dari,
            'sampai' => $sampai,
            'produk' => $produk,
            'toko'   => $toko,
        ]);

        return $this->streamPdf($html, 'Laporan-Produk-Terlaris-' . $dari . '_' . $sampai . '.pdf');
    }

    // =================================================================
    // HELPER: render HTML ke PDF stream pakai Dompdf
    // =================================================================
    private function streamPdf(string $html, string $filename)
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);   // izinkan gambar/CSS eksternal jika perlu
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
}