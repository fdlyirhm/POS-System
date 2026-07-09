<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="display:grid;grid-template-columns:1fr 300px;gap:16px">
  <div>
    <div class="card">
      <div class="card-header">
        <div style="display:flex;align-items:center;gap:12px">
          <div>
            <div class="card-title"><?= esc($pembelian['no_pembelian']) ?></div>
            <div style="font-size:12px;color:#6c757d">
              <?= date('d M Y', strtotime($pembelian['tanggal'])) ?> &nbsp;|&nbsp;
              Supplier: <strong><?= esc($pembelian['nama_supplier']) ?></strong>
            </div>
          </div>
          <span class="badge <?= $pembelian['status'] === 'diterima' ? 'badge-success' : ($pembelian['status'] === 'pending' ? 'badge-warning' : 'badge-danger') ?>" style="font-size:13px;padding:5px 12px">
            <?= ucfirst($pembelian['status']) ?>
          </span>
        </div>
        <div style="display:flex;gap:8px">
          <?php if ($pembelian['status'] === 'pending'): ?>
          <form method="POST" action="/pembelian/terima/<?= $pembelian['id'] ?>" onsubmit="return confirm('Konfirmasi penerimaan barang? Stok akan bertambah otomatis.')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check-circle"></i> Terima Barang</button>
          </form>
          <form method="POST" action="/pembelian/batal/<?= $pembelian['id'] ?>" onsubmit="return confirm('Batalkan pembelian ini?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Batal</button>
          </form>
          <?php endif; ?>
          <a href="/pembelian" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Kode</th>
              <th>Nama Produk</th>
              <th style="text-align:right">Harga Beli</th>
              <th style="text-align:center">Qty</th>
              <th style="text-align:right">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($detail as $d): ?>
            <tr>
              <td><?= esc($d['kode']) ?></td>
              <td>
                <strong><?= esc($d['nama_produk']) ?></strong>
                <div style="font-size:11px;color:#6c757d">Stok saat ini: <?= $d['stok_saat_ini'] ?> <?= $d['satuan'] ?></div>
              </td>
              <td style="text-align:right">Rp <?= number_format($d['harga_beli'], 0, ',', '.') ?></td>
              <td style="text-align:center;font-weight:600"><?= $d['qty'] ?> <?= $d['satuan'] ?></td>
              <td style="text-align:right;font-weight:600">Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr style="background:#f8f9fa;font-weight:700">
              <td colspan="4" style="padding:10px 12px;text-align:right">Total Pembelian:</td>
              <td style="padding:10px 12px;text-align:right;color:#1D9E75;font-size:15px">
                Rp <?= number_format($pembelian['total'], 0, ',', '.') ?>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-title" style="margin-bottom:14px">Info Pembelian</div>
      <table style="width:100%;font-size:13px">
        <tr><td style="color:#6c757d;padding:5px 0">No. Pembelian</td><td style="font-weight:600"><?= esc($pembelian['no_pembelian']) ?></td></tr>
        <tr><td style="color:#6c757d;padding:5px 0">Tanggal</td><td><?= date('d M Y', strtotime($pembelian['tanggal'])) ?></td></tr>
        <tr><td style="color:#6c757d;padding:5px 0">Supplier</td><td><?= esc($pembelian['nama_supplier']) ?></td></tr>
        <tr><td style="color:#6c757d;padding:5px 0">Telp. Supplier</td><td><?= esc($pembelian['telp_supplier'] ?? '-') ?></td></tr>
        <tr><td style="color:#6c757d;padding:5px 0">Dibuat oleh</td><td><?= esc($pembelian['nama_user']) ?></td></tr>
        <tr><td style="color:#6c757d;padding:5px 0">Catatan</td><td><?= esc($pembelian['catatan'] ?? '-') ?></td></tr>
      </table>
      <?php if ($pembelian['status'] === 'diterima'): ?>
      <div style="margin-top:14px;background:#E1F5EE;border-radius:8px;padding:10px 12px;font-size:12px;color:#065f46">
        <i class="fas fa-check-circle"></i> Barang sudah diterima. Stok produk telah diperbarui secara otomatis.
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
