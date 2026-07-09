<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 28px 32px; }
  body { font-family: Arial, sans-serif; font-size: 11px; color: #1a1a1a; }
  .header { text-align:center; border-bottom: 2px solid #1D9E75; padding-bottom: 10px; margin-bottom: 14px; }
  .header h1 { font-size: 18px; color: #0F6E56; margin: 0 0 2px; }
  .header .sub { font-size: 11px; color: #666; }
  .meta { font-size: 10px; color: #888; text-align:center; margin-bottom: 16px; }

  .metric-row { width: 100%; margin-bottom: 16px; }
  .metric-box { display:inline-block; width: 23%; border: 1px solid #d8e8e0; border-radius: 6px; padding: 8px; margin-right: 1%; background:#f7fdfa; }
  .metric-label { font-size: 9px; color: #666; margin-bottom: 4px; }
  .metric-value { font-size: 14px; font-weight: bold; color: #0F6E56; }

  h2.section { font-size: 13px; color: #0F6E56; border-bottom: 1px solid #cdeede; padding-bottom: 4px; margin: 18px 0 10px; }

  /* ===== GRAFIK BATANG CSS (tanpa JS, kompatibel Dompdf) ===== */
  .chart-wrap { width: 100%; border:1px solid #e2e2e2; border-radius:6px; padding: 10px; background:#fff; }
  .chart-bars { width: 100%; }
  .bar-row { width: 100%; margin-bottom: 3px; }
  .bar-label { display:inline-block; width: 9%; font-size:8px; color:#555; text-align:right; padding-right:4px; }
  .bar-track { display:inline-block; width: 78%; background:#f0f0f0; height: 10px; border-radius: 3px; vertical-align:middle; }
  .bar-fill { height: 10px; background: linear-gradient(to right,#1D9E75,#5DCAA5); border-radius:3px; }
  .bar-val { display:inline-block; width: 12%; font-size:8px; color:#444; padding-left:4px; }

  table { width:100%; border-collapse: collapse; font-size: 10px; margin-top: 6px; }
  th { background:#e9f8f0; color:#0F6E56; padding: 6px 8px; text-align:left; border-bottom: 1px solid #cdeede; }
  td { padding: 5px 8px; border-bottom: 1px solid #eee; }
  tr:nth-child(even) td { background: #fafafa; }
  .text-right { text-align:right; }
  .badge { padding:2px 6px; border-radius:4px; font-size:9px; color:#fff; }
  .badge-lunas { background:#1D9E75; }
  .badge-kredit { background:#e6a23c; }
  .badge-batal { background:#cc4444; }

  .footer { margin-top:20px; text-align:center; font-size:9px; color:#999; border-top:1px solid #eee; padding-top:8px; }
</style>
</head>
<body>

  <div class="header">
    <h1><?= esc($toko['nama_toko'] ?? 'Toko Sembako') ?></h1>
    <div class="sub">Laporan Penjualan Harian</div>
  </div>
  <div class="meta">
    Periode: <?= date('d F Y', strtotime($tanggal)) ?> &nbsp;|&nbsp;
    Dicetak: <?= date('d/m/Y H:i') ?> &nbsp;|&nbsp;
    PPN: <?= $pajak_persen ?>%
  </div>

  <!-- METRIK -->
  <div class="metric-row">
    <div class="metric-box">
      <div class="metric-label">TOTAL PENJUALAN</div>
      <div class="metric-value">Rp <?= number_format($ringkasan['total_penjualan'] ?? 0, 0, ',', '.') ?></div>
    </div>
    <div class="metric-box">
      <div class="metric-label">JUMLAH TRANSAKSI</div>
      <div class="metric-value"><?= $ringkasan['jumlah_transaksi'] ?? 0 ?></div>
    </div>
    <div class="metric-box">
      <div class="metric-label">TOTAL DISKON</div>
      <div class="metric-value">Rp <?= number_format($ringkasan['total_diskon'] ?? 0, 0, ',', '.') ?></div>
    </div>
    <div class="metric-box">
      <div class="metric-label">PPN TERKUMPUL</div>
      <div class="metric-value">Rp <?= number_format($ringkasan['total_pajak'] ?? 0, 0, ',', '.') ?></div>
    </div>
  </div>

  <!-- GRAFIK PER JAM -->
  <h2 class="section">Grafik Penjualan per Jam</h2>
  <div class="chart-wrap">
    <div class="chart-bars">
      <?php
        $maxJam = max(array_column($perJam, 'total')) ?: 1;
        foreach ($perJam as $j):
          if ($j['total'] == 0 && ($j['jam'] < 6 || $j['jam'] > 22)) continue; // skip jam sepi non-operasional
          $pct = round(($j['total'] / $maxJam) * 100);
      ?>
      <div class="bar-row">
        <span class="bar-label"><?= str_pad($j['jam'], 2, '0', STR_PAD_LEFT) ?>:00</span>
        <span class="bar-track"><span class="bar-fill" style="width:<?= max($pct,1) ?>%"></span></span>
        <span class="bar-val">Rp <?= number_format($j['total'], 0, ',', '.') ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- TABEL TRANSAKSI -->
  <h2 class="section">Detail Transaksi</h2>
  <table>
    <thead>
      <tr>
        <th>No. Transaksi</th><th>Waktu</th><th>Pelanggan</th><th>Kasir</th>
        <th class="text-right">Total</th><th>Metode</th><th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($transaksi as $t): ?>
      <tr>
        <td><?= esc($t['no_transaksi']) ?></td>
        <td><?= date('H:i', strtotime($t['tanggal'])) ?></td>
        <td><?= esc($t['pelanggan']) ?></td>
        <td><?= esc($t['nama_kasir']) ?></td>
        <td class="text-right">Rp <?= number_format($t['total'], 0, ',', '.') ?></td>
        <td><?= ucfirst($t['metode_bayar']) ?></td>
        <td>
          <span class="badge badge-<?= $t['status']==='lunas'?'lunas':($t['status']==='kredit'?'kredit':'batal') ?>">
            <?= ucfirst($t['status']) ?>
          </span>
        </td>
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
