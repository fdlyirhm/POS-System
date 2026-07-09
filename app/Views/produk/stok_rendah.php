<?php /* app/Views/produk/stok_rendah.php */ ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-exclamation-triangle" style="color:#e67e22"></i> Produk Stok Rendah / Hampir Habis</div>
    <a href="/produk" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
  </div>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Kode</th><th>Nama Produk</th><th>Kategori</th><th style="text-align:center">Stok</th><th style="text-align:center">Min. Stok</th><th>Status</th></tr></thead>
      <tbody>
        <?php if (empty($produk)): ?>
        <tr><td colspan="6" style="text-align:center;padding:24px;color:#6c757d"><i class="fas fa-check-circle" style="color:#1D9E75"></i> Semua stok dalam kondisi aman</td></tr>
        <?php else: ?>
        <?php foreach ($produk as $p): ?>
        <tr>
          <td><strong><?= esc($p['kode']) ?></strong></td>
          <td><?= esc($p['nama']) ?></td>
          <td><?= esc($p['nama_kategori']) ?></td>
          <td style="text-align:center;font-weight:700;color:<?= $p['stok']==0 ? '#dc3545' : '#e67e22' ?>"><?= $p['stok'] ?> <?= $p['satuan'] ?></td>
          <td style="text-align:center;color:#6c757d"><?= $p['stok_minimum'] ?></td>
          <td>
            <?php if ($p['stok'] == 0): ?>
              <span class="badge badge-danger">Habis</span>
            <?php elseif ($p['stok'] <= $p['stok_minimum']/2): ?>
              <span class="badge badge-danger">Kritis</span>
            <?php else: ?>
              <span class="badge badge-warning">Rendah</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
