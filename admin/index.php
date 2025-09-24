<?php
session_start();
require_once '../config.php';

// ตรวจสอบสิทธิ์ admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
$username = htmlspecialchars($_SESSION['username'] ?? 'ผู้ดูแลระบบ');
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>แผงควบคุมผู้ดูแลระบบ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color:#d0ebff; /* ฟ้าอ่อน */
      min-height:100vh;
      font-family: 'Kanit', sans-serif;
    }
    .topbar {
      background-color:#74c0fc; /* ฟ้าเข้ม */
      box-shadow:0 4px 12px rgba(0,0,0,.15);
    }
    .page-box {
      max-width:1200px;
      margin:24px auto;
      background:#fff;
      border-radius:12px;
      box-shadow:0 6px 20px rgba(0,0,0,.15);
      padding:24px;
    }
    .hello-chip {
      display:inline-flex; align-items:center;
      padding:.45rem .8rem; border-radius:999px;
      background:#e7f5ff; border:1px solid #74c0fc;
      color:#0d3b66; font-weight:500;
    }
    .menu-card {
      border:1px solid rgba(0,0,0,.06);
      border-radius:12px;
      background:#f8f9fa;
      transition:transform .15s ease, box-shadow .15s ease;
      box-shadow:0 4px 14px rgba(0,0,0,.08);
    }
    .menu-card:hover {
      transform:translateY(-3px);
      box-shadow:0 10px 24px rgba(0,0,0,.12);
      background:#fff;
    }
    .btn-blue {
      background-color:#339af0;
      border:none;
      color:#fff;
      border-radius:999px;
    }
    .btn-blue:hover { background-color:#228be6; }
    .btn-outline-blue {
      border:1px solid #339af0;
      color:#1971c2;
      border-radius:999px;
    }
    .btn-outline-blue:hover {
      background:#e7f5ff;
      border-color:#228be6;
      color:#0d3b66;
    }
    .section-title {
      color:#0d3b66;
      font-weight:700;
    }
    .soft-footer {
      color:#495057;
    }
  </style>
</head>

<body>

  <!-- Topbar -->
  <nav class="topbar navbar navbar-expand-lg">
    <div class="container d-flex justify-content-between align-items-center">
      <span class="navbar-brand fw-bold text-white">
        แผงควบคุมผู้ดูแลระบบ
      </span>
      <div class="d-flex align-items-center gap-2">
        <span class="hello-chip">
          admin/ <?= $username ?>
        </span>
        <a href="../logout.php" class="btn btn-outline-light btn-sm">
          ออกจากระบบ
        </a>
      </div>
    </div>
  </nav>

  <!-- Content -->
  <main class="page-box">
    <h2 class="section-title mb-4">เมนูหลัก</h2>
    <div class="row g-3 g-md-4">
      <!-- จัดการสินค้า -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card menu-card h-100 p-3">
          <h5 class="fw-bold mb-1">สินค้า</h5>
          <small class="text-muted d-block mb-2">เพิ่ม/แก้ไข/สต็อก</small>
          <p class="text-secondary">ดูและแก้ไขรายการสินค้าได้ที่นี่</p>
          <a href="products.php" class="btn btn-blue w-100">ไปที่สินค้า</a>
        </div>
      </div>

      <!-- คำสั่งซื้อ -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card menu-card h-100 p-3">
          <h5 class="fw-bold mb-1">คำสั่งซื้อ</h5>
          <small class="text-muted d-block mb-2">ตรวจสอบสถานะ</small>
          <p class="text-secondary">ติดตามออเดอร์และอัปเดตได้รวดเร็ว</p>
          <a href="orders.php" class="btn btn-blue w-100">ไปที่คำสั่งซื้อ</a>
        </div>
      </div>

      <!-- สมาชิก -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card menu-card h-100 p-3">
          <h5 class="fw-bold mb-1">สมาชิก</h5>
          <small class="text-muted d-block mb-2">บทบาท/สิทธิ์</small>
          <p class="text-secondary">จัดการผู้ใช้และสิทธิ์การใช้งาน</p>
          <a href="users.php" class="btn btn-blue w-100">ไปที่สมาชิก</a>
        </div>
      </div>

      <!-- หมวดหมู่ -->
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="card menu-card h-100 p-3">
          <h5 class="fw-bold mb-1">หมวดหมู่</h5>
          <small class="text-muted d-block mb-2">จัดระเบียบสินค้า</small>
          <p class="text-secondary">เพิ่ม/แก้ไขหมวดหมู่สินค้า</p>
          <a href="category.php" class="btn btn-blue w-100">ไปที่หมวดหมู่</a>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <a href="../logout.php" class="btn btn-outline-blue">ออกจากระบบ</a>
    </div>
  </main>

  <footer class="py-3 text-center soft-footer">
    <small>ระบบผู้ดูแล · โทนฟ้าอ่อน</small>
  </footer>

</body>
</html>
