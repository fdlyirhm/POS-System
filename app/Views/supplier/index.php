<?php /* ===================== SUPPLIER INDEX ===================== */ ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card">
  <div class="card-header">
    <div class="card-title"><i class="fas fa-truck" style="color:var(--green)"></i> Data Supplier</div>
    <a href="/supplier/tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Supplier</a>
  </div>
  <div class="table-wrapper">
    <table>
      <thead><tr><th>Kode</th><th>Nama Supplier</th><th>Kontak Person</th><th>Telepon</th><th>Email</th><th style="text-align:center">Aksi</th></tr></thead>
      <tbody>
        <?php if (empty($supplier)): ?>
        <tr><td colspan="6" style="text-align:center;padding:24px;color:#6c757d">Belum ada data supplier</td></tr>
        <?php else: ?>
        <?php foreach ($supplier as $s): ?>
        <tr>
          <td><strong><?= esc($s['kode']) ?></strong></td>
          <td><?= esc($s['nama']) ?></td>
          <td><?= esc($s['kontak_person'] ?? '-') ?></td>
          <td><?= esc($s['telepon'] ?? '-') ?></td>
          <td><?= esc($s['email'] ?? '-') ?></td>
          <td style="text-align:center"><a href="/supplier/edit/<?= $s['id'] ?>" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i> Edit</a></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
