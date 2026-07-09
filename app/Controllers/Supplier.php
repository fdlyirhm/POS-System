<?php
// app/Controllers/Supplier.php
namespace App\Controllers;
use App\Models\SupplierModel;

class Supplier extends BaseController
{
    protected $supplierModel;
    public function __construct() { $this->supplierModel = new SupplierModel(); }

    public function index()
    {
        return view('supplier/index', [
            'title'    => 'Data Supplier',
            'supplier' => $this->supplierModel->orderBy('nama')->findAll(),
        ]);
    }

    public function tambah()
    {
        return view('supplier/form', ['title' => 'Tambah Supplier']);
    }

    public function simpan()
    {
        $this->supplierModel->insert([
            'kode'          => $this->request->getPost('kode'),
            'nama'          => $this->request->getPost('nama'),
            'alamat'        => $this->request->getPost('alamat'),
            'telepon'       => $this->request->getPost('telepon'),
            'email'         => $this->request->getPost('email'),
            'kontak_person' => $this->request->getPost('kontak_person'),
        ]);
        return redirect()->to('/supplier')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('supplier/form', [
            'title'    => 'Edit Supplier',
            'supplier' => $this->supplierModel->find($id),
        ]);
    }

    public function update($id)
    {
        $this->supplierModel->update($id, [
            'nama'          => $this->request->getPost('nama'),
            'alamat'        => $this->request->getPost('alamat'),
            'telepon'       => $this->request->getPost('telepon'),
            'email'         => $this->request->getPost('email'),
            'kontak_person' => $this->request->getPost('kontak_person'),
        ]);
        return redirect()->to('/supplier')->with('success', 'Supplier berhasil diupdate.');
    }
}
