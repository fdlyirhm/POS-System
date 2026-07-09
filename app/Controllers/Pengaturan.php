<?php
namespace App\Controllers;

use App\Models\PengaturanModel;

class Pengaturan extends BaseController
{
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        return view('pengaturan/index', [
            'title'      => 'Pengaturan Toko',
            'pengaturan' => $this->pengaturanModel->getAll(),
        ]);
    }

    public function simpan()
    {
        $keys = [
            'nama_toko', 'alamat', 'telepon', 'email',
            'pajak_persen', 'nota_footer', 'buka_jam', 'tutup_jam',
        ];

        foreach ($keys as $key) {
            $value = $this->request->getPost($key);
            if ($value !== null) {
                $this->pengaturanModel->simpan($key, $value);
            }
        }

        return redirect()->to('/pengaturan')->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}
