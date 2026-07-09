<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
  <div class="card-header">
    <div style="display:flex;align-items:center;gap:14px">
      <div>
        <div class="card-title"><?= esc($opname['no_opname']) ?></div>
        <div style="font-size:12px;color:#6c757d">Tanggal: <?= date('d M Y', strtotime($opname['tanggal'])) ?> &nbsp;|&nbsp; Petugas: <?= esc($opname['nama_user'] ?? '-') ?></div>
      </div>
      <span class="badge <?= $opname['status'] === 'selesai' ? 'badge-success' : 'badge-warning' ?>" style="font-size:13px;padding:5px 12px">
        <?= ucfirst($opname['status']) ?>
      </span>
    </div>
    <div style="display:flex;gap:8px">
      <?php if ($opname['status'] === 'draft'): ?>
      <form method="POST" action="/stok-opname/selesaikan/<?= $opname['id'] ?>" onsubmit="return confirm('Finalisasi opname ini? Stok akan diperbarui sesuai stok fisik.')">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check-circle"></i> Selesaikan & Terapkan</button>
      </form>
      <?php endif; ?>
      <a href="/stok-opname" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
  </div>

  <?php if ($opname['catatan']): ?>
  <div style="background:#f8f9fa;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#6c757d">
    <i class="fas fa-sticky-note"></i> <?= esc($opname['catatan']) ?>
  </div>
  <?php endif; ?>

  <!-- Ringkasan selisih -->
  <?php
    $plusItems  = array_filter($detail, fn($d) => $d['selisih'] > 0);
    $minusItems = array_filter($detail, fn($d) => $d['selisih'] < 0);
    $okItems    = array_filter($detail, fn($d) => $d['selisih'] == 0);
  ?>
  <div class="metric-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:16px">
    <div class="metric-card">
      <div class="metric-label">Produk Sesuai</div>
      <div class="metric-value" style="color:#1D9E75"><?= count($okItems) ?></div>
      <div class="metric-sub" style="color:#6c757d">tidak ada selisih</div>
    </div>
    <div class="metric-card">
      <div class="metric-label">Stok Lebih</div>
      <div class="metric-value" style="color:#378ADD"><?= count($plusItems) ?></div>
      <div class="metric-sub" style="color:#6c757d">fisik > sistem</div>
    </div>
    <div class="metric-card">
      <div class="metric-label">Stok Kurang</div>
      <div class="metric-value" style="color:#dc3545"><?= count($minusItems) ?></div>
      <div class="metric-sub" style="color:#6c757d">fisik < sistem</div>
    </div>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Kode</th>
          <th>Nama Produk</th>
          <th>Kategori</th>
          <th style="text-align:center">Stok Sistem</th>
          <th style="text-align:center">Stok Fisik</th>
          <th style="text-align:center">Selisih</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($detail as $d): ?>
        <tr style="<?= $d['selisih'] != 0 ? 'background:' . ($d['selisih'] > 0 ? '#f0fff8' : '#fff5f5') : '' ?>">
          <td><?= esc($d['kode']) ?></td>
          <td><strong><?= esc($d['nama_produk']) ?></strong></td>
          <td><?= esc($d['nama_kategori']) ?></td>
          <td style="text-align:center"><?= $d['stok_sistem'] ?></td>
          <td style="text-align:center;font-weight:600"><?= $d['stok_fisik'] ?></td>
          <td style="text-align:center;font-weight:700;color:<?= $d['selisih'] > 0 ? '#1D9E75' : ($d['selisih'] < 0 ? '#dc3545' : '#6c757d') ?>">
            <?= ($d['selisih'] > 0 ? '+' : '') . $d['selisih'] ?>
          </td>
          <td style="font-size:12px;color:#6c757d"><?= esc($d['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>
