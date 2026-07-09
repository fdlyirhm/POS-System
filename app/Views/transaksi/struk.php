<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk #<?= esc($transaksi['no_transaksi']) ?></title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Courier New', monospace; font-size: 12px; background: #fff; width: 300px; margin: 0 auto; padding: 12px 10px; }
  .center  { text-align: center; }
  .bold    { font-weight: bold; }
  .sep-dash{ border-top: 1px dashed #000; margin: 7px 0; }
  .sep-solid{ border-top: 1px solid #000; margin: 7px 0; }
  .row     { display: flex; justify-content: space-between; margin: 2px 0; }
  .item-name   { margin: 4px 0 1px; font-weight: bold; }
  .item-detail { display: flex; justify-content: space-between; color: #333; }
  .total-row   { display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; }
  @media print {
    body { width: 80mm; }
    .no-print { display: none !important; }
    @page { margin: 0; size: 80mm auto; }
  }
</style>
</head>
<body>

<?php
  $namaTokoStruk = $toko['nama_toko']  ?? 'Toko Sembako';
  $alamatStruk   = $toko['alamat']     ?? '';
  $teleponStruk  = $toko['telepon']    ?? '';
  $footerStruk   = $toko['nota_footer'] ?? 'Terima kasih atas kunjungan Anda!';
  $simbol        = $toko['mata_uang']  ?? 'Rp';
  $pajakPersen   = (float)($toko['pajak_persen'] ?? 11);
?>

<!-- Header Toko (dari Pengaturan DB) -->
<div class="center">
  <div class="bold" style="font-size:15px;letter-spacing:1px"><?= strtoupper(esc($namaTokoStruk)) ?></div>
  <?php if ($alamatStruk): ?>
  <div style="font-size:11px;margin-top:2px"><?= esc($alamatStruk) ?></div>
  <?php endif; ?>
  <?php if ($teleponStruk): ?>
  <div style="font-size:11px">Telp: <?= esc($teleponStruk) ?></div>
  <?php endif; ?>
</div>

<div class="sep-solid"></div>

<div class="row"><span>No. Transaksi</span><span class="bold"><?= esc($transaksi['no_transaksi']) ?></span></div>
<div class="row"><span>Tanggal</span><span><?= date('d/m/Y H:i', strtotime($transaksi['tanggal'])) ?></span></div>
<div class="row"><span>Kasir</span><span><?= esc($transaksi['nama_kasir']) ?></span></div>
<div class="row"><span>Pelanggan</span><span><?= esc($transaksi['pelanggan']) ?></span></div>

<div class="sep-dash"></div>

<?php foreach ($detail as $item): ?>
<div class="item-name"><?= esc($item['nama_produk']) ?></div>
<div class="item-detail">
  <span><?= $item['qty'] ?> x <?= $simbol ?> <?= number_format($item['harga'], 0, ',', '.') ?></span>
  <span class="bold"><?= $simbol ?> <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
</div>
<?php endforeach; ?>

<div class="sep-dash"></div>

<div class="row"><span>Subtotal</span><span><?= $simbol ?> <?= number_format($transaksi['subtotal'], 0, ',', '.') ?></span></div>

<?php if ($transaksi['diskon'] > 0): ?>
<div class="row"><span>Diskon</span><span>- <?= $simbol ?> <?= number_format($transaksi['diskon'], 0, ',', '.') ?></span></div>
<?php endif; ?>

<?php if ($pajakPersen > 0): ?>
<div class="row"><span>PPN (<?= $pajakPersen ?>%)</span><span><?= $simbol ?> <?= number_format($transaksi['pajak'], 0, ',', '.') ?></span></div>
<?php endif; ?>

<div class="sep-solid"></div>
<div class="total-row"><span>TOTAL</span><span><?= $simbol ?> <?= number_format($transaksi['total'], 0, ',', '.') ?></span></div>

<?php if ($transaksi['metode_bayar'] === 'tunai'): ?>
<div class="sep-dash"></div>
<div class="row"><span>Bayar</span><span><?= $simbol ?> <?= number_format($transaksi['bayar'], 0, ',', '.') ?></span></div>
<div class="row bold"><span>Kembalian</span><span><?= $simbol ?> <?= number_format($transaksi['kembalian'], 0, ',', '.') ?></span></div>
<?php endif; ?>

<div class="sep-dash"></div>
<div class="row">
  <span>Metode Bayar</span>
  <span class="bold"><?= strtoupper($transaksi['metode_bayar']) ?></span>
</div>

<?php if ($transaksi['metode_bayar'] === 'kredit'): ?>
<div class="row" style="color:#cc0000">
  <span>Status Hutang</span><span class="bold">BELUM LUNAS</span>
</div>
<?php endif; ?>

<div class="sep-solid"></div>

<!-- Footer dari Pengaturan DB -->
<div class="center" style="margin-top:6px;font-size:11px">
  <div><?= esc($footerStruk) ?></div>
  <div style="margin-top:5px">**** Barang yang sudah dibeli ****</div>
  <div>**** tidak dapat dikembalikan ****</div>
</div>

<div class="center no-print" style="margin-top:16px;display:flex;gap:8px;justify-content:center">
  <button onclick="window.print()" style="padding:8px 18px;background:#1D9E75;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500">
    <i class="fas fa-print"></i> Cetak
  </button>
  <button onclick="window.close()" style="padding:8px 18px;background:#6c757d;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px">
    Tutup
  </button>
</div>

<script>window.onload = () => window.print();</script>
</body>
</html>
