<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isEdit = isset($produk); ?>

<div style="max-width:700px">
  <div class="card">
    <div class="card-header">
      <div class="card-title">
        <i class="fas <?= $isEdit ? 'fa-edit' : 'fa-plus-circle' ?>" style="color:var(--green)"></i>
        <?= $isEdit ? 'Edit Produk' : 'Tambah Produk Baru' ?>
      </div>
      <a href="/produk" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <?php foreach (session()->getFlashdata('errors') as $e): ?>
        <div><i class="fas fa-exclamation-circle"></i> <?= $e ?></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $isEdit ? '/produk/update/'.$produk['id'] : '/produk/simpan' ?>">
      <?= csrf_field() ?>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kode Produk <span style="color:red">*</span></label>
          <input type="text" name="kode" class="form-control" value="<?= old('kode', $produk['kode'] ?? '') ?>"
            placeholder="Contoh: PRD-016" <?= $isEdit ? 'readonly' : '' ?> required>
        </div>
        <div class="form-group">
          <label class="form-label">Barcode (Opsional)</label>
          <div style="display:flex;gap:8px">
            <input type="text" name="barcode" id="barcodeField" class="form-control" value="<?= old('barcode', $produk['barcode'] ?? '') ?>" placeholder="Scan atau isi manual">
            <button type="button" class="btn btn-secondary" onclick="startScanForm()" title="Scan barcode"><i class="fas fa-barcode"></i></button>
          </div>
          <div id="scanStatus" style="font-size:11px;color:#1D9E75;margin-top:4px;display:none"><i class="fas fa-spinner fa-spin"></i> Kamera aktif, arahkan ke barcode...</div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Nama Produk <span style="color:red">*</span></label>
        <input type="text" name="nama" class="form-control" value="<?= old('nama', $produk['nama'] ?? '') ?>" placeholder="Nama produk" required>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kategori <span style="color:red">*</span></label>
          <select name="kategori_id" class="form-control" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategori as $k): ?>
            <option value="<?= $k['id'] ?>" <?= (old('kategori_id', $produk['kategori_id'] ?? '') == $k['id']) ? 'selected' : '' ?>>
              <?= esc($k['nama']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Supplier</label>
          <select name="supplier_id" class="form-control">
            <option value="">-- Pilih Supplier --</option>
            <?php foreach ($supplier as $s): ?>
            <option value="<?= $s['id'] ?>" <?= (old('supplier_id', $produk['supplier_id'] ?? '') == $s['id']) ? 'selected' : '' ?>>
              <?= esc($s['nama']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Harga Beli (Rp) <span style="color:red">*</span></label>
          <input type="number" name="harga_beli" class="form-control" value="<?= old('harga_beli', $produk['harga_beli'] ?? '') ?>" placeholder="0" required>
        </div>
        <div class="form-group">
          <label class="form-label">Harga Jual (Rp) <span style="color:red">*</span></label>
          <input type="number" name="harga_jual" class="form-control" value="<?= old('harga_jual', $produk['harga_jual'] ?? '') ?>" placeholder="0" required>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Stok Awal <span style="color:red">*</span></label>
          <input type="number" name="stok" class="form-control" value="<?= old('stok', $produk['stok'] ?? 0) ?>" <?= $isEdit ? 'readonly' : '' ?> required>
          <?php if ($isEdit): ?><div style="font-size:11px;color:#6c757d;margin-top:4px">Edit stok melalui menu Stok Opname</div><?php endif; ?>
        </div>
        <div class="form-group">
          <label class="form-label">Stok Minimum</label>
          <input type="number" name="stok_minimum" class="form-control" value="<?= old('stok_minimum', $produk['stok_minimum'] ?? 10) ?>" placeholder="10">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Satuan <span style="color:red">*</span></label>
          <select name="satuan" class="form-control" required>
            <?php foreach (['pcs','kg','gram','liter','ml','botol','bks','sak','buah','lusin','kodi'] as $s): ?>
            <option value="<?= $s ?>" <?= (old('satuan', $produk['satuan'] ?? '') === $s) ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php if ($isEdit): ?>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <option value="aktif"    <?= ($produk['status'] === 'aktif') ? 'selected' : '' ?>>Aktif</option>
            <option value="nonaktif" <?= ($produk['status'] === 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
          </select>
        </div>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi produk (opsional)"><?= old('deskripsi', $produk['deskripsi'] ?? '') ?></textarea>
      </div>

      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $isEdit ? 'Update Produk' : 'Simpan Produk' ?></button>
        <a href="/produk" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
      </div>
    </form>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
let scanActive = false;

function startScanForm() {
  if (scanActive) return;
  const status = document.getElementById('scanStatus');
  status.style.display = 'block';
  scanActive = true;

  // Buat elemen video temporary
  const video = document.createElement('div');
  video.id = 'scannerDiv';
  video.style.cssText = 'position:fixed;bottom:20px;right:20px;width:280px;height:200px;background:#000;border-radius:12px;z-index:999;overflow:hidden;border:2px solid #1D9E75';
  const closeBtn = document.createElement('button');
  closeBtn.textContent = '✕ Tutup';
  closeBtn.style.cssText = 'position:absolute;top:6px;right:6px;z-index:1000;background:#dc3545;color:white;border:none;border-radius:6px;padding:3px 8px;cursor:pointer;font-size:12px';
  closeBtn.onclick = stopScanForm;
  document.body.appendChild(video);
  video.appendChild(closeBtn);

  Quagga.init({
    inputStream: { name: 'Live', type: 'LiveStream', target: document.getElementById('scannerDiv'), constraints: { facingMode: 'environment' } },
    decoder: { readers: ['ean_reader','ean_8_reader','code_128_reader','code_39_reader','upc_reader'] },
  }, function(err) {
    if (err) { status.textContent = 'Gagal akses kamera'; return; }
    Quagga.start();
  });

  Quagga.onDetected(function(result) {
    document.getElementById('barcodeField').value = result.codeResult.code;
    stopScanForm();
  });
}

function stopScanForm() {
  if (!scanActive) return;
  Quagga.stop();
  scanActive = false;
  document.getElementById('scanStatus').style.display = 'none';
  const div = document.getElementById('scannerDiv');
  if (div) div.remove();
}
</script>

<?= $this->endSection() ?>
