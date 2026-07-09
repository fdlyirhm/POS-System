<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:16px">
  <!-- List Kategori -->
  <div class="card">
    <div class="card-title" style="margin-bottom:14px"><i class="fas fa-tags" style="color:var(--green)"></i> Master Kategori</div>
    <div class="table-wrapper">
      <table>
        <thead><tr><th>Kode</th><th>Nama Kategori</th><th>Deskripsi</th><th style="text-align:center">Aksi</th></tr></thead>
        <tbody>
          <?php foreach ($kategori as $k): ?>
          <tr>
            <td><strong><?= esc($k['kode']) ?></strong></td>
            <td><?= esc($k['nama']) ?></td>
            <td><?= esc($k['deskripsi'] ?? '-') ?></td>
            <td style="text-align:center">
              <button type="button" class="btn btn-secondary btn-sm"
                onclick="editKategori(<?= $k['id'] ?>,'<?= addslashes($k['nama']) ?>','<?= addslashes($k['deskripsi'] ?? '') ?>')">
                <i class="fas fa-edit"></i>
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Form Tambah/Edit -->
  <div>
    <div class="card">
      <div class="card-title" style="margin-bottom:14px" id="formTitle">Tambah Kategori</div>
      <form method="POST" id="kategoriForm" action="/kategori/simpan">
        <?= csrf_field() ?>
        <div class="form-group">
          <label class="form-label">Kode <span style="color:red">*</span></label>
          <input type="text" name="kode" id="kodeField" class="form-control" placeholder="KAT-06" required>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Kategori <span style="color:red">*</span></label>
          <input type="text" name="nama" id="namaKatField" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Deskripsi</label>
          <textarea name="deskripsi" id="deskField" class="form-control" rows="3"></textarea>
        </div>
        <div style="display:flex;gap:8px">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
          <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const baseAction = '<?= base_url() ?>';
function editKategori(id, nama, desk) {
  document.getElementById('formTitle').textContent = 'Edit Kategori';
  document.getElementById('kategoriForm').action = baseAction + 'kategori/update/' + id;
  document.getElementById('kodeField').style.display = 'none';
  document.getElementById('namaKatField').value = nama;
  document.getElementById('deskField').value = desk;
}
function resetForm() {
  document.getElementById('formTitle').textContent = 'Tambah Kategori';
  document.getElementById('kategoriForm').action = baseAction + 'kategori/simpan';
  document.getElementById('kategoriForm').reset();
  document.getElementById('kodeField').style.display = 'block';
}
</script>

<?= $this->endSection() ?>
