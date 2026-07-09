<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="metric-grid">
  <div class="metric-card">
    <div class="metric-label"><i class="fas fa-money-bill-wave"></i> Penjualan Hari Ini</div>
    <div class="metric-value">Rp <?= number_format($penjualan_hari, 0, ',', '.') ?></div>
    <div class="metric-sub text-success"><i class="fas fa-circle" style="font-size:7px"></i> Real-time</div>
  </div>
  <div class="metric-card">
    <div class="metric-label"><i class="fas fa-receipt"></i> Transaksi Hari Ini</div>
    <div class="metric-value"><?= $total_transaksi ?></div>
    <div class="metric-sub" style="color:#6c757d">transaksi selesai</div>
  </div>
  <div class="metric-card">
    <div class="metric-label"><i class="fas fa-exclamation-triangle"></i> Stok Hampir Habis</div>
    <div class="metric-value <?= count($stok_rendah) > 0 ? 'text-danger' : 'text-success' ?>"><?= count($stok_rendah) ?></div>
    <div class="metric-sub" style="color:#6c757d"><a href="/produk/stok" style="color:inherit">lihat detail →</a></div>
  </div>
  <div class="metric-card">
    <div class="metric-label"><i class="fas fa-file-invoice-dollar"></i> Total Hutang Aktif</div>
    <div class="metric-value <?= $total_hutang > 0 ? 'text-danger' : 'text-success' ?>">Rp <?= number_format($total_hutang, 0, ',', '.') ?></div>
    <div class="metric-sub" style="color:#6c757d">
      <?php if ($hutang_jatuh_tempo > 0): ?>
        <span style="color:#dc3545"><i class="fas fa-exclamation-circle"></i> <?= $hutang_jatuh_tempo ?> jatuh tempo</span>
      <?php else: ?>
        semua tepat waktu
      <?php endif; ?>
    </div>
  </div>
  <?php if ($pembelian_pending > 0): ?>
  <div class="metric-card" style="border:1px solid #fef3c7">
    <div class="metric-label"><i class="fas fa-shopping-basket"></i> Pembelian Pending</div>
    <div class="metric-value" style="color:#92400e"><?= $pembelian_pending ?></div>
    <div class="metric-sub"><a href="/pembelian?status=pending" style="color:#92400e">konfirmasi penerimaan →</a></div>
  </div>
  <?php endif; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
  <!-- Produk Terlaris -->
  <div class="card">
    <div class="card-header">
      <div class="card-title"><i class="fas fa-fire" style="color:#e67e22"></i> Produk Terlaris Bulan Ini</div>
      <a href="/laporan/produk" style="font-size:12px;color:var(--green);text-decoration:none">Lihat semua →</a>
    </div>
    <?php if ($produk_terlaris): ?>
    <?php $maxQty = max(array_column($produk_terlaris, 'total_qty')); ?>
    <?php foreach ($produk_terlaris as $p): ?>
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
      <div style="font-size:12px;color:#6c757d;width:110px;text-align:right;flex-shrink:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= esc($p['nama']) ?>"><?= esc($p['nama']) ?></div>
      <div style="flex:1;background:#f0f0f0;border-radius:4px;height:10px">
        <div style="height:10px;border-radius:4px;background:#1D9E75;width:<?= $maxQty > 0 ? round(($p['total_qty']/$maxQty)*100) : 0 ?>%"></div>
      </div>
      <div style="font-size:11px;color:#6c757d;width:55px;flex-shrink:0"><?= number_format($p['total_qty']) ?> <?= $p['satuan'] ?></div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <p style="color:#6c757d;font-size:13px;text-align:center;padding:16px">Belum ada data penjualan bulan ini</p>
    <?php endif; ?>
  </div>

  <!-- Transaksi Terbaru -->
  <div class="card">
    <div class="card-header">
      <div class="card-title"><i class="fas fa-clock" style="color:var(--green)"></i> Transaksi Terbaru</div>
      <a href="/transaksi" style="font-size:12px;color:var(--green);text-decoration:none">Lihat semua →</a>
    </div>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead><tr>
        <th style="text-align:left;padding:5px 8px;color:#6c757d;font-size:11px;border-bottom:1px solid #e9ecef">No. Transaksi</th>
        <th style="text-align:left;padding:5px 8px;color:#6c757d;font-size:11px;border-bottom:1px solid #e9ecef">Pelanggan</th>
        <th style="text-align:right;padding:5px 8px;color:#6c757d;font-size:11px;border-bottom:1px solid #e9ecef">Total</th>
        <th style="padding:5px 8px;border-bottom:1px solid #e9ecef"></th>
      </tr></thead>
      <tbody>
        <?php foreach ($transaksi_terbaru as $t): ?>
        <tr>
          <td style="padding:8px;border-bottom:1px solid #f0f0f0;font-weight:500">
            <a href="/transaksi/detail/<?= $t['id'] ?? '' ?>" style="color:var(--green);text-decoration:none"><?= esc($t['no_transaksi']) ?></a>
          </td>
          <td style="padding:8px;border-bottom:1px solid #f0f0f0"><?= esc($t['pelanggan']) ?></td>
          <td style="padding:8px;border-bottom:1px solid #f0f0f0;text-align:right;font-weight:600">Rp <?= number_format($t['total'],0,',','.') ?></td>
          <td style="padding:8px;border-bottom:1px solid #f0f0f0">
            <span class="badge <?= $t['status']==='lunas' ? 'badge-success' : ($t['status']==='kredit' ? 'badge-warning' : 'badge-danger') ?>">
              <?= ucfirst($t['status']) ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Stok Rendah -->
<?php if (count($stok_rendah) > 0): ?>
<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-exclamation-triangle" style="color:#e67e22"></i> Produk Perlu Restock</div>
    <div style="display:flex;gap:8px">
      <a href="/pembelian/buat" class="btn btn-primary btn-sm"><i class="fas fa-shopping-basket"></i> Buat Pembelian</a>
      <a href="/produk/stok" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>
  </div>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Kode</th><th>Nama Produk</th><th>Kategori</th><th style="text-align:center">Stok</th><th style="text-align:center">Minimum</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach (array_slice($stok_rendah, 0, 5) as $p): ?>
        <tr>
          <td><?= esc($p['kode']) ?></td>
          <td><strong><?= esc($p['nama']) ?></strong></td>
          <td><?= esc($p['nama_kategori']) ?></td>
          <td style="text-align:center;font-weight:700;color:<?= $p['stok'] == 0 ? '#dc3545' : '#e67e22' ?>"><?= $p['stok'] ?> <?= $p['satuan'] ?></td>
          <td style="text-align:center;color:#6c757d"><?= $p['stok_minimum'] ?></td>
          <td>
            <?php if ($p['stok'] == 0): ?>
              <span class="badge badge-danger">Habis</span>
            <?php elseif ($p['stok'] <= $p['stok_minimum'] / 2): ?>
              <span class="badge badge-danger">Kritis</span>
            <?php else: ?>
              <span class="badge badge-warning">Rendah</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
