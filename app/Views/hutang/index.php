<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="metric-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
  <div class="metric-card">
    <div class="metric-label"><i class="fas fa-money-bill-wave"></i> Total Hutang Aktif</div>
    <div class="metric-value" style="color:#dc3545">Rp <?= number_format($total_hutang, 0, ',', '.') ?></div>
    <div class="metric-sub" style="color:#6c757d">belum lunas</div>
  </div>
  <div class="metric-card">
    <div class="metric-label"><i class="fas fa-exclamation-triangle"></i> Jatuh Tempo Hari Ini</div>
    <div class="metric-value" style="color:<?= $jatuh_tempo > 0 ? '#dc3545' : '#1D9E75' ?>"><?= $jatuh_tempo ?></div>
    <div class="metric-sub" style="color:#6c757d">hutang melewati jatuh tempo</div>
  </div>
  <div class="metric-card">
    <div class="metric-label"><i class="fas fa-users"></i> Total Debitur</div>
    <div class="metric-value"><?= count(array_filter($hutang, fn($h) => $h['status'] === 'belum_lunas')) ?></div>
    <div class="metric-sub" style="color:#6c757d">pelanggan dengan hutang aktif</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-file-invoice-dollar" style="color:var(--green)"></i> Daftar Hutang Pelanggan</div>
  </div>

  <form method="GET" action="/hutang" style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
    <input type="text" name="keyword" class="form-control" placeholder="Cari nama / telepon / no. transaksi..." value="<?= esc($keyword) ?>" style="flex:1;min-width:220px">
    <select name="status" class="form-control" style="width:160px">
      <option value="">Semua Status</option>
      <option value="belum_lunas" <?= $status === 'belum_lunas' ? 'selected' : '' ?>>Belum Lunas</option>
      <option value="lunas"       <?= $status === 'lunas'       ? 'selected' : '' ?>>Lunas</option>
    </select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
    <a href="/hutang" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
  </form>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No. Transaksi</th>
          <th>Pelanggan</th>
          <th>Telepon</th>
          <th style="text-align:right">Total Hutang</th>
          <th style="text-align:right">Sudah Dibayar</th>
          <th style="text-align:right">Sisa Hutang</th>
          <th>Jatuh Tempo</th>
          <th>Status</th>
          <th style="text-align:center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($hutang)): ?>
        <tr><td colspan="9" style="text-align:center;padding:24px;color:#6c757d">Tidak ada data hutang</td></tr>
        <?php else: ?>
        <?php foreach ($hutang as $h): ?>
        <?php $terlambat = $h['status'] === 'belum_lunas' && $h['jatuh_tempo'] && $h['jatuh_tempo'] < date('Y-m-d'); ?>
        <tr style="<?= $terlambat ? 'background:#fff5f5' : '' ?>">
          <td>
            <a href="/transaksi/detail/<?= $h['transaksi_id'] ?>" style="color:#1D9E75;font-weight:600;text-decoration:none">
              <?= esc($h['no_transaksi']) ?>
            </a>
          </td>
          <td><strong><?= esc($h['pelanggan']) ?></strong></td>
          <td><?= esc($h['telepon'] ?? '-') ?></td>
          <td style="text-align:right">Rp <?= number_format($h['total_hutang'], 0, ',', '.') ?></td>
          <td style="text-align:right;color:#1D9E75">Rp <?= number_format($h['total_bayar'], 0, ',', '.') ?></td>
          <td style="text-align:right;font-weight:700;color:<?= $h['sisa_hutang'] > 0 ? '#dc3545' : '#1D9E75' ?>">
            Rp <?= number_format($h['sisa_hutang'], 0, ',', '.') ?>
          </td>
          <td>
            <?php if ($h['jatuh_tempo']): ?>
              <span style="color:<?= $terlambat ? '#dc3545' : '#6c757d' ?>;font-size:13px">
                <?= $terlambat ? '<i class="fas fa-exclamation-circle"></i> ' : '' ?>
                <?= date('d M Y', strtotime($h['jatuh_tempo'])) ?>
              </span>
            <?php else: ?>
              <span style="color:#6c757d">-</span>
            <?php endif; ?>
          </td>
          <td>
            <span class="badge <?= $h['status'] === 'lunas' ? 'badge-success' : 'badge-danger' ?>">
              <?= $h['status'] === 'lunas' ? 'Lunas' : 'Belum Lunas' ?>
            </span>
          </td>
          <td style="text-align:center">
            <a href="/hutang/detail/<?= $h['id'] ?>" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>
