<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="margin-bottom:16px">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-chart-bar" style="color:var(--green)"></i> Laporan Bulanan</div>
    <div style="display:flex;gap:8px">
    <a href="/laporan/export-pdf/bulanan?bulan=<?= $bulan ?>" target="_blank" class="btn btn-secondary btn-sm">
      <i class="fas fa-file-pdf" style="color:#dc3545"></i> Export PDF
    </a>
    <a href="/laporan/export/csv?dari=<?= date('Y-m-01', strtotime($bulan.'-01')) ?>&sampai=<?= date('Y-m-t', strtotime($bulan.'-01')) ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-download"></i> Export CSV
    </a>
  </div>
  </div>
  <form method="GET" style="display:flex;gap:10px;align-items:center">
    <label class="form-label" style="margin:0">Pilih Bulan:</label>
    <input type="month" name="bulan" class="form-control" value="<?= $bulan ?>" style="width:200px">
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
  </form>
</div>

<!-- Ringkasan -->
<div class="metric-grid" style="grid-template-columns:repeat(3,1fr)">
  <div class="metric-card">
    <div class="metric-label">Total Penjualan</div>
    <div class="metric-value">Rp <?= number_format($ringkasan['total_penjualan'] ?? 0, 0, ',', '.') ?></div>
    <div class="metric-sub text-success">Periode <?= date('M Y', strtotime($bulan.'-01')) ?></div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Jumlah Transaksi</div>
    <div class="metric-value"><?= number_format($ringkasan['jumlah_transaksi'] ?? 0) ?></div>
    <div class="metric-sub" style="color:#6c757d">transaksi selesai</div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Rata-rata per Transaksi</div>
    <div class="metric-value">
      Rp <?= $ringkasan['jumlah_transaksi'] > 0 ? number_format(($ringkasan['total_penjualan'] ?? 0) / $ringkasan['jumlah_transaksi'], 0, ',', '.') : '0' ?>
    </div>
    <div class="metric-sub" style="color:#6c757d">per transaksi</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
  <!-- Grafik Harian -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Grafik Penjualan Harian</div>
    </div>
    <canvas id="grafikHarian" height="220"></canvas>
  </div>

  <!-- Per Kategori -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Penjualan per Kategori</div>
    </div>
    <?php if (!empty($perKategori)): ?>
    <?php $maxKat = max(array_column($perKategori, 'total')); ?>
    <?php foreach ($perKategori as $k): ?>
    <div style="margin-bottom:12px">
      <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
        <span><?= esc($k['kategori']) ?></span>
        <span style="font-weight:600">Rp <?= number_format($k['total'],0,',','.') ?></span>
      </div>
      <div style="background:#f0f0f0;border-radius:4px;height:10px">
        <div style="height:10px;border-radius:4px;background:#1D9E75;width:<?= $maxKat > 0 ? round(($k['total']/$maxKat)*100) : 0 ?>%"></div>
      </div>
      <div style="font-size:11px;color:#6c757d;margin-top:2px"><?= number_format($k['total_qty']) ?> item terjual</div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <p style="color:#6c757d;text-align:center;padding:20px;font-size:13px">Belum ada data untuk periode ini</p>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const grafikData = <?= json_encode($grafik) ?>;
const labels  = grafikData.map(d => {
  const dt = new Date(d.tanggal);
  return dt.getDate() + '/' + (dt.getMonth()+1);
});
const values = grafikData.map(d => parseFloat(d.total));

new Chart(document.getElementById('grafikHarian'), {
  type: 'bar',
  data: {
    labels,
    datasets: [{
      label: 'Penjualan (Rp)',
      data: values,
      backgroundColor: '#1D9E75',
      borderRadius: 5,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => 'Rp ' + ctx.raw.toLocaleString('id')
        }
      }
    },
    scales: {
      y: {
        ticks: {
          callback: v => 'Rp ' + (v/1000).toFixed(0) + 'rb'
        },
        grid: { color: '#f0f0f0' }
      },
      x: { grid: { display: false } }
    }
  }
});
</script>

<?= $this->endSection() ?>
