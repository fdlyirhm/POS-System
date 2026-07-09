<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div style="display:grid;grid-template-columns:1fr 360px;gap:16px">
  <!-- List Pengguna -->
  <div class="card">
    <div class="card-title" style="margin-bottom:14px"><i class="fas fa-users" style="color:var(--green)"></i> Manajemen Pengguna</div>

    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <?php foreach (session()->getFlashdata('errors') as $e): ?><div><?= $e ?></div><?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="table-wrapper">
      <table>
        <thead><tr><th>Nama</th><th>Username</th><th>Role</th><th>Status</th><th style="text-align:center">Aksi</th></tr></thead>
        <tbody>
          <?php foreach ($pengguna as $p): ?>
          <tr>
            <td><strong><?= esc($p['nama']) ?></strong></td>
            <td style="font-family:monospace"><?= esc($p['username']) ?></td>
            <td>
              <span class="badge <?= $p['role']==='admin' ? 'badge-danger' : 'badge-info' ?>">
                <?= ucfirst($p['role']) ?>
              </span>
            </td>
            <td>
              <span class="badge <?= $p['status']==='aktif' ? 'badge-success' : 'badge-danger' ?>">
                <?= ucfirst($p['status']) ?>
              </span>
            </td>
            <td style="text-align:center">
              <button class="btn btn-secondary btn-sm"
                onclick="editUser(<?= $p['id'] ?>,'<?= addslashes($p['nama']) ?>','<?= $p['role'] ?>','<?= $p['status'] ?>')">
                <i class="fas fa-edit"></i>
              </button>
              <?php if ($p['id'] != session()->get('user_id')): ?>
              <form method="POST" action="/pengguna/hapus/<?= $p['id'] ?>" style="display:inline" onsubmit="return confirm('Nonaktifkan pengguna ini?')">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-user-slash"></i></button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Form -->
  <div>
    <div class="card">
      <div class="card-title" style="margin-bottom:14px" id="userFormTitle">Tambah Pengguna</div>
      <form method="POST" id="userForm" action="/pengguna/simpan">
        <?= csrf_field() ?>
        <div class="form-group">
          <label class="form-label">Nama Lengkap <span style="color:red">*</span></label>
          <input type="text" name="nama" id="userNama" class="form-control" required>
        </div>
        <div class="form-group" id="usernameGroup">
          <label class="form-label">Username <span style="color:red">*</span></label>
          <input type="text" name="username" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Password <span id="passNote" style="color:#6c757d;font-size:11px">(min 6 karakter)</span></label>
          <input type="password" name="password" class="form-control" id="passField" placeholder="Password baru...">
        </div>
        <div class="form-group">
          <label class="form-label">Role <span style="color:red">*</span></label>
          <select name="role" id="userRole" class="form-control" required>
            <option value="kasir">Kasir</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <div class="form-group" id="statusGroup" style="display:none">
          <label class="form-label">Status</label>
          <select name="status" id="userStatus" class="form-control">
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
        <div style="display:flex;gap:8px">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
          <button type="button" class="btn btn-secondary" onclick="resetUserForm()">Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const baseUrl = '<?= base_url() ?>';
let editMode = false;

function editUser(id, nama, role, status) {
  editMode = true;
  document.getElementById('userFormTitle').textContent = 'Edit Pengguna';
  document.getElementById('userForm').action = baseUrl + 'pengguna/update/' + id;
  document.getElementById('userNama').value = nama;
  document.getElementById('userRole').value = role;
  document.getElementById('userStatus').value = status;
  document.getElementById('usernameGroup').style.display = 'none';
  document.getElementById('statusGroup').style.display = 'block';
  document.getElementById('passNote').textContent = '(kosongkan jika tidak diubah)';
  document.getElementById('passField').required = false;
  window.scrollTo(0, 0);
}

function resetUserForm() {
  editMode = false;
  document.getElementById('userFormTitle').textContent = 'Tambah Pengguna';
  document.getElementById('userForm').action = baseUrl + 'pengguna/simpan';
  document.getElementById('userForm').reset();
  document.getElementById('usernameGroup').style.display = 'block';
  document.getElementById('statusGroup').style.display = 'none';
  document.getElementById('passNote').textContent = '(min 6 karakter)';
  document.getElementById('passField').required = true;
}
</script>

<?= $this->endSection() ?>
