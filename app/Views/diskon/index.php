<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="display:grid;grid-template-columns:1fr 380px;gap:16px">
  <!-- Daftar Diskon -->
  <div class="card">
    <div class="card-header">
      <div class="card-title"><i class="fas fa-tags" style="color:var(--green)"></i> Daftar Diskon & Promo</div>
    </div>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Promo</th>
            <th>Diskon</th>
            <th>Min. Belanja</th>
            <th>Berlaku</th>
            <th>Kuota</th>
            <th>Status</th>
            <th style="text-align:center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($diskon)): ?>
          <tr><td colspan="8" style="text-align:center;padding:24px;color:#6c757d">Belum ada data diskon</td></tr>
          <?php else: ?>
          <?php foreach ($diskon as $d): ?>
          <?php
            $expired    = $d['berlaku_sampai'] < date('Y-m-d');
            $habis      = $d['kuota'] !== null && $d['terpakai'] >= $d['kuota'];
            $statusReal = $d['status'] === 'nonaktif' ? 'nonaktif' : ($expired ? 'expired' : ($habis ? 'habis' : 'aktif'));
          ?>
          <tr>
            <td><span style="font-family:monospace;font-weight:700;color:#1D9E75"><?= esc($d['kode']) ?></span></td>
            <td><?= esc($d['nama']) ?></td>
            <td style="font-weight:600">
              <?= $d['tipe'] === 'persen' ? $d['nilai'] . '%' : 'Rp ' . number_format($d['nilai'], 0, ',', '.') ?>
            </td>
            <td>Rp <?= number_format($d['min_belanja'], 0, ',', '.') ?></td>
            <td style="font-size:12px">
              <?= date('d M Y', strtotime($d['berlaku_dari'])) ?><br>
              <span style="color:#6c757d">s/d <?= date('d M Y', strtotime($d['berlaku_sampai'])) ?></span>
            </td>
            <td style="text-align:center">
              <?php if ($d['kuota'] !== null): ?>
                <span style="font-size:12px"><?= $d['terpakai'] ?> / <?= $d['kuota'] ?></span>
                <div style="background:#f0f0f0;border-radius:4px;height:6px;margin-top:3px">
                  <div style="height:6px;border-radius:4px;background:#1D9E75;width:<?= round(($d['terpakai']/$d['kuota'])*100) ?>%"></div>
                </div>
              <?php else: ?>
                <span style="color:#6c757d;font-size:12px">Tidak terbatas</span>
              <?php endif; ?>
            </td>
            <td>
              <?php
                $badgeClass = match($statusReal) {
                  'aktif'    => 'badge-success',
                  'expired'  => 'badge-danger',
                  'habis'    => 'badge-warning',
                  default    => 'badge-danger',
                };
              ?>
              <span class="badge <?= $badgeClass ?>"><?= ucfirst($statusReal) ?></span>
            </td>
            <td style="text-align:center">
              <button class="btn btn-secondary btn-sm"
                onclick="editDiskon(<?= $d['id'] ?>,'<?= addslashes($d['nama']) ?>',<?= $d['nilai'] ?>,<?= $d['min_belanja'] ?>,'<?= $d['berlaku_dari'] ?>','<?= $d['berlaku_sampai'] ?>',<?= $d['kuota'] ?? 'null' ?>,'<?= $d['status'] ?>')">
                <i class="fas fa-edit"></i>
              </button>
              <form method="POST" action="/diskon/hapus/<?= $d['id'] ?>" style="display:inline" onsubmit="return confirm('Nonaktifkan diskon ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Form Tambah/Edit -->
  <div>
    <div class="card">
      <div class="card-title" style="margin-bottom:14px" id="diskonFormTitle">Tambah Diskon Baru</div>

      <?php if (session()->getFlashdata('errors')): ?>
      <div class="alert alert-danger">
        <?php foreach (session()->getFlashdata('errors') as $e): ?><div><?= $e ?></div><?php endforeach; ?>
      </div>
      <?php endif; ?>

      <form method="POST" id="diskonForm" action="/diskon/simpan">
        <?= csrf_field() ?>

        <div class="form-group" id="kodeGroup">
          <label class="form-label">Kode Promo <span style="color:red">*</span></label>
          <input type="text" name="kode" id="diskonKode" class="form-control" placeholder="DISC10" style="text-transform:uppercase" required>
          <div style="font-size:11px;color:#6c757d;margin-top:3px">Kode yang diketik pelanggan di kasir</div>
        </div>

        <div class="form-group">
          <label class="form-label">Nama Promo <span style="color:red">*</span></label>
          <input type="text" name="nama" id="diskonNama" class="form-control" required>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tipe Diskon</label>
            <select name="tipe" id="diskonTipe" class="form-control" onchange="toggleTipe()">
              <option value="persen">Persentase (%)</option>
              <option value="nominal">Nominal (Rp)</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" id="nilaiLabel">Nilai (%) <span style="color:red">*</span></label>
            <input type="number" name="nilai" id="diskonNilai" class="form-control" placeholder="10" min="0" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Minimum Belanja (Rp)</label>
          <input type="number" name="min_belanja" id="diskonMinBelanja" class="form-control" placeholder="0" value="0" min="0">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Berlaku Dari <span style="color:red">*</span></label>
            <input type="date" name="berlaku_dari" id="diskonDari" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Berlaku Sampai <span style="color:red">*</span></label>
            <input type="date" name="berlaku_sampai" id="diskonSampai" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Kuota Penggunaan <span style="font-size:11px;color:#6c757d">(kosongkan = tidak terbatas)</span></label>
          <input type="number" name="kuota" id="diskonKuota" class="form-control" placeholder="Contoh: 100" min="1">
        </div>

        <div class="form-group" id="statusGroup" style="display:none">
          <label class="form-label">Status</label>
          <select name="status" id="diskonStatus" class="form-control">
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>

        <div style="display:flex;gap:8px">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
          <button type="button" class="btn btn-secondary" onclick="resetDiskonForm()">Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const baseUrl = '<?= base_url() ?>';

function toggleTipe() {
  const tipe  = document.getElementById('diskonTipe').value;
  document.getElementById('nilaiLabel').textContent = tipe === 'persen' ? 'Nilai (%) *' : 'Nilai (Rp) *';
}

function editDiskon(id, nama, nilai, minBelanja, dari, sampai, kuota, status) {
  document.getElementById('diskonFormTitle').textContent = 'Edit Diskon';
  document.getElementById('diskonForm').action = baseUrl + 'diskon/update/' + id;
  document.getElementById('kodeGroup').style.display    = 'none';
  document.getElementById('statusGroup').style.display  = 'block';
  document.getElementById('diskonNama').value      = nama;
  document.getElementById('diskonNilai').value     = nilai;
  document.getElementById('diskonMinBelanja').value = minBelanja;
  document.getElementById('diskonDari').value      = dari;
  document.getElementById('diskonSampai').value    = sampai;
  document.getElementById('diskonKuota').value     = kuota === null ? '' : kuota;
  document.getElementById('diskonStatus').value    = status;
  window.scrollTo(0, 0);
}

function resetDiskonForm() {
  document.getElementById('diskonFormTitle').textContent = 'Tambah Diskon Baru';
  document.getElementById('diskonForm').action = baseUrl + 'diskon/simpan';
  document.getElementById('diskonForm').reset();
  document.getElementById('kodeGroup').style.display   = 'block';
  document.getElementById('statusGroup').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
