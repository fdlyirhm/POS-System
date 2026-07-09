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

  h2.section { font-size: 13px; color: #0F6E56; border-bottom: 1px solid #cdeede; padding-bottom: 4px; margin: 18px 0 10px; }

  .chart-wrap { width: 100%; border:1px solid #e2e2e2; border-radius:6px; padding: 12px; background:#fff; margin-bottom:16px; }
  .bar-row { width: 100%; margin-bottom: 7px; }
  .bar-label { display:inline-block; width: 28%; font-size:9px; color:#333; font-weight:bold; }
  .bar-track { display:inline-block; width: 50%; background:#f0f0f0; height: 12px; border-radius: 4px; vertical-align:middle; }
  .bar-fill { height: 12px; border-radius:4px; }
  .bar-val { display:inline-block; width: 18%; font-size:9px; color:#444; padding-left:6px; }

  table { width:100%; border-collapse: collapse; font-size: 10px; margin-top: 6px; }
  th { background:#e9f8f0; color:#0F6E56; padding: 6px 8px; text-align:left; border-bottom: 1px solid #cdeede; }
  td { padding: 6px 8px; border-bottom: 1px solid #eee; }
  tr:nth-child(even) td { background: #fafafa; }
  .text-right { text-align:right; }
  .text-center { text-align:center; }
  .rank-badge { display:inline-block; width:18px; height:18px; border-radius:50%; color:#fff; font-size:9px; text-align:center; line-height:18px; font-weight:bold; }
  .rank-1 { background:#f5a623; }
  .rank-2 { background:#a0a0a0; }
  .rank-3 { background:#cd7f32; }
  .rank-n { background:#1D9E75; }

  .footer { margin-top:20px; text-align:center; font-size:9px; color:#999; border-top:1px solid #eee; padding-top:8px; }
</style>
</head>
<body>

  <div class="header">
    <h1><?= esc($toko['nama_toko'] ?? 'Toko Sembako') ?></h1>
    <div>Laporan Produk Terlaris</div>
  </div>
  <div class="meta">
    Periode: <?= date('d M Y', strtotime($dari)) ?> &mdash; <?= date('d M Y', strtotime($sampai)) ?> &nbsp;|&nbsp;
    Dicetak: <?= date('d/m/Y H:i') ?>
  </div>

  <!-- GRAFIK HORIZONTAL TOP 10 -->
  <h2 class="section">Grafik Top 10 Produk Terlaris</h2>
  <div class="chart-wrap">
    <?php
      $top10 = array_slice($produk, 0, 10);
      $maxQty = !empty($top10) ? max(array_column($top10, 'total_qty')) : 1;
      $maxQty = $maxQty ?: 1;
      $warna = ['#1D9E75','#378ADD','#e67e22','#9b59b6','#e74c3c','#16a085','#f39c12','#2980b9','#27ae60','#8e44ad'];
      foreach ($top10 as $i => $p):
        $pct = round(($p['total_qty'] / $maxQty) * 100);
    ?>
    <div class="bar-row">
      <span class="bar-label"><?= esc($p['nama']) ?></span>
      <span class="bar-track"><span class="bar-fill" style="width:<?= max($pct,2) ?>%; background:<?= $warna[$i % count($warna)] ?>"></span></span>
      <span class="bar-val"><?= number_format($p['total_qty']) ?> <?= $p['satuan'] ?></span>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- TABEL RANKING -->
  <h2 class="section">Tabel Ranking Lengkap</h2>
  <table>
    <thead>
      <tr>
        <th class="text-center">#</th><th>Nama Produk</th><th>Kategori</th>
        <th class="text-right">Qty Terjual</th><th class="text-right">Total Penjualan</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($produk as $i => $p): ?>
      <tr>
        <td class="text-center">
          <span class="rank-badge rank-<?= $i < 3 ? ($i+1) : 'n' ?>"><?= $i+1 ?></span>
        </td>
        <td><?= esc($p['nama']) ?></td>
        <td><?= esc($p['nama_kategori'] ?? '-') ?></td>
        <td class="text-right"><?= number_format($p['total_qty']) ?> <?= $p['satuan'] ?></td>
        <td class="text-right">Rp <?= number_format($p['total_penjualan'], 0, ',', '.') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr style="background:#e9f8f0; font-weight:bold">
        <td colspan="3" class="text-right">Total Keseluruhan</td>
        <td class="text-right"><?= number_format(array_sum(array_column($produk,'total_qty'))) ?></td>
        <td class="text-right">Rp <?= number_format(array_sum(array_column($produk,'total_penjualan')), 0, ',', '.') ?></td>
      </tr>
    </tfoot>
  </table>

  <div class="footer">
    <?= esc($toko['alamat'] ?? '') ?> &nbsp;|&nbsp; Telp: <?= esc($toko['telepon'] ?? '-') ?><br>
    Dokumen ini dibuat otomatis oleh Sistem Informasi Penjualan Toko Sembako
  </div>

</body>
</html>
