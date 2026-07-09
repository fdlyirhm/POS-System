# Sistem Informasi Penjualan Toko Sembako
**Framework:** CodeIgniter 4 | **PHP:** 8.1+ | **Database:** MySQL 8.0+

---

## Struktur File Proyek

```
app/
├── Config/
│   ├── Routes.php          ← Semua routing aplikasi
│   └── Filters.php         ← Daftarkan AuthFilter di sini
├── Controllers/
│   ├── Auth.php            ← Login & logout
│   ├── Dashboard.php       ← Halaman utama
│   ├── Transaksi.php       ← POS + API barcode
│   ├── Produk.php          ← CRUD produk
│   ├── Laporan.php         ← Laporan & export
│   ├── Supplier.php        ← Master supplier
│   ├── Kategori.php        ← Master kategori
│   └── Pengguna.php        ← Manajemen user
├── Filters/
│   └── AuthFilter.php      ← Middleware session & role
├── Models/
│   ├── TransaksiModel.php
│   ├── ProdukModel.php
│   └── Models.php          ← UserModel, KategoriModel, dll
└── Views/
    ├── layouts/main.php    ← Template utama sidebar
    ├── auth/login.php
    ├── dashboard/index.php
    ├── transaksi/
    │   ├── pos.php         ← Kasir POS + barcode scanner
    │   ├── index.php
    │   ├── detail.php
    │   └── struk.php       ← Struk thermal print
    ├── produk/
    │   ├── index.php
    │   ├── form.php        ← Tambah/edit + scan barcode
    │   └── stok_rendah.php
    ├── laporan/
    │   ├── index.php
    │   ├── harian.php
    │   └── bulanan.php     ← Dengan grafik Chart.js
    ├── supplier/
    │   ├── index.php
    │   └── form.php
    ├── kategori/index.php
    └── pengguna/index.php
sql/
└── database.sql            ← DDL + data awal
```

---

## Langkah Instalasi

### 1. Buat project CI4
```bash
composer create-project codeigniter4/appstarter toko-sembako
cd toko-sembako
```

### 2. Copy semua file
Salin semua file dari folder ini ke dalam project CI4.

### 3. Setup database
```bash
# Buat database
mysql -u root -p < sql/database.sql
```

### 4. Konfigurasi .env
```env
CI_ENVIRONMENT = development

database.default.hostname = localhost
database.default.database = db_toko_sembako
database.default.username = root
database.default.password = your_password
database.default.DBDriver = MySQLi
database.default.port     = 3306

app.baseURL = 'http://localhost:8080/'
```

### 5. Daftarkan AuthFilter di app/Config/Filters.php
```php
use App\Filters\AuthFilter;

public array $aliases = [
    'csrf'     => CSRF::class,
    'toolbar'  => DebugToolbar::class,
    'honeypot' => Honeypot::class,
    'auth'     => AuthFilter::class,  // ← Tambahkan ini
];
```

### 6. Jalankan server
```bash
php spark serve
```
Buka: http://localhost:8080

---

## Akun Default

| Username | Password | Role  |
|----------|----------|-------|
| admin    | password | Admin |
| kasir1   | password | Kasir |
| kasir2   | password | Kasir |

---

## Fitur Utama

### Kasir POS + Barcode Scanner
- Scan barcode menggunakan **kamera** (Quagga.js — mendukung EAN-13, EAN-8, Code 128, Code 39, UPC)
- Input **manual** kode barcode lewat keyboard / scanner USB
- **Autocomplete** pencarian nama produk
- Keranjang belanja dinamis dengan AJAX
- Metode bayar: Tunai, QRIS, Debit, Kredit
- Hitung kembalian otomatis
- **Cetak struk** thermal (80mm)

### Manajemen Produk
- CRUD produk dengan kode barcode
- Scan barcode langsung di form tambah/edit produk
- Monitoring stok rendah & kritis
- Log perubahan stok otomatis saat transaksi

### Laporan
- Laporan harian & bulanan
- Grafik penjualan (Chart.js)
- Penjualan per kategori
- Export CSV
- Produk terlaris

### Keamanan
- Session-based authentication
- Role-based access control (Admin vs Kasir)
- CSRF protection (bawaan CI4)
- Password hashing (bcrypt)

---

## Library Eksternal (CDN)

| Library | Fungsi |
|---------|--------|
| Quagga.js | Barcode scanner via kamera (EAN, Code128, dll) |
| Chart.js 4 | Grafik laporan bulanan |
| Font Awesome 6 | Icon seluruh aplikasi |

---

## Catatan Pengembangan Lanjutan

- Tambah **PhpSpreadsheet** untuk export Excel: `composer require phpoffice/phpspreadsheet`
- Tambah **TCPDF / DomPDF** untuk export PDF laporan
- Implementasi **stok opname** di menu produk
- Tambah **modul hutang/kredit pelanggan**
- Integrasi **payment gateway QRIS** (Midtrans / Xendit)
