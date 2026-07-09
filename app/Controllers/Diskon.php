<?php
namespace App\Controllers;

use App\Models\DiskonModel;

class Diskon extends BaseController
{
    protected $diskonModel;

    public function __construct()
    {
        $this->diskonModel = new DiskonModel();
    }

    public function index()
    {
        return view('diskon/index', [
            'title'  => 'Diskon & Promo',
            'diskon' => $this->diskonModel->orderBy('berlaku_sampai', 'ASC')->findAll(),
        ]);
    }

    public function simpan()
    {
        $rules = [
            'kode'           => 'required|max_length[20]|is_unique[diskon.kode]',
            'nama'           => 'required',
            'tipe'           => 'required|in_list[persen,nominal]',
            'nilai'          => 'required|numeric',
            'berlaku_dari'   => 'required|valid_date',
            'berlaku_sampai' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->diskonModel->insert([
            'kode'           => strtoupper($this->request->getPost('kode')),
            'nama'           => $this->request->getPost('nama'),
            'tipe'           => $this->request->getPost('tipe'),
            'nilai'          => $this->request->getPost('nilai'),
            'min_belanja'    => $this->request->getPost('min_belanja') ?? 0,
            'berlaku_dari'   => $this->request->getPost('berlaku_dari'),
            'berlaku_sampai' => $this->request->getPost('berlaku_sampai'),
            'kuota'          => $this->request->getPost('kuota') ?: null,
            'status'         => 'aktif',
        ]);

        return redirect()->to('/diskon')->with('success', 'Diskon berhasil ditambahkan.');
    }

    public function update($id)
    {
        $this->diskonModel->update($id, [
            'nama'           => $this->request->getPost('nama'),
            'nilai'          => $this->request->getPost('nilai'),
            'min_belanja'    => $this->request->getPost('min_belanja') ?? 0,
            'berlaku_dari'   => $this->request->getPost('berlaku_dari'),
            'berlaku_sampai' => $this->request->getPost('berlaku_sampai'),
            'kuota'          => $this->request->getPost('kuota') ?: null,
            'status'         => $this->request->getPost('status'),
        ]);

        return redirect()->to('/diskon')->with('success', 'Diskon berhasil diupdate.');
    }

    public function hapus($id)
    {
        $this->diskonModel->update($id, ['status' => 'nonaktif']);
        return redirect()->to('/diskon')->with('success', 'Diskon dinonaktifkan.');
    }

    // API: validasi kode diskon dari POS
    public function cekKode()
    {
        $kode     = $this->request->getPost('kode');
        $subtotal = (float)$this->request->getPost('subtotal');

        $hasil = $this->diskonModel->cekKode($kode, $subtotal);
        return $this->response->setJSON($hasil);
    }
}
