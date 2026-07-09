<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-shopping-basket" style="color:var(--green)"></i> Pembelian / Restock Barang</div>
    <a href="/pembelian/buat" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Pembelian</a>
  </div>

  <form method="GET" action="/pembelian" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px">
    <input type="text" name="keyword" class="form-control" placeholder="Cari no. pembelian / supplier..." value="<?= esc($keyword) ?>" style="flex:1;min-width:200px">
    <input type="date" name="dari" class="form-control" value="<?= $dari ?>" style="width:150px">
    <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>" style="width:150px">
    <select name="status" class="form-control" style="width:140px">
      <option value="">Semua Status</option>
      <option value="pending"  <?= $status === 'pending'  ? 'selected' : '' ?>>Pending</option>
      <option value="diterima" <?= $status === 'diterima' ? 'selected' : '' ?>>Diterima</option>
      <option value="batal"    <?= $status === 'batal'    ? 'selected' : '' ?>>Batal</option>
    </select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
    <a href="/pembelian" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
  </form>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No. Pembelian</th>
          <th>Tanggal</th>
          <th>Supplier</th>
          <th>Dibuat Oleh</th>
          <th style="text-align:right">Total</th>
          <th>Status</th>
          <th style="text-align:center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($pembelian)): ?>
        <tr><td colspan="7" style="text-align:center;padding:24px;color:#6c757d">Tidak ada data pembelian</td></tr>
        <?php else: ?>
        <?php foreach ($pembelian as $p): ?>
        <tr>
          <td><strong><?= esc($p['no_pembelian']) ?></strong></td>
          <td><?= date('d M Y', strtotime($p['tanggal'])) ?></td>
          <td><?= esc($p['nama_supplier']) ?></td>
          <td><?= esc($p['nama_user']) ?></td>
          <td style="text-align:right;font-weight:600">Rp <?= number_format($p['total'], 0, ',', '.') ?></td>
          <td>
            <span class="badge <?= $p['status'] === 'diterima' ? 'badge-success' : ($p['status'] === 'pending' ? 'badge-warning' : 'badge-danger') ?>">
              <?= ucfirst($p['status']) ?>
            </span>
          </td>
          <td style="text-align:center">
            <a href="/pembelian/detail/<?= $p['id'] ?>" class="btn btn-secondary btn-sm"><i class="fas fa-eye"></i></a>
            <?php if ($p['status'] === 'pending'): ?>
            <form method="POST" action="/pembelian/terima/<?= $p['id'] ?>" style="display:inline" onsubmit="return confirm('Konfirmasi penerimaan barang?')">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Terima</button>
            </form>
            <form method="POST" action="/pembelian/batal/<?= $p['id'] ?>" style="display:inline" onsubmit="return confirm('Batalkan pembelian ini?')">
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
