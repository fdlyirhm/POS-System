<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Toko Sembako</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', sans-serif; background: #f4f6f8; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
  .login-wrap { width: 100%; max-width: 400px; padding: 24px; }
  .login-card { background: #fff; border-radius: 16px; border: 1px solid #e9ecef; padding: 36px 32px; }
  .logo-area { text-align: center; margin-bottom: 28px; }
  .logo-icon { width: 60px; height: 60px; background: #1D9E75; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 26px; margin: 0 auto 12px; }
  h1 { font-size: 20px; font-weight: 700; color: #343a40; }
  p { font-size: 13px; color: #6c757d; margin-top: 4px; }
  .form-group { margin-bottom: 16px; }
  label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 5px; color: #343a40; }
  input { width: 100%; padding: 10px 14px; border: 1px solid #ced4da; border-radius: 8px; font-size: 14px; outline: none; }
  input:focus { border-color: #1D9E75; }
  .btn-login { width: 100%; padding: 12px; background: #1D9E75; color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 4px; }
  .btn-login:hover { background: #0F6E56; }
  .alert { padding: 10px 14px; border-radius: 8px; margin-bottom: 14px; font-size: 13px; background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
  .footer-text { text-align: center; font-size: 12px; color: #6c757d; margin-top: 20px; }
</style>
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="logo-area">
      <div class="logo-icon">&#128722;</div>
      <h1>Toko Sembako</h1>
      <p>Sistem Informasi Penjualan</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
      <?= csrf_field() ?>
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan username" required autofocus value="<?= old('username') ?>">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required>
      </div>
      <button type="submit" class="btn-login">Masuk</button>
    </form>
  </div>
</div>
</body>
</html>
