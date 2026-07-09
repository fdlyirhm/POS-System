<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-receipt" style="color:var(--green)"></i> Daftar Transaksi</div>
    <a href="/transaksi/pos" class="btn btn-primary"><i class="fas fa-plus"></i> Transaksi Baru</a>
  </div>

  <!-- Filter -->
  <form method="GET" action="/transaksi" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px">
    <input type="text" name="keyword" class="form-control" placeholder="Cari no. transaksi / pelanggan..." value="<?= esc($keyword) ?>" style="flex:1;min-width:200px">
    <input type="date" name="dari" class="form-control" value="<?= $dari ?>" style="width:150px">
    <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>" style="width:150px">
    <select name="status" class="form-control" style="width:130px">
      <option value="">Semua Status</option>
      <option value="lunas"  <?= $status==='lunas'  ? 'selected':'' ?>>Lunas</option>
      <option value="kredit" <?= $status==='kredit' ? 'selected':'' ?>>Kredit</option>
      <option value="batal"  <?= $status==='batal'  ? 'selected':'' ?>>Batal</option>
    </select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
    <a href="/transaksi" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
  </form>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No. Transaksi</th>
          <th>Tanggal</th>
          <th>Pelanggan</th>
          <th>Kasir</th>
          <th>Total</th>
          <th>Metode</th>
          <th>Status</th>
          <th style="text-align:center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($transaksi)): ?>
        <tr><td colspan="8" style="text-align:center;padding:24px;color:#6c757d">Tidak ada data transaksi</td></tr>
        <?php else: ?>
        <?php foreach ($transaksi as $t): ?>
        <tr>
          <td><strong><?= esc($t['no_transaksi']) ?></strong></td>
          <td><?= date('d/m/Y H:i', strtotime($t['tanggal'])) ?></td>
          <td><?= esc($t['pelanggan']) ?></td>
          <td><?= esc($t['nama_kasir']) ?></td>
          <td><strong>Rp <?= number_format($t['total'],0,',','.') ?></strong></td>
          <td><span class="badge badge-info"><?= ucfirst($t['metode_bayar']) ?></span></td>
          <td>
            <span class="badge <?= $t['status']==='lunas' ? 'badge-success' : ($t['status']==='kredit' ? 'badge-warning' : 'badge-danger') ?>">
              <?= ucfirst($t['status']) ?>
            </span>
          </td>
          <td style="text-align:center">
            <a href="/transaksi/detail/<?= $t['id'] ?>" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
            <a href="/transaksi/struk/<?= $t['id'] ?>" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i></a>
            <?php if ($t['status'] !== 'batal' && session()->get('role') === 'admin'): ?>
            <form method="POST" action="/transaksi/batal/<?= $t['id'] ?>" style="display:inline" onsubmit="return confirm('Batalkan transaksi ini?')">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
            </form>
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
