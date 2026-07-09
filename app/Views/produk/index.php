<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-box" style="color:var(--green)"></i> Daftar Produk</div>
    <?php if (session()->get('role') === 'admin'): ?>
    <a href="/produk/tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Produk</a>
    <?php endif; ?>
  </div>

  <form method="GET" action="/produk" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px">
    <input type="text" name="keyword" class="form-control" placeholder="Cari kode, nama, barcode..." value="<?= esc($keyword) ?>" style="flex:1;min-width:200px">
    <select name="kategori_id" class="form-control" style="width:180px">
      <option value="">Semua Kategori</option>
      <?php foreach ($kategori as $k): ?>
      <option value="<?= $k['id'] ?>"><?= esc($k['nama']) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
    <a href="/produk" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
  </form>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Kode</th>
          <th>Barcode</th>
          <th>Nama Produk</th>
          <th>Kategori</th>
          <th style="text-align:right">Harga Beli</th>
          <th style="text-align:right">Harga Jual</th>
          <th style="text-align:center">Stok</th>
          <th>Status</th>
          <?php if (session()->get('role') === 'admin'): ?>
          <th style="text-align:center">Aksi</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($produk)): ?>
        <tr><td colspan="9" style="text-align:center;padding:24px;color:#6c757d">Tidak ada data produk</td></tr>
        <?php else: ?>
        <?php foreach ($produk as $p): ?>
        <tr>
          <td><strong><?= esc($p['kode']) ?></strong></td>
          <td style="font-family:monospace;font-size:12px"><?= esc($p['barcode'] ?? '-') ?></td>
          <td><?= esc($p['nama']) ?></td>
          <td><?= esc($p['nama_kategori']) ?></td>
          <td style="text-align:right">Rp <?= number_format($p['harga_beli'],0,',','.') ?></td>
          <td style="text-align:right;font-weight:600">Rp <?= number_format($p['harga_jual'],0,',','.') ?></td>
          <td style="text-align:center">
            <?php $stokClass = $p['stok'] == 0 ? 'badge-danger' : ($p['stok'] <= $p['stok_minimum'] ? 'badge-warning' : 'badge-success'); ?>
            <span class="badge <?= $stokClass ?>"><?= $p['stok'] ?> <?= $p['satuan'] ?></span>
          </td>
          <td>
            <span class="badge <?= $p['status']==='aktif' ? 'badge-success' : 'badge-danger' ?>">
              <?= ucfirst($p['status']) ?>
            </span>
          </td>
          <?php if (session()->get('role') === 'admin'): ?>
          <td style="text-align:center">
            <a href="/produk/edit/<?= $p['id'] ?>" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></a>
            <form method="POST" action="/produk/hapus/<?= $p['id'] ?>" style="display:inline" onsubmit="return confirm('Nonaktifkan produk ini?')">
              <?= csrf_field() ?>
              <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
            </form>
          </td>
          <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>
