<?php
session_start();
require_once '../config.php';      // เชื่อมต่อฐานข้อมูล
require_once 'auth_admin.php';     // ตรวจสิทธิ์แอดมิน

// กัน session ว่าง
$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แผงควบคุมผู้ดูแลระบบ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ใช้ Bootstrap เวอร์ชันเดียวกับหน้า Login -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background-color:#d0ebff; /* โทนเดียวกับหน้า Login */
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            margin:0;
        }
        .dash-box{
            background:#fff;
            width:100%;
            max-width:900px;          /* กว้างกว่า login เล็กน้อยเพื่อใส่ปุ่มได้ */
            padding:2.5rem;
            border-radius:12px;
            box-shadow:0 6px 20px rgba(0,0,0,.20);
        }
        .dash-title{
            font-size:2rem;
            font-weight:700;
            text-align:center;
            margin-bottom: .25rem;
        }
        .dash-sub{
            text-align:center;
            color:#6c757d;
            margin-bottom:1.75rem;
        }
        .action-card{
            border:1px solid rgba(0,0,0,.06);
            border-radius:12px;
            padding:1rem;
            height:100%;
            transition:transform .15s ease, box-shadow .15s ease;
            background:#f8f9fa;
        }
        .action-card:hover{
            transform:translateY(-2px);
            box-shadow:0 8px 20px rgba(0,0,0,.12);
            background:#ffffff;
        }
        .action-title{
            font-weight:700;
            margin-bottom:.5rem;
        }
        .btn-big{
            padding:.75rem 1rem;
            font-size:1.05rem;
            border-radius:10px;
            width:100%;
        }
        .top-bar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            margin-bottom:1rem;
            flex-wrap:wrap;
        }
        .hello{
            font-weight:600;
        }
    </style>
</head>

<body>
    <div class="dash-box container-fluid">
        <!-- หัวเรื่อง + ผู้ใช้ -->
        <div class="dash-title">แผงควบคุมผู้ดูแลระบบ</div>
        <div class="dash-sub">ยินดีต้อนรับ, <span class="hello"><?= $username ?></span></div>

        <div class="top-bar">
            <div class="text-muted small">
                เข้าสู่ระบบด้วยสิทธิ์: <span class="badge text-bg-primary">Admin</span>
            </div>
            <div class="ms-auto">
                <a href="../logout.php" class="btn btn-outline-secondary btn-sm">ออกจากระบบ</a>
            </div>
        </div>

        <!-- ปุ่มแบบการ์ด + ปุ่มหลักด้านล่าง (ทั้งสองวิธีรองรับ) -->
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="action-card d-flex flex-column">
                    <div class="action-title">จัดการสินค้า</div>
                    <div class="text-muted mb-3 small">เพิ่ม/แก้ไข/ลบ สินค้าในร้าน</div>
                    <a href="products.php" class="btn btn-primary btn-big mt-auto">ไปที่สินค้า</a>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="action-card d-flex flex-column">
                    <div class="action-title">คำสั่งซื้อ</div>
                    <div class="text-muted mb-3 small">ติดตามสถานะคำสั่งซื้อ</div>
                    <a href="orders.php" class="btn btn-success btn-big mt-auto">ไปที่คำสั่งซื้อ</a>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="action-card d-flex flex-column">
                    <div class="action-title">สมาชิก</div>
                    <div class="text-muted mb-3 small">รายชื่อ/สิทธิ์ผู้ใช้</div>
                    <a href="users.php" class="btn btn-warning btn-big mt-auto">ไปที่สมาชิก</a>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="action-card d-flex flex-column">
                    <div class="action-title">หมวดหมู่</div>
                    <div class="text-muted mb-3 small">จัดกลุ่มสินค้าให้เป็นระบบ</div>
                    <a href="categories.php" class="btn btn-dark btn-big mt-auto">ไปที่หมวดหมู่</a>
                </div>
            </div>
        </div>

        <!-- ปุ่มสไตล์เดิม (สำรอง) -->
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="products.php" class="btn btn-primary w-100 py-3">จัดการสินค้า</a>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="orders.php" class="btn btn-success w-100 py-3">จัดการคำสั่งซื้อ</a>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="users.php" class="btn btn-warning w-100 py-3">จัดการสมาชิก</a>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <a href="categories.php" class="btn btn-dark w-100 py-3">จัดการหมวดหมู่</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
