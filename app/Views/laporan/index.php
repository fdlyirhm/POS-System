<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
  <?php
  $menus = [
    ['icon' => 'fa-calendar-day', 'title' => 'Laporan Harian',  'sub' => 'Ringkasan penjualan per hari', 'url' => '/laporan/harian',  'color' => '#1D9E75'],
    ['icon' => 'fa-calendar-alt', 'title' => 'Laporan Bulanan', 'sub' => 'Grafik & analisis bulanan',    'url' => '/laporan/bulanan', 'color' => '#378ADD'],
    ['icon' => 'fa-fire',         'title' => 'Produk Terlaris', 'sub' => 'Ranking produk terjual',       'url' => '/laporan/produk',  'color' => '#e67e22'],
  ];
  ?>
  <?php foreach ($menus as $m): ?>
  <a href="<?= $m['url'] ?>" style="text-decoration:none">
    <div class="card" style="text-align:center;padding:32px 20px;cursor:pointer;transition:.15s"
         onmouseover="this.style.borderColor='<?= $m['color'] ?>'"
         onmouseout="this.style.borderColor='#e9ecef'">
      <div style="width:56px;height:56px;border-radius:14px;background:<?= $m['color'] ?>22;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
        <i class="fas <?= $m['icon'] ?>" style="font-size:24px;color:<?= $m['color'] ?>"></i>
      </div>
      <div style="font-size:15px;font-weight:700;color:#343a40;margin-bottom:6px"><?= $m['title'] ?></div>
      <div style="font-size:13px;color:#6c757d"><?= $m['sub'] ?></div>
    </div>
  </a>
  <?php endforeach; ?>
</div>

<?= $this->endSection() ?>
