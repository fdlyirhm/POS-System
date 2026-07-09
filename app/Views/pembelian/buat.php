<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="display:grid;grid-template-columns:1fr 320px;gap:16px">
  <div>
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-shopping-basket" style="color:var(--green)"></i> Form Pembelian / Restock</div>
        <a href="/pembelian" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Kembali</a>
      </div>

      <form method="POST" action="/pembelian/simpan" id="formPembelian">
        <?= csrf_field() ?>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tanggal <span style="color:red">*</span></label>
            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Supplier <span style="color:red">*</span></label>
            <select name="supplier_id" class="form-control" required>
              <option value="">-- Pilih Supplier --</option>
              <?php foreach ($supplier as $s): ?>
              <option value="<?= $s['id'] ?>"><?= esc($s['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Cari produk -->
        <div class="form-group">
          <label class="form-label">Tambah Produk</label>
          <div style="display:flex;gap:8px">
            <select id="pilihProduk" class="form-control">
              <option value="">-- Pilih produk untuk ditambahkan --</option>
              <?php foreach ($produk as $p): ?>
              <option value="<?= $p['id'] ?>" data-nama="<?= esc($p['nama']) ?>" data-harga="<?= $p['harga_beli'] ?>" data-satuan="<?= $p['satuan'] ?>">
                <?= esc($p['kode']) ?> — <?= esc($p['nama']) ?> (Stok: <?= $p['stok'] ?>)
              </option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-primary" onclick="tambahItem()"><i class="fas fa-plus"></i> Tambah</button>
          </div>
        </div>

        <!-- Tabel item -->
        <div class="table-wrapper" style="margin-bottom:14px">
          <table id="tblItem" style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
              <tr style="background:#f8f9fa; text-align:left;">
                <th style="padding:8px 10px; font-size:12px; color:#6c757d; border-bottom:1px solid #e9ecef; width:40%;">Produk</th>
                
                <th style="padding:8px 10px; font-size:12px; color:#6c757d; border-bottom:1px solid #e9ecef; width:25%;">Harga Beli (Rp)</th>
                
                <th style="padding:8px 10px; font-size:12px; color:#6c757d; border-bottom:1px solid #e9ecef; width:15%;">Qty</th>
                
                <th style="padding:8px 10px; font-size:12px; color:#6c757d; border-bottom:1px solid #e9ecef; width:15%;">Subtotal</th>
                
                <th style="padding:8px 10px; border-bottom:1px solid #e9ecef; width:5%;"></th>
              </tr>
            </thead>
            <tbody id="itemBody">
              <tr id="emptyRow"><td colspan="5" style="text-align:center;padding:20px;color:#6c757d;font-size:13px">Belum ada produk ditambahkan</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-group">
          <label class="form-label">Catatan</label>
          <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan pembelian (opsional)"></textarea>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pembelian</button>
      </form>
    </div>
  </div>

  <!-- Ringkasan -->
  <div>
    <div class="card" style="position:sticky;top:70px">
      <div class="card-title" style="margin-bottom:14px">Ringkasan</div>
      <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid #f0f0f0">
        <span>Jumlah Item</span><span id="jmlItem" style="font-weight:600">0</span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:700;padding:12px 0;color:#1D9E75">
        <span>Total</span><span id="totalDisp">Rp 0</span>
      </div>
    </div>
  </div>
</div>

<script>
let items = [];

function tambahItem() {
  const sel   = document.getElementById('pilihProduk');
  const opt   = sel.options[sel.selectedIndex];
  if (!opt.value) return;

  const id    = opt.value;
  const nama  = opt.dataset.nama;
  const harga = parseFloat(opt.dataset.harga) || 0;
  const sat   = opt.dataset.satuan;

  if (items.find(i => i.id === id)) {
    alert('Produk sudah ditambahkan'); 
    return;
  }

  items.push({ id, nama, harga, qty: 1, satuan: sat });
  renderItems(); // Render ulang tabel hanya saat nambah item
  sel.value = '';
}

function removeItem(idx) { 
  items.splice(idx, 1); 
  renderItems(); // Render ulang tabel hanya saat hapus item
}

// FUNGSI BARU: Untuk menghitung ulang total tanpa me-render ulang form
function hitungTotal() {
  const total = items.reduce((a, b) => a + (b.harga * b.qty), 0);
  document.getElementById('jmlItem').textContent   = items.length + ' item';
  document.getElementById('totalDisp').textContent = 'Rp ' + total.toLocaleString('id');
}

function updateItem(idx, field, val) {
  // Update data di dalam array
  items[idx][field] = parseFloat(val) || 0;
  
  // Hitung subtotal untuk baris ini
  const subtotal = items[idx].harga * items[idx].qty;
  
  // Ubah teks subtotal di baris ini saja tanpa mengganggu input yang sedang diketik
  const subtotalEl = document.getElementById(`subtotal-${idx}`);
  if (subtotalEl) {
    subtotalEl.textContent = 'Rp ' + subtotal.toLocaleString('id');
  }

  // Hitung ulang Grand Total di samping kanan
  hitungTotal();
}

function renderItems() {
  const tbody = document.getElementById('itemBody');
  const emptyRow = document.getElementById('emptyRow');
  if (emptyRow) emptyRow.remove();

  if (items.length === 0) {
    tbody.innerHTML = '<tr id="emptyRow"><td colspan="5" style="text-align:center;padding:20px;color:#6c757d;font-size:13px">Belum ada produk ditambahkan</td></tr>';
    hitungTotal();
    return;
  }

  tbody.innerHTML = items.map((item, i) => `
    <tr style="border-bottom:1px solid #f0f0f0">
      <td style="padding:8px 10px; font-size:13px;">
        <div style="font-weight:500">${item.nama}</div>
        <input type="hidden" name="produk_id[]" value="${item.id}">
      </td>

      <td style="padding:8px 10px;">
        <input type="number" name="harga_beli[]" class="form-control" value="${item.harga}"
          style="width:100%; max-width:180px; text-align:left;" min="0" oninput="updateItem(${i},'harga',this.value)">
      </td>

      <td style="padding:8px 10px;">
        <input type="number" name="qty[]" class="form-control" value="${item.qty}"
          style="width:100%; max-width:100px; text-align:left;" min="1" oninput="updateItem(${i},'qty',this.value)">
      </td>

      <td id="subtotal-${i}" style="padding:8px 10px; font-weight:600;">
        Rp ${(item.harga * item.qty).toLocaleString('id')}
      </td>
      
      <td style="padding:8px 10px; text-align:center;">
        <button type="button" onclick="removeItem(${i})" style="background:none; border:none; cursor:pointer; color:#dc3545;">
          <i class="fas fa-times"></i>
        </button>
      </td>
    </tr>
  `).join('');

  hitungTotal();
}
</script>

<?= $this->endSection() ?>
