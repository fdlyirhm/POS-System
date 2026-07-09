<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="display:grid;grid-template-columns:1fr 320px;gap:16px">
  <!-- Riwayat Pembayaran -->
  <div>
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title"><?= esc($hutang['pelanggan']) ?></div>
          <div style="font-size:12px;color:#6c757d">
            Transaksi: <a href="/transaksi/detail/<?= $hutang['transaksi_id'] ?>" style="color:#1D9E75"><?= esc($hutang['no_transaksi'] ?? '-') ?></a>
            <?php if ($hutang['telepon']): ?> &nbsp;|&nbsp; Telp: <?= esc($hutang['telepon']) ?><?php endif; ?>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
          <span class="badge <?= $hutang['status'] === 'lunas' ? 'badge-success' : 'badge-danger' ?>" style="font-size:13px;padding:5px 12px">
            <?= $hutang['status'] === 'lunas' ? 'Lunas' : 'Belum Lunas' ?>
          </span>
          <a href="/hutang" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
      </div>

      <!-- Progress bayar -->
      <?php $pct = $hutang['total_hutang'] > 0 ? round(($hutang['total_bayar'] / $hutang['total_hutang']) * 100) : 0; ?>
      <div style="margin-bottom:20px">
        <div style="display:flex;justify-content:space-between;font-size:12px;color:#6c757d;margin-bottom:6px">
          <span>Progress Pembayaran</span>
          <span><?= $pct ?>%</span>
        </div>
        <div style="background:#f0f0f0;border-radius:6px;height:14px;overflow:hidden">
          <div style="height:14px;border-radius:6px;background:<?= $pct >= 100 ? '#1D9E75' : '#378ADD' ?>;width:<?= $pct ?>%;transition:.4s"></div>
        </div>
        <div style="display:flex;justify-content:space-between;font-size:12px;margin-top:6px">
          <span style="color:#1D9E75">Dibayar: Rp <?= number_format($hutang['total_bayar'], 0, ',', '.') ?></span>
          <span style="color:#dc3545">Sisa: Rp <?= number_format($hutang['sisa_hutang'], 0, ',', '.') ?></span>
        </div>
      </div>

      <!-- Riwayat pembayaran -->
      <div class="card-title" style="margin-bottom:12px">Riwayat Pembayaran</div>
      <?php if (empty($riwayat)): ?>
      <div style="text-align:center;padding:20px;color:#6c757d;font-size:13px">Belum ada pembayaran</div>
      <?php else: ?>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Dicatat Oleh</th>
              <th style="text-align:right">Jumlah</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($riwayat as $r): ?>
            <tr>
              <td><?= date('d M Y H:i', strtotime($r['tanggal'])) ?></td>
              <td><?= esc($r['nama_user']) ?></td>
              <td style="text-align:right;font-weight:600;color:#1D9E75">Rp <?= number_format($r['jumlah'], 0, ',', '.') ?></td>
              <td style="font-size:12px;color:#6c757d"><?= esc($r['catatan'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Panel bayar -->
  <div>
    <div class="card">
      <div class="card-title" style="margin-bottom:14px">Info Hutang</div>
      <table style="width:100%;font-size:13px;margin-bottom:16px">
        <tr><td style="color:#6c757d;padding:5px 0">Total Hutang</td><td style="font-weight:700;text-align:right">Rp <?= number_format($hutang['total_hutang'], 0, ',', '.') ?></td></tr>
        <tr><td style="color:#6c757d;padding:5px 0">Sudah Dibayar</td><td style="color:#1D9E75;font-weight:600;text-align:right">Rp <?= number_format($hutang['total_bayar'], 0, ',', '.') ?></td></tr>
        <tr><td style="color:#6c757d;padding:5px 0;border-top:1px solid #f0f0f0;padding-top:8px">Sisa Hutang</td>
            <td style="color:#dc3545;font-weight:700;font-size:16px;text-align:right;border-top:1px solid #f0f0f0;padding-top:8px">
              Rp <?= number_format($hutang['sisa_hutang'], 0, ',', '.') ?>
            </td></tr>
        <?php if ($hutang['jatuh_tempo']): ?>
        <tr><td style="color:#6c757d;padding:5px 0">Jatuh Tempo</td>
            <td style="text-align:right;color:<?= $hutang['jatuh_tempo'] < date('Y-m-d') && $hutang['status'] === 'belum_lunas' ? '#dc3545' : '#343a40' ?>;font-weight:500">
              <?= date('d M Y', strtotime($hutang['jatuh_tempo'])) ?>
            </td></tr>
        <?php endif; ?>
      </table>

      <?php if ($hutang['status'] === 'belum_lunas'): ?>
      <div style="border-top:1px solid #e9ecef;padding-top:14px">
        <div class="card-title" style="margin-bottom:12px;font-size:14px">Catat Pembayaran</div>
        <form method="POST" action="/hutang/bayar/<?= $hutang['id'] ?>">
          <?= csrf_field() ?>
          <div class="form-group">
            <label class="form-label">Jumlah Bayar (Rp)</label>
            <input type="number" name="jumlah" class="form-control" placeholder="0" min="1" max="<?= $hutang['sisa_hutang'] ?>" required>
            <div style="display:flex;gap:6px;margin-top:6px;flex-wrap:wrap">
              <button type="button" class="btn btn-secondary btn-sm" onclick="setBayar(<?= $hutang['sisa_hutang'] / 2 ?>)">½ Sisa</button>
              <button type="button" class="btn btn-secondary btn-sm" onclick="setBayar(<?= $hutang['sisa_hutang'] ?>)">Lunas Semua</button>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Catatan</label>
            <input type="text" name="catatan" class="form-control" placeholder="Catatan pembayaran...">
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
            <i class="fas fa-check"></i> Catat Pembayaran
          </button>
        </form>
      </div>
      <?php else: ?>
      <div style="background:#E1F5EE;border-radius:8px;padding:12px;text-align:center;font-size:13px;color:#065f46">
        <i class="fas fa-check-circle" style="font-size:20px;display:block;margin-bottom:6px"></i>
        Hutang sudah lunas
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function setBayar(v) {
  document.querySelector('input[name="jumlah"]').value = Math.round(v);
}
</script>

<?= $this->endSection() ?>
