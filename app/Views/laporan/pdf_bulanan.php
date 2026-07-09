<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 28px 32px; }
  body { font-family: Arial, sans-serif; font-size: 11px; color: #1a1a1a; }
  .header { text-align:center; border-bottom: 2px solid #1D9E75; padding-bottom: 10px; margin-bottom: 14px; }
  .header h1 { font-size: 18px; color: #0F6E56; margin: 0 0 2px; }
  .meta { font-size: 10px; color: #888; text-align:center; margin-bottom: 16px; }

  .metric-row { width: 100%; margin-bottom: 16px; }
  .metric-box { display:inline-block; width: 31%; border: 1px solid #d8e8e0; border-radius: 6px; padding: 8px; margin-right: 1%; background:#f7fdfa; }
  .metric-label { font-size: 9px; color: #666; margin-bottom: 4px; }
  .metric-value { font-size: 14px; font-weight: bold; color: #0F6E56; }

  h2.section { font-size: 13px; color: #0F6E56; border-bottom: 1px solid #cdeede; padding-bottom: 4px; margin: 18px 0 10px; }

  .chart-wrap { width: 100%; border:1px solid #e2e2e2; border-radius:6px; padding: 10px; background:#fff; }
  .bar-row { width: 100%; margin-bottom: 3px; }
  .bar-label { display:inline-block; width: 12%; font-size:8px; color:#555; }
  .bar-track { display:inline-block; width: 65%; background:#f0f0f0; height: 9px; border-radius: 3px; vertical-align:middle; }
  .bar-fill { height: 9px; background: linear-gradient(to right,#1D9E75,#5DCAA5); border-radius:3px; }
  .bar-val { display:inline-block; width: 20%; font-size:8px; color:#444; padding-left:4px; }

  /* kategori horizontal bar dengan persen */
  .kat-row { margin-bottom: 8px; }
  .kat-name { font-size:10px; font-weight:bold; color:#333; }
  .kat-track { background:#f0f0f0; height: 12px; border-radius: 4px; margin-top:2px; }
  .kat-fill { height: 12px; border-radius:4px; text-align:right; padding-right:4px; color:#fff; font-size:8px; line-height:12px; }

  table { width:100%; border-collapse: collapse; font-size: 10px; margin-top: 6px; }
  th { background:#e9f8f0; color:#0F6E56; padding: 6px 8px; text-align:left; border-bottom: 1px solid #cdeede; }
  td { padding: 5px 8px; border-bottom: 1px solid #eee; }
  tr:nth-child(even) td { background: #fafafa; }
  .text-right { text-align:right; }

  .footer { margin-top:20px; text-align:center; font-size:9px; color:#999; border-top:1px solid #eee; padding-top:8px; }
</style>
</head>
<body>

  <div class="header">
    <h1><?= esc($toko['nama_toko'] ?? 'Toko Sembako') ?></h1>
    <div>Laporan Penjualan Bulanan</div>
  </div>
  <div class="meta">
    Periode: <?= date('F Y', strtotime($bulan.'-01')) ?> &nbsp;|&nbsp; Dicetak: <?= date('d/m/Y H:i') ?>
  </div>

  <div class="metric-row">
    <div class="metric-box">
      <div class="metric-label">TOTAL PENJUALAN</div>
      <div class="metric-value">Rp <?= number_format($ringkasan['total_penjualan'] ?? 0, 0, ',', '.') ?></div>
    </div>
    <div class="metric-box">
      <div class="metric-label">JUMLAH TRANSAKSI</div>
      <div class="metric-value"><?= number_format($ringkasan['jumlah_transaksi'] ?? 0) ?></div>
    </div>
    <div class="metric-box">
      <div class="metric-label">RATA-RATA / TRANSAKSI</div>
      <div class="metric-value">
        Rp <?= $ringkasan['jumlah_transaksi'] > 0 ? number_format(($ringkasan['total_penjualan'] ?? 0)/$ringkasan['jumlah_transaksi'],0,',','.') : '0' ?>
      </div>
    </div>
  </div>

  <!-- GRAFIK HARIAN -->
  <h2 class="section">Grafik Penjualan Harian</h2>
  <div class="chart-wrap">
    <?php
      $maxHari = !empty($grafik) ? max(array_column($grafik, 'total')) : 1;
      $maxHari = $maxHari ?: 1;
      foreach ($grafik as $g):
        $pct = round(($g['total'] / $maxHari) * 100);
    ?>
    <div class="bar-row">
      <span class="bar-label"><?= date('d M', strtotime($g['tanggal'])) ?></span>
      <span class="bar-track"><span class="bar-fill" style="width:<?= max($pct,1) ?>%"></span></span>
      <span class="bar-val">Rp <?= number_format($g['total'], 0, ',', '.') ?></span>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- PENJUALAN PER KATEGORI -->
  <h2 class="section">Penjualan per Kategori</h2>
  <?php
    $totalKat = array_sum(array_column($perKategori, 'total')) ?: 1;
    $warna = ['#1D9E75','#378ADD','#e67e22','#9b59b6','#e74c3c','#16a085'];
  ?>
  <?php foreach ($perKategori as $i => $k): $pct = round(($k['total']/$totalKat)*100); ?>
  <div class="kat-row">
    <div class="kat-name"><?= esc($k['kategori']) ?> &nbsp; <span style="color:#888;font-weight:normal">(<?= number_format($k['total_qty']) ?> item)</span></div>
    <div class="kat-track">
      <div class="kat-fill" style="width:<?= max($pct,3) ?>%; background:<?= $warna[$i % count($warna)] ?>"><?= $pct ?>%</div>
    </div>
  </div>
  <?php endforeach; ?>

  <table style="margin-top:14px">
    <thead>
      <tr><th>Kategori</th><th class="text-right">Item Terjual</th><th class="text-right">Total Penjualan</th><th class="text-right">Kontribusi</th></tr>
    </thead>
    <tbody>
      <?php foreach ($perKategori as $k): $pct = round(($k['total']/$totalKat)*100,1); ?>
      <tr>
        <td><?= esc($k['kategori']) ?></td>
        <td class="text-right"><?= number_format($k['total_qty']) ?></td>
        <td class="text-right">Rp <?= number_format($k['total'], 0, ',', '.') ?></td>
        <td class="text-right"><?= $pct ?>%</td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="footer">
    <?= esc($toko['alamat'] ?? '') ?> &nbsp;|&nbsp; Telp: <?= esc($toko['telepon'] ?? '-') ?><br>
    Dokumen ini dibuat otomatis oleh Sistem Informasi Penjualan Toko Sembako
  </div>

</body>
</html>
