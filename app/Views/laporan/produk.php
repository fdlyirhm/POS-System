<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Filter Periode -->
<div class="card" style="margin-bottom:16px">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-fire" style="color:#e67e22"></i> Laporan Produk Terlaris</div>
    <div style="display:flex;gap:8px">
    <a href="/laporan/export-pdf/produk?dari=<?= $dari ?>&sampai=<?= $sampai ?>" target="_blank" class="btn btn-secondary btn-sm">
      <i class="fas fa-file-pdf" style="color:#dc3545"></i> Export PDF
    </a>
    <a href="/laporan/export/csv?dari=<?= $dari ?>&sampai=<?= $sampai ?>" class="btn btn-secondary btn-sm">
      <i class="fas fa-download"></i> Export CSV
    </a>
</div>
  </div>
  <form method="GET" action="/laporan/produk" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <label class="form-label" style="margin:0;white-space:nowrap">Periode:</label>
    <input type="date" name="dari"   class="form-control" value="<?= $dari ?>"   style="width:160px">
    <span style="color:#6c757d">s/d</span>
    <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>" style="width:160px">
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
    <a href="/laporan" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
  </form>
</div>

<!-- Ringkasan Periode -->
<div class="metric-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
  <div class="metric-card">
    <div class="metric-label">Total Item Terjual</div>
    <div class="metric-value"><?= number_format(array_sum(array_column($produk, 'total_qty'))) ?></div>
    <div class="metric-sub" style="color:#6c757d">unit/pcs/kg</div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Total Penjualan Produk</div>
    <div class="metric-value">Rp <?= number_format(array_sum(array_column($produk, 'total_penjualan')), 0, ',', '.') ?></div>
    <div class="metric-sub" style="color:#6c757d"><?= date('d M Y', strtotime($dari)) ?> — <?= date('d M Y', strtotime($sampai)) ?></div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Jumlah Jenis Produk Terjual</div>
    <div class="metric-value"><?= count($produk) ?></div>
    <div class="metric-sub" style="color:#6c757d">jenis produk berbeda</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

  <!-- Tabel Ranking -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Ranking Produk Terlaris</div>
    </div>

    <?php if (empty($produk)): ?>
    <div style="text-align:center;padding:32px;color:#6c757d">
      <i class="fas fa-inbox" style="font-size:36px;margin-bottom:12px;display:block"></i>
      Tidak ada data penjualan pada periode ini
    </div>
    <?php else: ?>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th style="width:40px;text-align:center">#</th>
            <th>Nama Produk</th>
            <th style="text-align:center">Terjual</th>
            <th style="text-align:right">Total Penjualan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($produk as $i => $p): ?>
          <tr>
            <td style="text-align:center">
              <?php if ($i === 0): ?>
                <span style="color:#f59e0b;font-size:18px">&#9733;</span>
              <?php elseif ($i === 1): ?>
                <span style="color:#94a3b8;font-size:16px">&#9733;</span>
              <?php elseif ($i === 2): ?>
                <span style="color:#cd7c3e;font-size:14px">&#9733;</span>
              <?php else: ?>
                <span style="color:#6c757d;font-size:13px"><?= $i + 1 ?></span>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600;font-size:13px"><?= esc($p['nama']) ?></div>
              <div style="font-size:11px;color:#6c757d"><?= esc($p['nama_kategori']) ?></div>
            </td>
            <td style="text-align:center;font-weight:700;color:#1D9E75">
              <?= number_format($p['total_qty']) ?> <span style="font-size:11px;font-weight:400;color:#6c757d"><?= $p['satuan'] ?></span>
            </td>
            <td style="text-align:right;font-weight:600">
              Rp <?= number_format($p['total_penjualan'], 0, ',', '.') ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <!-- Grafik Bar Horizontal -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Grafik Penjualan (Top 10)</div>
    </div>

    <?php if (empty($produk)): ?>
    <div style="text-align:center;padding:32px;color:#6c757d">Belum ada data</div>
    <?php else: ?>
    <?php
      $top10   = array_slice($produk, 0, 10);
      $maxQty  = max(array_column($top10, 'total_qty'));
      $colors  = ['#1D9E75','#378ADD','#e67e22','#9b59b6','#e74c3c','#1abc9c','#f39c12','#2980b9','#27ae60','#8e44ad'];
    ?>
    <div style="padding:4px 0">
      <?php foreach ($top10 as $i => $p): ?>
      <div style="margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px">
          <span style="font-weight:500;color:#343a40;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= esc($p['nama']) ?>">
            <?= esc($p['nama']) ?>
          </span>
          <span style="color:#6c757d;flex-shrink:0;margin-left:8px">
            <?= number_format($p['total_qty']) ?> <?= $p['satuan'] ?>
          </span>
        </div>
        <div style="background:#f0f0f0;border-radius:6px;height:12px;overflow:hidden">
          <div style="height:12px;border-radius:6px;background:<?= $colors[$i % count($colors)] ?>;width:<?= $maxQty > 0 ? round(($p['total_qty'] / $maxQty) * 100) : 0 ?>%;transition:width .4s ease"></div>
        </div>
        <div style="font-size:11px;color:#6c757d;margin-top:3px;text-align:right">
          Rp <?= number_format($p['total_penjualan'], 0, ',', '.') ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

</div>

<!-- Tabel Lengkap per Kategori -->
<?php if (!empty($perKategori)): ?>
<div class="card" style="margin-top:16px">
  <div class="card-header">
    <div class="card-title">Rekapitulasi per Kategori</div>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Kategori</th>
          <th style="text-align:center">Item Terjual</th>
          <th style="text-align:right">Total Penjualan</th>
          <th style="text-align:right">Kontribusi</th>
          <th>Proporsi</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $grandTotal = array_sum(array_column($perKategori, 'total'));
        ?>
        <?php foreach ($perKategori as $k): ?>
        <?php $pct = $grandTotal > 0 ? round(($k['total'] / $grandTotal) * 100, 1) : 0; ?>
        <tr>
          <td><strong><?= esc($k['kategori']) ?></strong></td>
          <td style="text-align:center"><?= number_format($k['total_qty']) ?> item</td>
          <td style="text-align:right;font-weight:600">Rp <?= number_format($k['total'], 0, ',', '.') ?></td>
          <td style="text-align:right">
            <span class="badge badge-info"><?= $pct ?>%</span>
          </td>
          <td style="min-width:120px">
            <div style="background:#f0f0f0;border-radius:4px;height:8px;overflow:hidden">
              <div style="height:8px;border-radius:4px;background:#1D9E75;width:<?= $pct ?>%"></div>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr style="background:#f8f9fa;font-weight:700">
          <td style="padding:10px 12px">Total</td>
          <td style="padding:10px 12px;text-align:center"><?= number_format(array_sum(array_column($perKategori, 'total_qty'))) ?> item</td>
          <td style="padding:10px 12px;text-align:right;color:#1D9E75">Rp <?= number_format($grandTotal, 0, ',', '.') ?></td>
          <td style="padding:10px 12px;text-align:right">100%</td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
