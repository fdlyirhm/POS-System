<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="display:grid;grid-template-columns:1fr 320px;gap:16px">
  <div>
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-receipt" style="color:var(--green)"></i> Detail Transaksi</div>
        <div style="display:flex;gap:8px">
          <a href="/transaksi/struk/<?= $transaksi['id'] ?>" target="_blank" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Cetak Struk</a>
          <a href="/transaksi" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px">
        <div>
          <div style="font-size:12px;color:#6c757d">No. Transaksi</div>
          <div style="font-weight:700;font-size:16px"><?= esc($transaksi['no_transaksi']) ?></div>
        </div>
        <div>
          <div style="font-size:12px;color:#6c757d">Status</div>
          <span class="badge <?= $transaksi['status']==='lunas' ? 'badge-success' : ($transaksi['status']==='kredit' ? 'badge-warning' : 'badge-danger') ?>" style="font-size:13px;padding:5px 12px">
            <?= ucfirst($transaksi['status']) ?>
          </span>
        </div>
        <div>
          <div style="font-size:12px;color:#6c757d">Tanggal & Waktu</div>
          <div style="font-weight:500"><?= date('d M Y, H:i:s', strtotime($transaksi['tanggal'])) ?></div>
        </div>
        <div>
          <div style="font-size:12px;color:#6c757d">Kasir</div>
          <div style="font-weight:500"><?= esc($transaksi['nama_kasir']) ?></div>
        </div>
        <div>
          <div style="font-size:12px;color:#6c757d">Pelanggan</div>
          <div style="font-weight:500"><?= esc($transaksi['pelanggan']) ?></div>
        </div>
        <div>
          <div style="font-size:12px;color:#6c757d">Metode Bayar</div>
          <div style="font-weight:500"><?= ucfirst($transaksi['metode_bayar']) ?></div>
        </div>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Produk</th>
              <th>Kode</th>
              <th style="text-align:right">Harga</th>
              <th style="text-align:center">Qty</th>
              <th style="text-align:right">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($detail as $d): ?>
            <tr>
              <td><strong><?= esc($d['nama_produk']) ?></strong></td>
              <td><?= esc($d['kode']) ?></td>
              <td style="text-align:right">Rp <?= number_format($d['harga'],0,',','.') ?></td>
              <td style="text-align:center"><?= $d['qty'] ?> <?= $d['satuan'] ?></td>
              <td style="text-align:right;font-weight:600">Rp <?= number_format($d['subtotal'],0,',','.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-title" style="margin-bottom:16px">Ringkasan Pembayaran</div>
      <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid #f0f0f0">
        <span>Subtotal</span><span>Rp <?= number_format($transaksi['subtotal'],0,',','.') ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid #f0f0f0">
        <span>Diskon</span><span style="color:#065f46">- Rp <?= number_format($transaksi['diskon'],0,',','.') ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid #f0f0f0">
        <span>PPN (11%)</span><span>Rp <?= number_format($transaksi['pajak'],0,',','.') ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:700;padding:12px 0;border-top:2px solid #e9ecef;margin-top:4px">
        <span>TOTAL</span><span style="color:#1D9E75">Rp <?= number_format($transaksi['total'],0,',','.') ?></span>
      </div>
      <?php if ($transaksi['metode_bayar'] === 'tunai'): ?>
      <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0">
        <span>Bayar</span><span>Rp <?= number_format($transaksi['bayar'],0,',','.') ?></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:600;padding:6px 0;color:#1D9E75">
        <span>Kembalian</span><span>Rp <?= number_format($transaksi['kembalian'],0,',','.') ?></span>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
