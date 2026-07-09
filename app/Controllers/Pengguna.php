<?php
// app/Controllers/Pengguna.php
namespace App\Controllers;
use App\Models\UserModel;

class Pengguna extends BaseController
{
    protected $userModel;
    public function __construct() { $this->userModel = new UserModel(); }

    public function index()
    {
        return view('pengguna/index', [
            'title'     => 'Manajemen Pengguna',
            'pengguna'  => $this->userModel->orderBy('nama')->findAll(),
        ]);
    }

    public function simpan()
    {
        $rules = [
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'nama'     => 'required',
            'role'     => 'required|in_list[admin,kasir]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'nama'     => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'     => $this->request->getPost('role'),
            'status'   => 'aktif',
        ]);

        return redirect()->to('/pengguna')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update($id)
    {
        $data = [
            'nama'   => $this->request->getPost('nama'),
            'role'   => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
        ];

        $newPass = $this->request->getPost('password');
        if (!empty($newPass)) {
            $data['password'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $data);
        return redirect()->to('/pengguna')->with('success', 'Pengguna berhasil diupdate.');
    }

    public function hapus($id)
    {
        if ($id == session()->get('user_id')) {
            return redirect()->to('/pengguna')->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        $this->userModel->update($id, ['status' => 'nonaktif']);
        return redirect()->to('/pengguna')->with('success', 'Pengguna berhasil dinonaktifkan.');
    }
}
