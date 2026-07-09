<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="display:grid;grid-template-columns:1fr 320px;gap:16px">

  <!-- Form Pengaturan -->
  <div>
    <form method="POST" action="/pengaturan/simpan">
      <?= csrf_field() ?>

      <!-- Identitas Toko -->
      <div class="card">
        <div class="card-title" style="margin-bottom:16px"><i class="fas fa-store" style="color:var(--green)"></i> Identitas Toko</div>
        <div class="form-group">
          <label class="form-label">Nama Toko</label>
          <input type="text" name="nama_toko" class="form-control" value="<?= esc($pengaturan['nama_toko'] ?? '') ?>" placeholder="Toko Sembako Berkah">
          <div style="font-size:11px;color:#6c757d;margin-top:3px">Muncul di title browser, sidebar, dan header struk cetak</div>
        </div>
        <div class="form-group">
          <label class="form-label">Alamat</label>
          <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. ..."><?= esc($pengaturan['alamat'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Nomor Telepon</label>
            <input type="text" name="telepon" class="form-control" value="<?= esc($pengaturan['telepon'] ?? '') ?>" placeholder="021-xxxxxxxx">
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= esc($pengaturan['email'] ?? '') ?>" placeholder="toko@email.com">
          </div>
        </div>
      </div>

      <!-- Konfigurasi Transaksi -->
      <div class="card">
        <div class="card-title" style="margin-bottom:16px"><i class="fas fa-calculator" style="color:var(--green)"></i> Konfigurasi Transaksi</div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">PPN / Pajak (%)</label>
            <input type="number" name="pajak_persen" class="form-control" value="<?= esc($pengaturan['pajak_persen'] ?? '11') ?>" min="0" max="100" step="0.5">
            <div style="font-size:11px;color:#6c757d;margin-top:3px">
              Berlaku di Kasir POS, struk cetak, dan semua laporan. Isi <strong>0</strong> untuk nonaktifkan pajak.
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Simbol Mata Uang</label>
            <input type="text" name="mata_uang" class="form-control" value="<?= esc($pengaturan['mata_uang'] ?? 'Rp') ?>" placeholder="Rp">
            <div style="font-size:11px;color:#6c757d;margin-top:3px">Muncul di struk cetak</div>
          </div>
        </div>
      </div>

      <!-- Jam Operasional -->
      <div class="card">
        <div class="card-title" style="margin-bottom:16px"><i class="fas fa-clock" style="color:var(--green)"></i> Jam Operasional</div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Jam Buka</label>
            <input type="time" name="buka_jam" class="form-control" value="<?= esc($pengaturan['buka_jam'] ?? '07:00') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Jam Tutup</label>
            <input type="time" name="tutup_jam" class="form-control" value="<?= esc($pengaturan['tutup_jam'] ?? '21:00') ?>">
          </div>
        </div>
        <div style="font-size:11px;color:#6c757d">Ditampilkan di topbar semua halaman</div>
      </div>

      <!-- Struk -->
      <div class="card">
        <div class="card-title" style="margin-bottom:16px"><i class="fas fa-receipt" style="color:var(--green)"></i> Pengaturan Struk / Nota</div>
        <div class="form-group">
          <label class="form-label">Teks Footer Struk</label>
          <textarea name="nota_footer" class="form-control" rows="2" placeholder="Terima kasih atas kunjungan Anda!"><?= esc($pengaturan['nota_footer'] ?? '') ?></textarea>
          <div style="font-size:11px;color:#6c757d;margin-top:3px">Muncul di bagian bawah struk thermal setiap transaksi</div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="padding:11px 24px">
        <i class="fas fa-save"></i> Simpan Semua Pengaturan
      </button>
    </form>
  </div>

  <!-- Info dampak perubahan -->
  <div>
    <div class="card" style="position:sticky;top:70px">
      <div class="card-title" style="margin-bottom:14px"><i class="fas fa-info-circle" style="color:#378ADD"></i> Dampak Perubahan</div>

      <div style="font-size:13px;color:#6c757d;margin-bottom:14px">
        Pengaturan ini terintegrasi ke seluruh modul aplikasi:
      </div>

      <div style="display:flex;flex-direction:column;gap:10px">
        <?php
        $dampak = [
          ['icon'=>'fa-store',        'color'=>'#1D9E75', 'modul'=>'Nama Toko',       'desc'=>'Sidebar, title browser, struk cetak'],
          ['icon'=>'fa-cash-register','color'=>'#378ADD', 'modul'=>'PPN/Pajak',        'desc'=>'Kasir POS, struk, laporan harian & bulanan'],
          ['icon'=>'fa-receipt',      'color'=>'#e67e22', 'modul'=>'Struk Cetak',      'desc'=>'Nama toko, alamat, telepon, footer, PPN'],
          ['icon'=>'fa-chart-bar',    'color'=>'#9b59b6', 'modul'=>'Laporan',          'desc'=>'Kolom PPN dinamis sesuai persentase'],
          ['icon'=>'fa-clock',        'color'=>'#6c757d', 'modul'=>'Jam Operasional',  'desc'=>'Ditampilkan di topbar semua halaman'],
        ];
        ?>
        <?php foreach ($dampak as $d): ?>
        <div style="display:flex;align-items:flex-start;gap:10px;padding:10px;background:#f8f9fa;border-radius:8px">
          <div style="width:32px;height:32px;border-radius:8px;background:<?= $d['color'] ?>22;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="fas <?= $d['icon'] ?>" style="font-size:14px;color:<?= $d['color'] ?>"></i>
          </div>
          <div>
            <div style="font-size:13px;font-weight:600;color:#343a40"><?= $d['modul'] ?></div>
            <div style="font-size:11px;color:#6c757d;margin-top:2px"><?= $d['desc'] ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Preview nilai saat ini -->
      <div style="margin-top:16px;border-top:1px solid #e9ecef;padding-top:14px">
        <div style="font-size:12px;font-weight:600;color:#6c757d;margin-bottom:10px">NILAI SAAT INI</div>
        <?php
        $preview = [
          'Nama Toko'   => $pengaturan['nama_toko']   ?? '-',
          'PPN'         => ($pengaturan['pajak_persen'] ?? '11') . '%',
          'Jam Buka'    => $pengaturan['buka_jam']    ?? '-',
          'Jam Tutup'   => $pengaturan['tutup_jam']   ?? '-',
          'Mata Uang'   => $pengaturan['mata_uang']   ?? 'Rp',
        ];
        ?>
        <?php foreach ($preview as $k => $v): ?>
        <div style="display:flex;justify-content:space-between;font-size:12px;padding:4px 0;border-bottom:1px solid #f0f0f0">
          <span style="color:#6c757d"><?= $k ?></span>
          <span style="font-weight:600"><?= esc($v) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
