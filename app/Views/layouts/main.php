<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?? 'Toko Sembako' ?> — <?= esc($toko['nama_toko'] ?? 'SIP Toko Sembako') ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root{
    --green:#1D9E75;--green-light:#E1F5EE;--green-dark:#0F6E56;
    --gray-100:#f8f9fa;--gray-200:#e9ecef;--gray-400:#ced4da;
    --gray-600:#6c757d;--gray-800:#343a40;
    --red:#dc3545;--sidebar-w:242px;
  }
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Segoe UI',sans-serif;background:#f4f6f8;color:var(--gray-800);display:flex;min-height:100vh}

  /* Sidebar */
  .sidebar{width:var(--sidebar-w);background:#fff;border-right:1px solid var(--gray-200);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;overflow-y:auto}
  .sidebar-logo{padding:15px 16px;border-bottom:1px solid var(--gray-200);display:flex;align-items:center;gap:10px;flex-shrink:0}
  .logo-icon{width:36px;height:36px;background:var(--green);border-radius:9px;display:flex;align-items:center;justify-content:center;color:white;font-size:16px;flex-shrink:0}
  .logo-text{font-size:12px;font-weight:700;color:var(--gray-800);line-height:1.3}
  .logo-sub{font-size:10px;color:var(--gray-600);font-weight:400}
  .sidebar-nav{padding:10px 8px;flex:1}
  .nav-label{font-size:10px;font-weight:600;color:var(--gray-600);text-transform:uppercase;letter-spacing:.6px;padding:8px 10px 4px}
  .nav-link{display:flex;align-items:center;gap:9px;padding:8px 11px;border-radius:8px;color:var(--gray-600);text-decoration:none;font-size:13px;margin-bottom:1px;transition:all .15s;position:relative}
  .nav-link:hover{background:var(--gray-100);color:var(--gray-800)}
  .nav-link.active{background:var(--green-light);color:var(--green-dark);font-weight:600}
  .nav-link i{width:16px;text-align:center;font-size:13px;flex-shrink:0}
  .nav-badge{margin-left:auto;background:var(--red);color:white;font-size:10px;font-weight:700;padding:1px 6px;border-radius:10px;line-height:1.6}
  .sidebar-user{padding:11px 14px;border-top:1px solid var(--gray-200);display:flex;align-items:center;gap:10px;flex-shrink:0}
  .user-avatar{width:30px;height:30px;border-radius:50%;background:var(--green-light);color:var(--green-dark);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0}

  /* Main */
  .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh}
  .topbar{background:#fff;border-bottom:1px solid var(--gray-200);padding:11px 22px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50}
  .topbar-title{font-size:15px;font-weight:700}
  .breadcrumb{font-size:11px;color:var(--gray-600);margin-top:2px}
  .page-content{padding:20px;flex:1}

  /* Cards */
  .card{background:#fff;border-radius:12px;border:1px solid var(--gray-200);padding:18px;margin-bottom:14px}
  .card-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
  .card-title{font-size:14px;font-weight:600}
  .metric-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px;margin-bottom:18px}
  .metric-card{background:var(--gray-100);border-radius:10px;padding:14px}
  .metric-label{font-size:12px;color:var(--gray-600);margin-bottom:5px}
  .metric-value{font-size:22px;font-weight:700;color:var(--gray-800)}
  .metric-sub{font-size:11px;margin-top:3px}
  .text-success{color:var(--green-dark)}
  .text-danger{color:var(--red)}

  /* Table */
  .table-wrapper{overflow-x:auto}
  table{width:100%;border-collapse:collapse;font-size:13px}
  thead th{background:var(--gray-100);padding:9px 11px;text-align:left;font-weight:600;font-size:11px;color:var(--gray-600);border-bottom:1px solid var(--gray-200);white-space:nowrap}
  tbody td{padding:9px 11px;border-bottom:1px solid var(--gray-200);vertical-align:middle}
  tbody tr:hover{background:var(--gray-100)}
  tbody tr:last-child td{border-bottom:none}
  tfoot td{padding:9px 11px}

  /* Badges */
  .badge{display:inline-flex;align-items:center;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600;white-space:nowrap}
  .badge-success{background:#d1fae5;color:#065f46}
  .badge-warning{background:#fef3c7;color:#92400e}
  .badge-danger{background:#fee2e2;color:#991b1b}
  .badge-info{background:#dbeafe;color:#1e40af}

  /* Buttons */
  .btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;text-decoration:none;border:1px solid transparent;transition:all .15s}
  .btn-primary{background:var(--green);color:white;border-color:var(--green)}
  .btn-primary:hover{background:var(--green-dark)}
  .btn-secondary{background:#fff;color:var(--gray-800);border-color:var(--gray-400)}
  .btn-secondary:hover{background:var(--gray-100)}
  .btn-danger{background:var(--red);color:white;border-color:var(--red)}
  .btn-danger:hover{background:#b91c1c}
  .btn-sm{padding:4px 10px;font-size:12px}

  /* Forms */
  .form-group{margin-bottom:14px}
  .form-label{display:block;font-size:13px;font-weight:500;margin-bottom:4px}
  .form-control{width:100%;padding:8px 11px;border:1px solid var(--gray-400);border-radius:8px;font-size:13px;color:var(--gray-800);outline:none;transition:border .15s;background:#fff}
  .form-control:focus{border-color:var(--green)}
  select.form-control{cursor:pointer}
  textarea.form-control{resize:vertical}
  .form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}

  /* Alert */
  .alert{padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:13px}
  .alert-success{background:var(--green-light);color:var(--green-dark);border:1px solid #6ee7b7}
  .alert-danger{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
  .alert-warning{background:#fef3c7;color:#92400e;border:1px solid #fcd34d}
</style>
</head>
<body>

<?php
  $uri = uri_string();
  function navActive(string $uri, string ...$checks): string {
    foreach ($checks as $c) {
      if ($c === $uri || str_starts_with($uri, $c)) return 'active';
    }
    return '';
  }
  $notifStok   = $notif_stok  ?? 0;
  $notifHutang = $notif_hutang ?? 0;
  $namaTokoNav = $toko['nama_toko'] ?? 'Toko Sembako';
?>

<nav class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon"><i class="fas fa-store"></i></div>
    <div>
      <div class="logo-text"><?= esc($namaTokoNav) ?></div>
      <div class="logo-sub">Sistem Informasi Penjualan</div>
    </div>
  </div>

  <div class="sidebar-nav">
    <div class="nav-label">Menu Utama</div>
    <a href="/dashboard"     class="nav-link <?= navActive($uri,'dashboard') ?>"><i class="fas fa-chart-pie"></i> Dashboard</a>
    <a href="/transaksi/pos" class="nav-link <?= navActive($uri,'transaksi/pos') ?>"><i class="fas fa-cash-register"></i> Kasir POS</a>
    <a href="/transaksi"     class="nav-link <?= $uri==='transaksi'?'active':'' ?>"><i class="fas fa-receipt"></i> Transaksi</a>
    <a href="/produk"        class="nav-link <?= navActive($uri,'produk') ?>">
      <i class="fas fa-box"></i> Produk
      <?php if ($notifStok > 0): ?><span class="nav-badge"><?= $notifStok ?></span><?php endif; ?>
    </a>
    <a href="/hutang"        class="nav-link <?= navActive($uri,'hutang') ?>">
      <i class="fas fa-file-invoice-dollar"></i> Hutang Pelanggan
      <?php if ($notifHutang > 0): ?><span class="nav-badge"><?= $notifHutang ?></span><?php endif; ?>
    </a>

    <?php if (session()->get('role') === 'admin'): ?>
    <div class="nav-label" style="margin-top:6px">Gudang & Stok</div>
    <a href="/stok-opname" class="nav-link <?= navActive($uri,'stok-opname') ?>"><i class="fas fa-clipboard-list"></i> Stok Opname</a>
    <a href="/pembelian"   class="nav-link <?= navActive($uri,'pembelian') ?>"><i class="fas fa-shopping-basket"></i> Pembelian / Restock</a>

    <div class="nav-label" style="margin-top:6px">Laporan</div>
    <a href="/laporan"         class="nav-link <?= $uri==='laporan'?'active':'' ?>"><i class="fas fa-chart-bar"></i> Laporan</a>
    <a href="/laporan/harian"  class="nav-link <?= navActive($uri,'laporan/harian') ?>"><i class="fas fa-calendar-day"></i> Laporan Harian</a>
    <a href="/laporan/bulanan" class="nav-link <?= navActive($uri,'laporan/bulanan') ?>"><i class="fas fa-calendar-alt"></i> Laporan Bulanan</a>
    <a href="/laporan/produk"  class="nav-link <?= navActive($uri,'laporan/produk') ?>"><i class="fas fa-fire"></i> Produk Terlaris</a>

    <div class="nav-label" style="margin-top:6px">Master Data</div>
    <a href="/supplier"   class="nav-link <?= navActive($uri,'supplier') ?>"><i class="fas fa-truck"></i> Supplier</a>
    <a href="/kategori"   class="nav-link <?= navActive($uri,'kategori') ?>"><i class="fas fa-tags"></i> Kategori</a>
    <a href="/diskon"     class="nav-link <?= navActive($uri,'diskon') ?>"><i class="fas fa-percent"></i> Diskon & Promo</a>
    <a href="/pengguna"   class="nav-link <?= navActive($uri,'pengguna') ?>"><i class="fas fa-users"></i> Pengguna</a>
    <a href="/pengaturan" class="nav-link <?= navActive($uri,'pengaturan') ?>"><i class="fas fa-cog"></i> Pengaturan</a>
    <?php endif; ?>
  </div>

  <div class="sidebar-user">
    <div class="user-avatar"><?= strtoupper(substr(session()->get('nama') ?? 'U', 0, 2)) ?></div>
    <div style="flex:1;min-width:0">
      <div style="font-size:12px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= esc(session()->get('nama')) ?></div>
      <div style="font-size:10px;color:var(--gray-600)"><?= ucfirst(session()->get('role') ?? '') ?></div>
    </div>
    <a href="/logout" title="Logout" style="color:var(--gray-600);flex-shrink:0"><i class="fas fa-sign-out-alt"></i></a>
  </div>
</nav>

<div class="main">
  <div class="topbar">
    <div>
      <div class="topbar-title"><?= esc($title ?? '') ?></div>
      <div class="breadcrumb"><?= esc($namaTokoNav) ?> / <?= esc($title ?? '') ?></div>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
      <?php if (isset($toko['buka_jam'], $toko['tutup_jam'])): ?>
      <span style="font-size:11px;color:var(--gray-600)"><i class="fas fa-clock"></i> <?= $toko['buka_jam'] ?>–<?= $toko['tutup_jam'] ?></span>
      <?php endif; ?>
      <span style="font-size:11px;color:var(--gray-600)"><?= date('d M Y, H:i') ?></span>
      <a href="/transaksi/pos" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Transaksi Baru</a>
    </div>
  </div>

  <div class="page-content">
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('warning') ?></div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
  </div>
</div>

</body>
</html>
