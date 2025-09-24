<?php
session_start();
require '../config.php';
require 'auth_admin.php';

// เพิ่มหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if ($category_name) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->execute([$category_name]);
        $_SESSION['success'] = "เพิ่มหมวดหมู่สำเร็จแล้ว";
    } else {
        $_SESSION['error'] = "กรุณากรอกชื่อหมวดหมู่";
    }
    header("Location: category.php");
    exit;
}

// ลบหมวดหมู่
if (isset($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $productCount = (int)$stmt->fetchColumn();

    if ($productCount > 0) {
        $_SESSION['error'] = "ไม่สามารถลบได้ เนื่องจากยังมีสินค้าอยู่ในหมวดหมู่นี้";
    } else {
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $_SESSION['success'] = "ลบหมวดหมู่เรียบร้อยแล้ว";
    }
    header("Location: category.php");
    exit;
}

// แก้ไขหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $category_id = (int)($_POST['category_id'] ?? 0);
    $category_name = trim($_POST['new_name'] ?? '');
    if ($category_id && $category_name) {
        $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
        $stmt->execute([$category_name, $category_id]);
        $_SESSION['success'] = "อัปเดตชื่อหมวดหมู่เรียบร้อย";
    } else {
        $_SESSION['error'] = "กรุณากรอกชื่อใหม่ให้ถูกต้อง";
    }
    header("Location: category.php");
    exit;
}

// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id ASC")->fetchAll(PDO::FETCH_ASSOC);

// ชื่อผู้ใช้
$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการหมวดหมู่</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background-color:#d0ebff; /* Soft Blue */
            min-height:100vh;
            margin:0;
            display:flex;
            align-items:flex-start;
            justify-content:center;
            padding:24px 12px;
        }
        .page-box{
            background:#fff;
            width:100%;
            max-width:1100px;
            border-radius:12px;
            box-shadow:0 6px 20px rgba(0,0,0,.20);
            padding:24px;
            border:1px solid rgba(0,0,0,.08);
        }
        .btn-pill{ border-radius:10px; }

        .top-bar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:1rem;
        }

        .card-modern{
            border:1px solid rgba(0,0,0,.08);
            border-radius:12px;
            box-shadow:0 4px 14px rgba(0,0,0,.08);
            background:#f8f9fa;
        }
        .card-modern:hover{ background:#ffffff; }

        .dash-title{
            font-size:1.8rem;
            font-weight:700;
            margin-bottom:.25rem;
            text-align:center;
            color:#0d3b66; /* heading blue */
        }
        .dash-sub{
            text-align:center;
            color:#495057;
            margin-bottom:1.25rem;
        }

        .table thead th{
            background:#e7f5ff;
            border-bottom:1px solid rgba(0,0,0,.08);
        }
        .table tbody td{ border-color:rgba(0,0,0,.06); }

        .btn-primary{ background-color:#339af0; border-color:#339af0; }
        .btn-primary:hover{ background-color:#228be6; border-color:#228be6; }
        .btn-warning{ background-color:#ffd43b; border-color:#ffd43b; color:#212529; }
        .btn-danger{ background-color:#fa5252; border-color:#fa5252; }
    </style>
</head>
<body>
<div class="page-box">

    <div class="dash-title">จัดการหมวดหมู่สินค้า</div>
    <div class="dash-sub">ยินดีต้อนรับ, <span class="fw-semibold"><?= $username ?></span></div>

    <div class="top-bar">
        <a href="index.php" class="btn btn-outline-secondary btn-pill">← กลับหน้าผู้ดูแล</a>
        <a href="../logout.php" class="btn btn-outline-secondary btn-pill">ออกจากระบบ</a>
    </div>

    <!-- แจ้งเตือน -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- ฟอร์มเพิ่มหมวดหมู่ -->
    <div class="card card-modern mb-4">
        <div class="card-body">
            <h5 class="card-title mb-2">เพิ่มหมวดหมู่ใหม่</h5>
            <form method="post" class="row g-3 mt-1">
                <div class="col-12 col-md-6">
                    <label class="form-label">ชื่อหมวดหมู่</label>
                    <input type="text" name="category_name" class="form-control" placeholder="กรอกชื่อหมวดหมู่" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <button type="submit" name="add_category" class="btn btn-primary w-100 btn-pill">เพิ่ม</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ตารางหมวดหมู่ -->
    <div class="card card-modern">
        <div class="card-body">
            <h5 class="card-title mb-2">รายการหมวดหมู่</h5>
            <div class="table-responsive mt-2">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width:40%">ชื่อหมวดหมู่</th>
                            <th style="width:40%">แก้ไขชื่อ</th>
                            <th style="width:20%" class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= htmlspecialchars($cat['category_name']) ?></td>
                                <td>
                                    <form method="post" class="d-flex">
                                        <input type="hidden" name="category_id" value="<?= (int)$cat['category_id'] ?>">
                                        <input type="text" name="new_name" class="form-control me-2" placeholder="ชื่อใหม่" required>
                                        <button type="submit" name="update_category" class="btn btn-warning btn-sm btn-pill">บันทึก</button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <a href="category.php?delete=<?= (int)$cat['category_id'] ?>"
                                       class="btn btn-danger btn-sm btn-pill"
                                       onclick="return confirm('คุณต้องการลบหมวดหมู่นี้หรือไม่?');">ลบ</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">ยังไม่มีหมวดหมู่</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
