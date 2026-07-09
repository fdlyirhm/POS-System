<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-clipboard-list" style="color:var(--green)"></i> Stok Opname</div>
    <a href="/stok-opname/buat" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Opname Baru</a>
  </div>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No. Opname</th>
          <th>Tanggal</th>
          <th>Petugas</th>
          <th>Catatan</th>
          <th>Status</th>
          <th style="text-align:center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($opname)): ?>
        <tr><td colspan="6" style="text-align:center;padding:24px;color:#6c757d">Belum ada data stok opname</td></tr>
        <?php else: ?>
        <?php foreach ($opname as $o): ?>
        <tr>
          <td><strong><?= esc($o['no_opname']) ?></strong></td>
          <td><?= date('d M Y', strtotime($o['tanggal'])) ?></td>
          <td><?= esc($o['nama_user']) ?></td>
          <td><?= esc($o['catatan'] ?? '-') ?></td>
          <td>
            <span class="badge <?= $o['status'] === 'selesai' ? 'badge-success' : 'badge-warning' ?>">
              <?= ucfirst($o['status']) ?>
            </span>
          </td>
          <td style="text-align:center">
            <a href="/stok-opname/detail/<?= $o['id'] ?>" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i> Detail</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>
