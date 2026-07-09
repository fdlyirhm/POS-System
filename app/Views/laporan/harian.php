<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="margin-bottom:16px">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-calendar-day" style="color:var(--green)"></i> Laporan Harian</div>
    <div style="display:flex;gap:8px">
    <a href="/laporan/export-pdf/harian?tanggal=<?= $tanggal ?>" target="_blank" class="btn btn-secondary btn-sm">
      <i class="fas fa-file-pdf" style="color:#dc3545"></i> Export PDF
    </a>
    <a href="/laporan/export/csv?dari=<?= $tanggal ?>&sampai=<?= $tanggal ?>" class="btn btn-secondary btn-sm">
      <i class="fas fa-download"></i> Export CSV
    </a>
  </div>
  </div>
  <form method="GET" action="/laporan/harian" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <label class="form-label" style="margin:0;white-space:nowrap">Pilih Tanggal:</label>
    <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>" style="width:200px">
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
    <a href="/laporan" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
  </form>
</div>

<div class="metric-grid" style="grid-template-columns:repeat(4,1fr)">
  <div class="metric-card">
    <div class="metric-label">Total Penjualan</div>
    <div class="metric-value">Rp <?= number_format($ringkasan['total_penjualan'] ?? 0, 0, ',', '.') ?></div>
    <div class="metric-sub" style="color:#6c757d"><?= date('d M Y', strtotime($tanggal)) ?></div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Jumlah Transaksi</div>
    <div class="metric-value"><?= $ringkasan['jumlah_transaksi'] ?? 0 ?></div>
    <div class="metric-sub" style="color:#6c757d">transaksi selesai</div>
  </div>
  <div class="metric-card">
    <div class="metric-label">Total Diskon</div>
    <div class="metric-value" style="color:#e67e22">Rp <?= number_format($ringkasan['total_diskon'] ?? 0, 0, ',', '.') ?></div>
    <div class="metric-sub" style="color:#6c757d">potongan harga</div>
  </div>
  <div class="metric-card">
    <div class="metric-label">PPN (<?= $pajak_persen ?>%)</div>
    <div class="metric-value">Rp <?= number_format($ringkasan['total_pajak'] ?? 0, 0, ',', '.') ?></div>
    <div class="metric-sub" style="color:#6c757d">pajak terkumpul</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title">Detail Transaksi — <?= date('d F Y', strtotime($tanggal)) ?></div>
    <span style="font-size:13px;color:#6c757d"><?= count($transaksi) ?> transaksi</span>
  </div>

  <?php if (empty($transaksi)): ?>
  <div style="text-align:center;padding:32px;color:#6c757d">
    <i class="fas fa-inbox" style="font-size:36px;display:block;margin-bottom:12px"></i>
    Tidak ada transaksi pada tanggal ini
  </div>
  <?php else: ?>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No. Transaksi</th><th>Waktu</th><th>Pelanggan</th><th>Kasir</th>
          <th style="text-align:right">Subtotal</th>
          <th style="text-align:right">Diskon</th>
          <th style="text-align:right">PPN</th>
          <th style="text-align:right">Total</th>
          <th>Metode</th><th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($transaksi as $t): ?>
        <tr>
          <td><a href="/transaksi/detail/<?= $t['id'] ?>" style="color:#1D9E75;font-weight:600;text-decoration:none"><?= esc($t['no_transaksi']) ?></a></td>
          <td><?= date('H:i', strtotime($t['tanggal'])) ?></td>
          <td><?= esc($t['pelanggan']) ?></td>
          <td><?= esc($t['nama_kasir']) ?></td>
          <td style="text-align:right">Rp <?= number_format($t['subtotal'], 0, ',', '.') ?></td>
          <td style="text-align:right;color:#e67e22"><?= $t['diskon'] > 0 ? '- Rp '.number_format($t['diskon'],0,',','.') : '-' ?></td>
          <td style="text-align:right">Rp <?= number_format($t['pajak'], 0, ',', '.') ?></td>
          <td style="text-align:right;font-weight:600">Rp <?= number_format($t['total'], 0, ',', '.') ?></td>
          <td><span class="badge badge-info"><?= ucfirst($t['metode_bayar']) ?></span></td>
          <td><span class="badge <?= $t['status']==='lunas'?'badge-success':($t['status']==='kredit'?'badge-warning':'badge-danger') ?>"><?= ucfirst($t['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr style="background:#f8f9fa;font-weight:700">
          <td colspan="4" style="padding:10px 11px;text-align:right">Total:</td>
          <td style="padding:10px 11px;text-align:right">Rp <?= number_format(array_sum(array_column($transaksi,'subtotal')),0,',','.') ?></td>
          <td style="padding:10px 11px;text-align:right;color:#e67e22">- Rp <?= number_format(array_sum(array_column($transaksi,'diskon')),0,',','.') ?></td>
          <td style="padding:10px 11px;text-align:right">Rp <?= number_format(array_sum(array_column($transaksi,'pajak')),0,',','.') ?></td>
          <td style="padding:10px 11px;text-align:right;color:#1D9E75;font-size:14px">Rp <?= number_format(array_sum(array_column($transaksi,'total')),0,',','.') ?></td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <?php endif; ?>
</div>

<?= $this->endSection() ?>
