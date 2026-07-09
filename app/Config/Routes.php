<?php
// app/Config/Routes.php

$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->get('logout', 'Auth::logout');

// =============================================
// Dashboard
// =============================================
$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// =============================================
// Transaksi & POS
// =============================================
$routes->get('transaksi',                  'Transaksi::index',       ['filter' => 'auth']);
$routes->get('transaksi/pos',              'Transaksi::pos',         ['filter' => 'auth']);
$routes->post('transaksi/simpan',          'Transaksi::simpan',      ['filter' => 'auth']);
$routes->get('transaksi/detail/(:num)',    'Transaksi::detail/$1',   ['filter' => 'auth']);
$routes->get('transaksi/struk/(:num)',     'Transaksi::struk/$1',    ['filter' => 'auth']);
$routes->post('transaksi/batal/(:num)',    'Transaksi::batal/$1',    ['filter' => 'auth:admin']);

// API — Barcode & Produk
$routes->get('api/produk/barcode/(:any)', 'Transaksi::cariBarcode/$1', ['filter' => 'auth']);
$routes->get('api/produk/kode/(:any)',    'Transaksi::cariKode/$1',    ['filter' => 'auth']);

// API — Validasi kode diskon (dipanggil dari POS)
$routes->post('api/diskon/cek',           'Diskon::cekKode',           ['filter' => 'auth']);

// =============================================
// Produk
// =============================================
$routes->get('produk',                 'Produk::index',       ['filter' => 'auth']);
$routes->get('produk/stok',            'Produk::stokRendah',  ['filter' => 'auth']);
$routes->get('produk/tambah',          'Produk::tambah',      ['filter' => 'auth:admin']);
$routes->post('produk/simpan',         'Produk::simpan',      ['filter' => 'auth:admin']);
$routes->get('produk/edit/(:num)',     'Produk::edit/$1',     ['filter' => 'auth:admin']);
$routes->post('produk/update/(:num)',  'Produk::update/$1',   ['filter' => 'auth:admin']);
$routes->post('produk/hapus/(:num)',   'Produk::hapus/$1',    ['filter' => 'auth:admin']);

// =============================================
// Stok Opname
// =============================================
$routes->get('stok-opname',                      'StokOpname::index',        ['filter' => 'auth:admin']);
$routes->get('stok-opname/buat',                 'StokOpname::buat',         ['filter' => 'auth:admin']);
$routes->post('stok-opname/simpan',              'StokOpname::simpan',       ['filter' => 'auth:admin']);
$routes->get('stok-opname/detail/(:num)',        'StokOpname::detail/$1',    ['filter' => 'auth:admin']);
$routes->post('stok-opname/selesaikan/(:num)',   'StokOpname::selesaikan/$1',['filter' => 'auth:admin']);

// =============================================
// Pembelian / Restock
// =============================================
$routes->get('pembelian',                 'Pembelian::index',     ['filter' => 'auth:admin']);
$routes->get('pembelian/buat',            'Pembelian::buat',      ['filter' => 'auth:admin']);
$routes->post('pembelian/simpan',         'Pembelian::simpan',    ['filter' => 'auth:admin']);
$routes->get('pembelian/detail/(:num)',   'Pembelian::detail/$1', ['filter' => 'auth:admin']);
$routes->post('pembelian/terima/(:num)',  'Pembelian::terima/$1', ['filter' => 'auth:admin']);
$routes->post('pembelian/batal/(:num)',   'Pembelian::batal/$1',  ['filter' => 'auth:admin']);

// =============================================
// Hutang Pelanggan
// =============================================
$routes->get('hutang',                 'Hutang::index',      ['filter' => 'auth']);
$routes->get('hutang/detail/(:num)',   'Hutang::detail/$1',  ['filter' => 'auth']);
$routes->post('hutang/bayar/(:num)',   'Hutang::bayar/$1',   ['filter' => 'auth']);

// =============================================
// Diskon & Promo
// =============================================
$routes->get('diskon',                 'Diskon::index',      ['filter' => 'auth:admin']);
$routes->post('diskon/simpan',         'Diskon::simpan',     ['filter' => 'auth:admin']);
$routes->post('diskon/update/(:num)',  'Diskon::update/$1',  ['filter' => 'auth:admin']);
$routes->post('diskon/hapus/(:num)',   'Diskon::hapus/$1',   ['filter' => 'auth:admin']);

// =============================================
// Laporan
// =============================================
$routes->get('laporan',                    'Laporan::index',   ['filter' => 'auth:admin']);
$routes->get('laporan/harian',             'Laporan::harian',  ['filter' => 'auth:admin']);
$routes->get('laporan/bulanan',            'Laporan::bulanan', ['filter' => 'auth:admin']);
$routes->get('laporan/produk',             'Laporan::produk',  ['filter' => 'auth:admin']);
$routes->get('laporan/export/(:segment)',  'Laporan::export/$1',['filter' => 'auth:admin']);

$routes->get('laporan/export-pdf/harian',   'Laporan::exportPdfHarian',   ['filter' => 'auth:admin']);
$routes->get('laporan/export-pdf/bulanan',  'Laporan::exportPdfBulanan',  ['filter' => 'auth:admin']);
$routes->get('laporan/export-pdf/produk',   'Laporan::exportPdfProduk',   ['filter' => 'auth:admin']);

$routes->get('laporan/export-excel/harian',  'Laporan::exportExcelHarian',  ['filter' => 'auth:admin']);
$routes->get('laporan/export-excel/bulanan', 'Laporan::exportExcelBulanan', ['filter' => 'auth:admin']);
$routes->get('laporan/export-excel/produk',  'Laporan::exportExcelProduk',  ['filter' => 'auth:admin']);

// =============================================
// Master Data
// =============================================
$routes->get('supplier',                  'Supplier::index',      ['filter' => 'auth:admin']);
$routes->get('supplier/tambah',           'Supplier::tambah',     ['filter' => 'auth:admin']);
$routes->post('supplier/simpan',          'Supplier::simpan',     ['filter' => 'auth:admin']);
$routes->get('supplier/edit/(:num)',      'Supplier::edit/$1',    ['filter' => 'auth:admin']);
$routes->post('supplier/update/(:num)',   'Supplier::update/$1',  ['filter' => 'auth:admin']);

$routes->get('kategori',                  'Kategori::index',      ['filter' => 'auth:admin']);
$routes->post('kategori/simpan',          'Kategori::simpan',     ['filter' => 'auth:admin']);
$routes->post('kategori/update/(:num)',   'Kategori::update/$1',  ['filter' => 'auth:admin']);

// =============================================
// Pengguna
// =============================================
$routes->get('pengguna',                  'Pengguna::index',      ['filter' => 'auth:admin']);
$routes->post('pengguna/simpan',          'Pengguna::simpan',     ['filter' => 'auth:admin']);
$routes->post('pengguna/update/(:num)',   'Pengguna::update/$1',  ['filter' => 'auth:admin']);
$routes->post('pengguna/hapus/(:num)',    'Pengguna::hapus/$1',   ['filter' => 'auth:admin']);

// =============================================
// Pengaturan Toko
// =============================================
$routes->get('pengaturan',         'Pengaturan::index',  ['filter' => 'auth:admin']);
$routes->post('pengaturan/simpan', 'Pengaturan::simpan', ['filter' => 'auth:admin']);
