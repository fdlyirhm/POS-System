<?php
// app/Controllers/Kategori.php
namespace App\Controllers;
use App\Models\KategoriModel;

class Kategori extends BaseController
{
    protected $kategoriModel;
    public function __construct() { $this->kategoriModel = new KategoriModel(); }

    public function index()
    {
        return view('kategori/index', [
            'title'    => 'Master Kategori',
            'kategori' => $this->kategoriModel->orderBy('nama')->findAll(),
        ]);
    }

    public function simpan()
    {
        $this->kategoriModel->insert([
            'kode'      => $this->request->getPost('kode'),
            'nama'      => $this->request->getPost('nama'),
            'deskripsi' => $this->request->getPost('deskripsi'),
        ]);
        return redirect()->to('/kategori')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update($id)
    {
        $this->kategoriModel->update($id, [
            'nama'      => $this->request->getPost('nama'),
            'deskripsi' => $this->request->getPost('deskripsi'),
        ]);
        return redirect()->to('/kategori')->with('success', 'Kategori berhasil diupdate.');
    }
}
