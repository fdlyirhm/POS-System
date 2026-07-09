<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-clipboard-list" style="color:var(--green)"></i> Buat Stok Opname Baru</div>
    <a href="/stok-opname" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
  </div>

  <form method="POST" action="/stok-opname/simpan">
    <?= csrf_field() ?>

    <div class="form-row" style="margin-bottom:16px">
      <div class="form-group">
        <label class="form-label">Tanggal Opname</label>
        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Catatan</label>
        <input type="text" name="catatan" class="form-control" placeholder="Catatan opname (opsional)">
      </div>
    </div>

    <!-- Filter & Cari -->
    <div style="display:flex;gap:10px;margin-bottom:14px;align-items:center">
      <input type="text" id="filterProduk" class="form-control" placeholder="Filter nama produk..." style="max-width:280px" oninput="filterTable()">
      <button type="button" class="btn btn-secondary btn-sm" onclick="isiStokSistem()"><i class="fas fa-magic"></i> Salin Stok Sistem ke Fisik</button>
    </div>

    <div class="table-wrapper">
      <table id="tblOpname">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th style="text-align:center">Stok Sistem</th>
            <th style="text-align:center">Stok Fisik</th>
            <th style="text-align:center">Selisih</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($produk as $i => $p): ?>
          <tr>
            <td>
              <?= esc($p['kode']) ?>
              <input type="hidden" name="produk_id[]" value="<?= $p['id'] ?>">
            </td>
            <td><?= esc($p['nama']) ?></td>
            <td><?= esc($p['nama_kategori']) ?></td>
            <td style="text-align:center;font-weight:600" class="stok-sistem"><?= $p['stok'] ?></td>
            <td style="text-align:center">
              <input type="number" name="stok_fisik[]" class="form-control stok-fisik" min="0"
                value="<?= $p['stok'] ?>"
                data-sistem="<?= $p['stok'] ?>"
                style="width:90px;text-align:center;padding:5px 8px"
                oninput="hitungSelisih(this)">
            </td>
            <td style="text-align:center;font-weight:700" class="selisih" id="selisih-<?= $i ?>">0</td>
            <td>
              <input type="text" name="keterangan_item[]" class="form-control" placeholder="Keterangan..." style="font-size:12px;padding:5px 8px">
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div style="margin-top:16px;display:flex;gap:10px">
      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Opname</button>
      <a href="/stok-opname" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>

<script>
function hitungSelisih(input) {
  const sistem  = parseInt(input.dataset.sistem) || 0;
  const fisik   = parseInt(input.value) || 0;
  const selisih = fisik - sistem;
  const td      = input.closest('tr').querySelector('.selisih');
  td.textContent = (selisih > 0 ? '+' : '') + selisih;
  td.style.color = selisih > 0 ? '#1D9E75' : (selisih < 0 ? '#dc3545' : '#6c757d');
}

function isiStokSistem() {
  document.querySelectorAll('.stok-fisik').forEach(input => {
    input.value = input.dataset.sistem;
    hitungSelisih(input);
  });
}

function filterTable() {
  const q = document.getElementById('filterProduk').value.toLowerCase();
  document.querySelectorAll('#tblOpname tbody tr').forEach(tr => {
    const nama = tr.cells[1].textContent.toLowerCase();
    tr.style.display = nama.includes(q) ? '' : 'none';
  });
}

// Hitung semua selisih saat halaman load
document.querySelectorAll('.stok-fisik').forEach(hitungSelisih);
</script>

<?= $this->endSection() ?>
