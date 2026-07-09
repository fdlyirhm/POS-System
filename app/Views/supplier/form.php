<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isEdit = isset($supplier) && is_array($supplier) && isset($supplier['id']); ?>

<div style="max-width:600px">
  <div class="card">
    <div class="card-header">
      <div class="card-title"><i class="fas fa-truck" style="color:var(--green)"></i> <?= $isEdit ? 'Edit Supplier' : 'Tambah Supplier' ?></div>
      <a href="/supplier" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
    <form method="POST" action="<?= $isEdit ? '/supplier/update/'.$supplier['id'] : '/supplier/simpan' ?>">
      <?= csrf_field() ?>
      <?php if (!$isEdit): ?>
      <div class="form-group">
        <label class="form-label">Kode Supplier <span style="color:red">*</span></label>
        <input type="text" name="kode" class="form-control" placeholder="SUP-004" required>
      </div>
      <?php endif; ?>
      <div class="form-group">
        <label class="form-label">Nama Supplier <span style="color:red">*</span></label>
        <input type="text" name="nama" class="form-control" value="<?= esc($supplier['nama'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Kontak Person</label>
        <input type="text" name="kontak_person" class="form-control" value="<?= esc($supplier['kontak_person'] ?? '') ?>">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Telepon</label>
          <input type="text" name="telepon" class="form-control" value="<?= esc($supplier['telepon'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= esc($supplier['email'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-control" rows="3"><?= esc($supplier['alamat'] ?? '') ?></textarea>
      </div>
      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
        <a href="/supplier" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
