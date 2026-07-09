<?php
// app/Controllers/BaseController.php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\PengaturanModel;
use App\Models\ProdukModel;
use App\Models\HutangModel;

abstract class BaseController extends Controller
{
    // Helper 'pengaturan' otomatis tersedia di semua controller turunan
    protected $helpers = ['url', 'form', 'pengaturan'];

    // Data pengaturan toko, bisa diakses via $this->toko di semua controller
    protected array $toko = [];

    public function initController(
        RequestInterface  $request,
        ResponseInterface $response,
        LoggerInterface   $logger
    ): void {
        parent::initController($request, $response, $logger);

        if (!session()->get('logged_in')) {
            return;
        }

        $view = service('renderer');

        // ── 1. Inject pengaturan toko ke semua view ─────────────────
        try {
            $this->toko = (new PengaturanModel())->getAll();
        } catch (\Throwable $e) {
            $this->toko = [];
        }
        $view->setVar('toko', $this->toko);

        // ── 2. Notifikasi global untuk sidebar ───────────────────────
        $notifStok   = 0;
        $notifHutang = 0;

        try { $notifStok   = count((new ProdukModel())->stokRendah()); }   catch (\Throwable $e) {}
        try { $notifHutang = (new HutangModel())->jumlahHutangJatuhTempo(); } catch (\Throwable $e) {}

        $view->setVar('notif_stok',   $notifStok);
        $view->setVar('notif_hutang', $notifHutang);
    }
}
