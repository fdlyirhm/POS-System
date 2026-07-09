<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.pos-wrap{display:grid;grid-template-columns:1fr 340px;gap:16px}
.cam-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:9998;align-items:center;justify-content:center}
.cam-overlay.show{display:flex}
.cam-modal{background:#fff;border-radius:16px;padding:20px;width:460px;max-width:95vw}
.cam-viewport{width:100%;height:280px;background:#111;border-radius:10px;overflow:hidden;position:relative}
.cam-viewport video{width:100%;height:100%;object-fit:cover;display:block}
.scan-line{position:absolute;left:5%;right:5%;height:2.5px;background:#1D9E75;box-shadow:0 0 6px #1D9E75;animation:scanAnim 2s ease-in-out infinite;pointer-events:none}
@keyframes scanAnim{0%,100%{top:15%}50%{top:82%}}
.cam-corner{position:absolute;width:20px;height:20px;border-color:#1D9E75;border-style:solid}
.cam-corner.tl{top:8px;left:8px;border-width:3px 0 0 3px}
.cam-corner.tr{top:8px;right:8px;border-width:3px 3px 0 0}
.cam-corner.bl{bottom:8px;left:8px;border-width:0 0 3px 3px}
.cam-corner.br{bottom:8px;right:8px;border-width:0 3px 3px 0}
.cart-table td,.cart-table th{padding:8px 10px}
.qty-ctrl{display:flex;align-items:center;gap:5px}
.qty-btn{width:26px;height:26px;border-radius:6px;border:1px solid #ced4da;background:#fff;cursor:pointer;font-size:15px;font-weight:700;line-height:1;display:flex;align-items:center;justify-content:center}
.pay-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.pay-option{padding:11px 6px;border:1px solid #ced4da;border-radius:8px;text-align:center;cursor:pointer;font-size:12px;color:#6c757d;transition:.15s}
.pay-option:hover{border-color:#1D9E75}
.pay-option.active{border:2px solid #1D9E75;background:#E1F5EE;color:#0F6E56;font-weight:600}
.pay-icon{font-size:18px;margin-bottom:3px}
.sum-row{display:flex;justify-content:space-between;font-size:13px;padding:5px 0}
.total-row{display:flex;justify-content:space-between;font-size:17px;font-weight:700;padding:10px 0;border-top:1px solid #e9ecef}
.found-box{background:#E1F5EE;border:1px solid #6ee7b7;border-radius:8px;padding:12px;display:flex;align-items:center;gap:12px;margin-top:10px}
.notfound-box{background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:10px;text-align:center;font-size:12px;color:#991b1b;margin-top:8px}
</style>

<!-- ===== Camera Overlay ===== -->
<div class="cam-overlay" id="camOverlay">
  <div class="cam-modal">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
      <div style="font-size:15px;font-weight:700"><i class="fas fa-barcode" style="color:#1D9E75"></i> Scanner Kamera</div>
      <button type="button" onclick="closeCamera()" style="background:none;border:none;cursor:pointer;font-size:22px;color:#6c757d;line-height:1">&times;</button>
    </div>

    <div class="cam-viewport" id="camViewport">
      <div class="cam-corner tl"></div><div class="cam-corner tr"></div>
      <div class="cam-corner bl"></div><div class="cam-corner br"></div>
      <div class="scan-line" id="scanLine" style="display:none"></div>
      <div id="camPlaceholder" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:#888;gap:10px">
        <i class="fas fa-camera" style="font-size:48px"></i>
        <span style="font-size:12px;color:#aaa">Memulai kamera...</span>
      </div>
      <video id="camVideo" autoplay playsinline muted style="display:none"></video>
      <canvas id="camCanvas" style="display:none"></canvas>
    </div>

    <div id="camStatus" style="text-align:center;font-size:13px;color:#6c757d;margin:10px 0 0;min-height:20px">Menginisialisasi...</div>

    <div style="display:flex;gap:8px;margin-top:12px">
      <button type="button" class="btn btn-secondary" style="flex:1;justify-content:center" onclick="switchCamera()"><i class="fas fa-sync-alt"></i> Ganti Kamera</button>
      <button type="button" class="btn btn-danger"    style="flex:1;justify-content:center" onclick="closeCamera()"><i class="fas fa-times"></i> Tutup</button>
    </div>

    <div id="camResult" style="display:none;margin-top:12px;background:#f0fff8;border:1px solid #6ee7b7;border-radius:8px;padding:12px">
      <div style="font-size:11px;color:#6c757d;margin-bottom:4px">Barcode terdeteksi:</div>
      <div style="font-family:monospace;font-size:15px;font-weight:700;color:#065f46" id="camDetectedCode"></div>
      <div style="font-size:13px;color:#065f46;margin-top:3px" id="camDetectedName"></div>
      <button type="button" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:10px" onclick="konfirmasiScan()">
        <i class="fas fa-plus"></i> Tambah ke Keranjang
      </button>
    </div>
  </div>
</div>

<div class="pos-wrap">
  <!-- ===== KIRI ===== -->
  <div>
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-barcode" style="color:var(--green)"></i> Scan Produk</div>
        <span style="font-size:12px;color:#6c757d;font-weight:600"><?= date('d M Y H:i') ?></span>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        <!-- Kamera -->
        <div>
          <div class="form-label">Kamera (scan otomatis)</div>
          <div style="border:1px solid #ced4da;border-radius:10px;background:#1a1a2e;height:160px;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;position:relative;gap:6px" onclick="openCamera()">
            <div style="position:absolute;top:8px;left:8px;width:16px;height:16px;border-top:2px solid #1D9E75;border-left:2px solid #1D9E75"></div>
            <div style="position:absolute;top:8px;right:8px;width:16px;height:16px;border-top:2px solid #1D9E75;border-right:2px solid #1D9E75"></div>
            <div style="position:absolute;bottom:8px;left:8px;width:16px;height:16px;border-bottom:2px solid #1D9E75;border-left:2px solid #1D9E75"></div>
            <div style="position:absolute;bottom:8px;right:8px;width:16px;height:16px;border-bottom:2px solid #1D9E75;border-right:2px solid #1D9E75"></div>
            <i class="fas fa-camera" style="font-size:32px;color:#5DCAA5"></i>
            <span style="font-size:11px;color:#9FE1CB">Klik untuk scan</span>
          </div>
          <button type="button" class="btn btn-secondary" style="width:100%;margin-top:8px;justify-content:center;font-size:12px" onclick="openCamera()">
            <i class="fas fa-camera"></i> Buka Scanner
          </button>
        </div>

        <!-- Manual -->
        <div>
          <div class="form-label">Input barcode / kode</div>
          <div style="display:flex;gap:8px;margin-bottom:8px">
            <input type="text" id="barcodeInput" class="form-control" placeholder="Scan / ketik barcode..."
              onkeydown="if(event.key==='Enter'){event.preventDefault();cariBarcode()}">
            <button type="button" class="btn btn-primary" onclick="cariBarcode()"><i class="fas fa-search"></i></button>
          </div>
          <div class="form-label">Cari nama produk</div>
          <input type="text" id="namaInput" class="form-control" placeholder="Nama produk..." oninput="debounceSearch()" style="margin-bottom:6px">
          <div id="searchResults" style="display:none;border:1px solid #ced4da;border-radius:8px;background:#fff;max-height:150px;overflow-y:auto;z-index:10;position:relative"></div>

          <div id="foundBox" class="found-box" style="display:none">
            <div style="width:40px;height:40px;background:#1D9E75;border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:700;flex-shrink:0" id="foundIcon">-</div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13px;font-weight:600;color:#065f46;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" id="foundNama">-</div>
              <div style="font-size:11px;color:#059669" id="foundKode">-</div>
              <div style="font-size:12px;color:#065f46">Rp <span id="foundHarga">-</span> / <span id="foundSatuan">-</span></div>
              <div style="font-size:11px;color:#6c757d">Stok: <span id="foundStok">-</span></div>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="addToCart()"><i class="fas fa-plus"></i></button>
          </div>
          <div id="notFound" class="notfound-box" style="display:none"><i class="fas fa-exclamation-triangle"></i> Produk tidak ditemukan</div>
        </div>
      </div>
    </div>

    <!-- Keranjang -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="fas fa-shopping-cart" style="color:var(--green)"></i> Keranjang (<span id="cartCount">0</span>)</div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="clearCart()"><i class="fas fa-trash"></i> Kosongkan</button>
      </div>
      <div class="table-wrapper">
        <table class="cart-table" style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:#f8f9fa">
              <th style="border-bottom:1px solid #e9ecef">Produk</th>
              <th style="border-bottom:1px solid #e9ecef">Harga</th>
              <th style="border-bottom:1px solid #e9ecef">Qty</th>
              <th style="border-bottom:1px solid #e9ecef">Subtotal</th>
              <th style="border-bottom:1px solid #e9ecef"></th>
            </tr>
          </thead>
          <tbody id="cartBody">
            <tr><td colspan="5" style="text-align:center;padding:24px;color:#6c757d;font-size:13px"><i class="fas fa-shopping-cart"></i> Keranjang kosong</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- ===== KANAN ===== -->
  <div>
    <div class="card" style="position:sticky;top:70px">
      <div class="card-title" style="margin-bottom:14px"><i class="fas fa-receipt" style="color:var(--green)"></i> Pembayaran</div>

      <div class="form-group">
        <label class="form-label">Nama Pelanggan</label>
        <input type="text" id="pelanggan" class="form-control" value="Umum">
      </div>

      <!-- Kode Diskon -->
      <div class="form-group">
        <label class="form-label">Kode Diskon <span style="font-size:11px;color:#6c757d">(opsional)</span></label>
        <div style="display:flex;gap:8px">
          <input type="text" id="kodeDiskon" class="form-control" placeholder="Masukkan kode..." style="text-transform:uppercase">
          <button type="button" class="btn btn-secondary" onclick="cekDiskon()"><i class="fas fa-tag"></i></button>
          <button type="button" id="btnResetDiskon" class="btn btn-danger btn-sm" onclick="resetDiskon()" style="display:none" title="Hapus diskon"><i class="fas fa-times"></i></button>
        </div>
        <div id="diskonInfo" style="display:none;font-size:12px;margin-top:5px;padding:6px 10px;border-radius:6px"></div>
      </div>

      <!-- Ringkasan -->
      <div style="margin-bottom:14px">
        <div class="sum-row"><span>Subtotal</span><span id="subtotal">Rp 0</span></div>
        <div class="sum-row"><span style="color:#065f46">Diskon</span><span style="color:#065f46" id="diskonDisp">- Rp 0</span></div>
        <div class="sum-row"><span>PPN (<span id="pajakLabel"><?= $pajak_persen ?></span>%)</span><span id="pajak">Rp 0</span></div>
        <div class="total-row"><span>TOTAL</span><span id="total" style="color:#1D9E75">Rp 0</span></div>
      </div>

      <!-- Metode Bayar -->
      <div class="form-group">
        <label class="form-label">Metode Pembayaran</label>
        <div class="pay-grid">
          <div class="pay-option active" onclick="selectPay(this,'tunai')"><div class="pay-icon">💵</div>Tunai</div>
          <div class="pay-option" onclick="selectPay(this,'qris')"><div class="pay-icon">📱</div>QRIS</div>
          <div class="pay-option" onclick="selectPay(this,'debit')"><div class="pay-icon">💳</div>Debit</div>
          <div class="pay-option" onclick="selectPay(this,'kredit')"><div class="pay-icon">📝</div>Kredit</div>
        </div>
      </div>

      <!-- Tunai -->
      <div id="tunaiSection">
        <div class="form-group">
          <label class="form-label">Uang Diterima</label>
          <input type="number" id="bayar" class="form-control" placeholder="0" oninput="hitungKembalian()">
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-top:6px">
            <button type="button" class="btn btn-secondary btn-sm" style="justify-content:center" onclick="setBayar(50000)">50rb</button>
            <button type="button" class="btn btn-secondary btn-sm" style="justify-content:center" onclick="setBayar(100000)">100rb</button>
            <button type="button" class="btn btn-secondary btn-sm" style="justify-content:center" onclick="setBayar(200000)">200rb</button>
          </div>
        </div>
        <div class="sum-row" style="font-weight:600">
          <span>Kembalian</span>
          <span id="kembalian" style="color:#1D9E75;font-size:16px">Rp 0</span>
        </div>
      </div>

      <!-- Kredit: telepon + jatuh tempo -->
      <div id="kreditSection" style="display:none">
        <div style="background:#fff8e1;border:1px solid #ffc107;border-radius:8px;padding:10px 12px;margin-bottom:10px;font-size:12px;color:#856404">
          <i class="fas fa-exclamation-triangle"></i> Transaksi kredit akan otomatis dicatat sebagai hutang pelanggan.
        </div>
        <div class="form-group">
          <label class="form-label">Nomor Telepon Pelanggan</label>
          <input type="text" id="teleponKredit" class="form-control" placeholder="08xxxxxxxxxx">
        </div>
        <div class="form-group">
          <label class="form-label">Jatuh Tempo <span style="font-size:11px;color:#6c757d">(opsional)</span></label>
          <input type="date" id="jatuhTempo" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
        </div>
      </div>

      <button type="button" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:15px;margin-top:12px" onclick="prosesTransaksi()">
        <i class="fas fa-check-circle"></i> Proses Pembayaran
      </button>
      <button type="button" class="btn btn-secondary" style="width:100%;justify-content:center;margin-top:8px" onclick="clearCart()">
        <i class="fas fa-times"></i> Reset
      </button>
    </div>
  </div>
</div>

<!-- Modal Sukses -->
<div id="modalSukses" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;padding:32px;text-align:center;max-width:360px;width:90%">
    <div style="width:64px;height:64px;background:#E1F5EE;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
      <i class="fas fa-check-circle" style="font-size:32px;color:#1D9E75"></i>
    </div>
    <div style="font-size:18px;font-weight:700;margin-bottom:6px">Transaksi Berhasil!</div>
    <div style="font-size:13px;color:#6c757d;margin-bottom:4px" id="modalNoTrx"></div>
    <div id="modalKembalianWrap">
      <div style="font-size:24px;font-weight:700;color:#1D9E75;margin-bottom:4px" id="modalKembalian"></div>
      <div style="font-size:13px;color:#6c757d;margin-bottom:16px">Kembalian</div>
    </div>
    <div id="modalKreditWrap" style="display:none;background:#fff8e1;border-radius:8px;padding:10px;margin-bottom:16px;font-size:13px;color:#856404">
      <i class="fas fa-file-invoice-dollar"></i> Hutang dicatat di menu <strong>Hutang Pelanggan</strong>
    </div>
    <div style="display:flex;gap:10px;justify-content:center">
      <button class="btn btn-secondary" onclick="closeModal()"><i class="fas fa-plus"></i> Transaksi Baru</button>
      <button class="btn btn-primary" id="btnStruk"><i class="fas fa-print"></i> Cetak Struk</button>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
const baseUrl     = '<?= base_url() ?>';
const PAJAK_PCT   = <?= $pajak_persen ?>; // dari pengaturan DB

let cart          = [];
let currentProduct= null;
let payMethod     = 'tunai';
let lastTrxId     = null;
let searchTimer   = null;
let diskonAktif   = null;
let diskonNominal = 0;

// =============================================
// CAMERA
// =============================================
let cameraStream  = null;
let cameraFacing  = 'environment';
let scanInterval  = null;
let lastDetected  = '';
let detectedProd  = null;

async function openCamera() {
  document.getElementById('camOverlay').classList.add('show');
  document.getElementById('camResult').style.display = 'none';
  lastDetected = ''; detectedProd = null;
  await startStream();
}

async function startStream() {
  stopStream();
  const video  = document.getElementById('camVideo');
  const ph     = document.getElementById('camPlaceholder');
  const line   = document.getElementById('scanLine');
  const status = document.getElementById('camStatus');

  status.textContent = 'Menginisialisasi kamera...';
  ph.style.display = 'flex'; video.style.display = 'none'; line.style.display = 'none';

  try {
    cameraStream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: cameraFacing, width: { ideal: 1280 }, height: { ideal: 720 } }
    });
    video.srcObject = cameraStream;
    video.onloadedmetadata = () => {
      ph.style.display = 'none';
      video.style.display = 'block';
      line.style.display = 'block';
      status.textContent = 'Arahkan barcode ke area scan...';
      startFrameScan();
    };
  } catch(err) {
    ph.innerHTML = '<i class="fas fa-camera-slash" style="font-size:40px;color:#dc3545"></i><span style="font-size:12px;color:#dc3545;margin-top:8px">Gagal akses kamera: ' + err.message + '</span>';
    status.textContent = 'Gunakan input manual di bawah.';
  }
}

function startFrameScan() {
  const video  = document.getElementById('camVideo');
  const canvas = document.getElementById('camCanvas');
  const ctx    = canvas.getContext('2d');
  scanInterval = setInterval(() => {
    if (!cameraStream || video.readyState < 2) return;
    canvas.width  = video.videoWidth  || 640;
    canvas.height = video.videoHeight || 480;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    Quagga.decodeSingle({
      decoder: { readers: ['ean_reader','ean_8_reader','code_128_reader','code_39_reader','upc_reader'] },
      locate: true, src: canvas.toDataURL('image/png'),
    }, result => {
      if (result?.codeResult?.code && result.codeResult.code !== lastDetected) {
        lastDetected = result.codeResult.code;
        onDetected(result.codeResult.code);
      }
    });
  }, 600);
}

async function onDetected(code) {
  document.getElementById('camStatus').textContent = 'Barcode: ' + code + ' — Mencari...';
  try { const a = new AudioContext(), o = a.createOscillator(), g = a.createGain(); o.connect(g); g.connect(a.destination); g.gain.value=.3; o.frequency.value=880; o.start(); o.stop(a.currentTime+.08); } catch(e){}
  try {
    const res  = await fetch(baseUrl + 'api/produk/barcode/' + encodeURIComponent(code));
    const json = await res.json();
    if (json.status) {
      detectedProd = json.data;
      document.getElementById('camDetectedCode').textContent = code;
      document.getElementById('camDetectedName').textContent = json.data.nama + ' — Rp ' + Number(json.data.harga_jual).toLocaleString('id');
      document.getElementById('camResult').style.display = 'block';
      document.getElementById('camStatus').textContent   = '✓ Produk ditemukan!';
      clearInterval(scanInterval); scanInterval = null;
    } else {
      document.getElementById('camStatus').textContent = 'Produk "' + code + '" tidak ditemukan. Scan ulang...';
      setTimeout(() => { lastDetected = ''; }, 2000);
    }
  } catch(e) {
    setTimeout(() => { lastDetected = ''; }, 2000);
  }
}

function konfirmasiScan() {
  if (!detectedProd) return;
  addProductToCart(detectedProd);
  closeCamera();
  showToast('Ditambahkan: ' + detectedProd.nama);
}

async function switchCamera() {
  cameraFacing = cameraFacing === 'environment' ? 'user' : 'environment';
  document.getElementById('camResult').style.display = 'none';
  lastDetected = ''; detectedProd = null;
  clearInterval(scanInterval); scanInterval = null;
  await startStream();
}

function closeCamera() {
  stopStream();
  document.getElementById('camOverlay').classList.remove('show');
  document.getElementById('camResult').style.display = 'none';
  lastDetected = ''; detectedProd = null;
}

function stopStream() {
  clearInterval(scanInterval); scanInterval = null;
  if (cameraStream) { cameraStream.getTracks().forEach(t => t.stop()); cameraStream = null; }
  const video = document.getElementById('camVideo');
  video.srcObject = null; video.style.display = 'none';
  document.getElementById('scanLine').style.display = 'none';
  const ph = document.getElementById('camPlaceholder');
  ph.style.display = 'flex';
  ph.innerHTML = '<i class="fas fa-camera" style="font-size:48px"></i><span style="font-size:12px;color:#aaa;margin-top:8px">Memulai kamera...</span>';
}

document.getElementById('camOverlay').addEventListener('click', e => { if (e.target === e.currentTarget) closeCamera(); });

// =============================================
// BARCODE MANUAL
// =============================================
async function cariBarcode() {
  const code = document.getElementById('barcodeInput').value.trim();
  hideBoxes();
  if (!code) return;
  const res  = await fetch(baseUrl + 'api/produk/barcode/' + encodeURIComponent(code));
  const json = await res.json();
  json.status ? tampilkanProduk(json.data) : (document.getElementById('notFound').style.display = 'block');
}

function debounceSearch() { clearTimeout(searchTimer); searchTimer = setTimeout(cariNama, 350); }

async function cariNama() {
  const q   = document.getElementById('namaInput').value.trim();
  const box = document.getElementById('searchResults');
  if (q.length < 2) { box.style.display = 'none'; return; }
  const res  = await fetch(baseUrl + 'api/produk/kode/' + encodeURIComponent(q));
  const json = await res.json();
  if (!json.data?.length) { box.style.display = 'none'; return; }
  box.innerHTML = json.data.map(p => `
    <div onclick='pilihProduk(${JSON.stringify(p)})'
         style="padding:9px 12px;cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:13px;display:flex;justify-content:space-between"
         onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background=''">
      <div><div style="font-weight:500">${p.nama}</div><div style="font-size:11px;color:#6c757d">${p.kode} | Stok: ${p.stok}</div></div>
      <div style="font-weight:600;color:#1D9E75;white-space:nowrap;margin-left:8px">Rp ${Number(p.harga_jual).toLocaleString('id')}</div>
    </div>`).join('');
  box.style.display = 'block';
}

function pilihProduk(p) { document.getElementById('searchResults').style.display='none'; document.getElementById('namaInput').value=''; tampilkanProduk(p); }

function tampilkanProduk(p) {
  hideBoxes(); currentProduct = p;
  document.getElementById('foundIcon').textContent   = p.nama.substring(0,3).toUpperCase();
  document.getElementById('foundNama').textContent   = p.nama;
  document.getElementById('foundKode').textContent   = p.kode + (p.barcode ? ' | '+p.barcode : '');
  document.getElementById('foundHarga').textContent  = Number(p.harga_jual).toLocaleString('id');
  document.getElementById('foundSatuan').textContent = p.satuan;
  document.getElementById('foundStok').textContent   = p.stok;
  document.getElementById('foundBox').style.display  = 'flex';
}

function hideBoxes() { document.getElementById('foundBox').style.display='none'; document.getElementById('notFound').style.display='none'; currentProduct=null; }

// =============================================
// KERANJANG
// =============================================
function addToCart() { if (!currentProduct) return; addProductToCart(currentProduct); document.getElementById('barcodeInput').value=''; hideBoxes(); }

function addProductToCart(p) {
  const ex = cart.find(i => i.id === p.id);
  if (ex) { if (ex.qty >= p.stok) { alert('Stok tidak cukup!'); return; } ex.qty++; }
  else cart.push({...p, qty: 1});
  renderCart();
}

function removeItem(i)      { cart.splice(i,1); renderCart(); }
function clearCart()        { cart=[]; renderCart(); hideBoxes(); resetDiskon(); document.getElementById('bayar').value=''; hitungKembalian(); }
function changeQty(i,delta) { cart[i].qty+=delta; if(cart[i].qty<1) cart.splice(i,1); renderCart(); }

function renderCart() {
  const tbody = document.getElementById('cartBody');
  document.getElementById('cartCount').textContent = cart.length;
  if (!cart.length) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:24px;color:#6c757d;font-size:13px"><i class="fas fa-shopping-cart"></i> Keranjang kosong</td></tr>';
    updateSummary(); return;
  }
  tbody.innerHTML = cart.map((item,i) => `
    <tr style="border-bottom:1px solid #e9ecef">
      <td><div style="font-size:13px;font-weight:500">${item.nama}</div><div style="font-size:11px;color:#6c757d">${item.kode}</div></td>
      <td style="font-size:13px">Rp ${Number(item.harga_jual).toLocaleString('id')}</td>
      <td><div class="qty-ctrl"><button class="qty-btn" onclick="changeQty(${i},-1)">-</button><span style="min-width:24px;text-align:center;font-weight:600">${item.qty}</span><button class="qty-btn" onclick="changeQty(${i},1)">+</button></div></td>
      <td style="font-size:13px;font-weight:600">Rp ${(item.harga_jual*item.qty).toLocaleString('id')}</td>
      <td><button onclick="removeItem(${i})" style="background:none;border:none;cursor:pointer;color:#dc3545"><i class="fas fa-times"></i></button></td>
    </tr>`).join('');
  updateSummary();
}

function updateSummary() {
  const sub   = cart.reduce((a,b) => a + b.harga_jual*b.qty, 0);
  const seAD  = Math.max(0, sub - diskonNominal);
  const pajak = Math.round(seAD * PAJAK_PCT / 100);
  const total = seAD + pajak;
  document.getElementById('subtotal').textContent   = 'Rp ' + sub.toLocaleString('id');
  document.getElementById('diskonDisp').textContent = '- Rp ' + diskonNominal.toLocaleString('id');
  document.getElementById('pajak').textContent      = 'Rp ' + pajak.toLocaleString('id');
  document.getElementById('total').textContent      = 'Rp ' + total.toLocaleString('id');
  hitungKembalian();
}

function hitungKembalian() {
  const sub   = cart.reduce((a,b) => a + b.harga_jual*b.qty, 0);
  const total = Math.max(0, sub - diskonNominal + Math.round(Math.max(0,sub-diskonNominal)*PAJAK_PCT/100));
  const bayar = parseFloat(document.getElementById('bayar').value)||0;
  document.getElementById('kembalian').textContent = 'Rp ' + Math.max(0, bayar-total).toLocaleString('id');
}

function setBayar(v) { document.getElementById('bayar').value=v; hitungKembalian(); }

function selectPay(el, method) {
  document.querySelectorAll('.pay-option').forEach(e => e.classList.remove('active'));
  el.classList.add('active');
  payMethod = method;
  document.getElementById('tunaiSection').style.display   = method==='tunai'   ? 'block' : 'none';
  document.getElementById('kreditSection').style.display  = method==='kredit'  ? 'block' : 'none';
}

// =============================================
// DISKON
// =============================================
async function cekDiskon() {
  const kode = document.getElementById('kodeDiskon').value.trim();
  const sub  = cart.reduce((a,b) => a+b.harga_jual*b.qty, 0);
  const info = document.getElementById('diskonInfo');
  if (!kode) { info.style.display='none'; return; }

  const fd = new FormData();
  fd.append('kode', kode);
  fd.append('subtotal', sub);
  fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

  const res  = await fetch(baseUrl+'api/diskon/cek', {method:'POST', body:fd});
  const json = await res.json();

  info.style.display = 'block';
  if (json.valid) {
    diskonAktif = json.diskon; diskonNominal = json.nominal;
    info.style.cssText = 'display:block;font-size:12px;margin-top:5px;padding:6px 10px;border-radius:6px;background:#E1F5EE;color:#065f46';
    info.innerHTML = '<i class="fas fa-check-circle"></i> ' + json.pesan;
    document.getElementById('btnResetDiskon').style.display = 'inline-flex';
    updateSummary();
  } else {
    resetDiskon();
    info.style.cssText = 'display:block;font-size:12px;margin-top:5px;padding:6px 10px;border-radius:6px;background:#fee2e2;color:#991b1b';
    info.innerHTML = '<i class="fas fa-times-circle"></i> ' + json.pesan;
  }
}

function resetDiskon() {
  diskonAktif=null; diskonNominal=0;
  document.getElementById('kodeDiskon').value='';
  document.getElementById('diskonInfo').style.display='none';
  document.getElementById('btnResetDiskon').style.display='none';
  updateSummary();
}

// =============================================
// PROSES TRANSAKSI
// =============================================
async function prosesTransaksi() {
  if (!cart.length) { alert('Keranjang masih kosong!'); return; }
  const sub   = cart.reduce((a,b) => a+b.harga_jual*b.qty, 0);
  const total = Math.max(0, sub-diskonNominal + Math.round(Math.max(0,sub-diskonNominal)*PAJAK_PCT/100));
  const bayar = parseFloat(document.getElementById('bayar').value)||(payMethod!=='tunai'?total:0);

  if (payMethod==='tunai' && bayar<total) { alert('Uang diterima kurang!'); return; }

  const items = cart.map(i => ({produk_id:i.id, harga:i.harga_jual, qty:i.qty}));
  const fd = new FormData();
  fd.append('items', JSON.stringify(items));
  fd.append('pelanggan',     document.getElementById('pelanggan').value||'Umum');
  fd.append('telepon',       document.getElementById('teleponKredit')?.value||'');
  fd.append('metode_bayar',  payMethod);
  fd.append('bayar',         bayar);
  fd.append('diskon_id',     diskonAktif?.id||'');
  fd.append('diskon_nominal',diskonNominal);
  fd.append('jatuh_tempo',   document.getElementById('jatuhTempo')?.value||'');
  fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

  try {
    const res  = await fetch(baseUrl+'transaksi/simpan', {method:'POST', body:fd});
    const json = await res.json();
    if (json.status) {
      lastTrxId = json.transaksi_id;
      document.getElementById('modalNoTrx').textContent    = json.no_transaksi;
      document.getElementById('modalKembalian').textContent = 'Rp '+Number(json.kembalian).toLocaleString('id');
      document.getElementById('modalKembalianWrap').style.display = payMethod==='tunai' ? 'block' : 'none';
      document.getElementById('modalKreditWrap').style.display    = payMethod==='kredit' ? 'block' : 'none';
      document.getElementById('btnStruk').onclick = () => window.open(baseUrl+'transaksi/struk/'+lastTrxId,'_blank');
      document.getElementById('modalSukses').style.display = 'flex';
      clearCart(); resetDiskon();
    } else { alert('Gagal: '+json.message); }
  } catch(e) { alert('Kesalahan koneksi.'); }
}

function closeModal() {
  document.getElementById('modalSukses').style.display='none';
  document.getElementById('pelanggan').value='Umum';
  document.getElementById('bayar').value='';
}

function showToast(msg) {
  let t = document.getElementById('posToast');
  if (!t) { t=document.createElement('div'); t.id='posToast'; t.style.cssText='position:fixed;bottom:24px;right:24px;background:#1D9E75;color:white;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:500;z-index:99999;transition:opacity .3s;opacity:0'; document.body.appendChild(t); }
  t.textContent=msg; t.style.opacity='1';
  clearTimeout(t._t); t._t=setTimeout(()=>t.style.opacity='0',2500);
}

document.getElementById('barcodeInput').focus();
</script>

<?= $this->endSection() ?>
