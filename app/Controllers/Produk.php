<?php
// app/Controllers/Produk.php
namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\SupplierModel;
use App\Models\StokLogModel;

class Produk extends BaseController
{
    protected $produkModel;

    public function __construct()
    {
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $keyword    = $this->request->getGet('keyword');
        $kategoriId = $this->request->getGet('kategori_id');
        $status     = $this->request->getGet('status');

        $data = [
            'title'    => 'Daftar Produk',
            'produk'   => $this->produkModel->getProduk($keyword, $kategoriId, $status),
            'kategori' => (new KategoriModel())->findAll(),
            'keyword'  => $keyword,
        ];
        return view('produk/index', $data);
    }

    public function tambah()
    {
        $data = [
            'title'    => 'Tambah Produk',
            'kategori' => (new KategoriModel())->findAll(),
            'supplier' => (new SupplierModel())->findAll(),
        ];
        return view('produk/form', $data);
    }

    public function simpan()
    {
        $rules = [
            'kode'       => 'required|max_length[20]|is_unique[produk.kode]',
            'nama'       => 'required|max_length[150]',
            'kategori_id'=> 'required|integer',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok'       => 'required|integer',
            'satuan'     => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->produkModel->insert([
            'kode'          => $this->request->getPost('kode'),
            'barcode'       => $this->request->getPost('barcode') ?: null,
            'nama'          => $this->request->getPost('nama'),
            'kategori_id'   => $this->request->getPost('kategori_id'),
            'supplier_id'   => $this->request->getPost('supplier_id') ?: null,
            'harga_beli'    => $this->request->getPost('harga_beli'),
            'harga_jual'    => $this->request->getPost('harga_jual'),
            'stok'          => $this->request->getPost('stok'),
            'stok_minimum'  => $this->request->getPost('stok_minimum') ?: 10,
            'satuan'        => $this->request->getPost('satuan'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'status'        => 'aktif',
        ]);

        return redirect()->to('/produk')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = [
            'title'    => 'Edit Produk',
            'produk'   => $this->produkModel->find($id),
            'kategori' => (new KategoriModel())->findAll(),
            'supplier' => (new SupplierModel())->findAll(),
        ];
        return view('produk/form', $data);
    }

    public function update($id)
    {
        $this->produkModel->update($id, [
            'nama'          => $this->request->getPost('nama'),
            'barcode'       => $this->request->getPost('barcode'),
            'kategori_id'   => $this->request->getPost('kategori_id'),
            'supplier_id'   => $this->request->getPost('supplier_id'),
            'harga_beli'    => $this->request->getPost('harga_beli'),
            'harga_jual'    => $this->request->getPost('harga_jual'),
            'stok_minimum'  => $this->request->getPost('stok_minimum'),
            'satuan'        => $this->request->getPost('satuan'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'status'        => $this->request->getPost('status'),
        ]);

        return redirect()->to('/produk')->with('success', 'Produk berhasil diupdate.');
    }

    public function hapus($id)
    {
        $this->produkModel->update($id, ['status' => 'nonaktif']);
        return redirect()->to('/produk')->with('success', 'Produk berhasil dinonaktifkan.');
    }

    public function stokRendah()
    {
        $data = [
            'title'  => 'Produk Stok Rendah',
            'produk' => $this->produkModel->stokRendah(),
        ];
        return view('produk/stok_rendah', $data);
    }
}
