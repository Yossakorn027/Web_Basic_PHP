<?php
session_start();

require '../config.php';      // เชื่อมต่อ PDO
require 'auth_admin.php';     // ตรวจสอบสิทธิ์ Admin

// ---------- helper: ตรวจว่าตารางมีคอลัมน์หรือไม่ ----------
function table_has_column(PDO $conn, string $table, string $column): bool {
    $st = $conn->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
    $st->execute([$column]);
    return (bool)$st->fetch();
}
$HAS_IMAGE_COL = table_has_column($conn, 'products', 'image');

// เพิ่มสินค้าใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name        = trim($_POST['product_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);

    if ($name !== '' && $price > 0) {
        $imageName = null;

        if (!empty($_FILES['product_image']['name']) && $HAS_IMAGE_COL) {
            $file = $_FILES['product_image'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExt  = ['jpg','jpeg','png'];
            $allowedMime = ['image/jpeg','image/png'];

            $mime = null;
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $mime = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);
                }
            }
            if ($mime === null) { $mime = $file['type'] ?? ''; }

            if (in_array($ext, $allowedExt, true) && in_array($mime, $allowedMime, true)) {
                $imageName = 'product_' . time() . '.' . $ext;
                $dir = __DIR__ . '/../product_images';
                if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
                $path = $dir . '/' . $imageName;
                if (!move_uploaded_file($file['tmp_name'], $path)) {
                    $_SESSION['error'] = "อัปโหลดรูปไม่สำเร็จ";
                    header("Location: products.php"); exit;
                }
            } else {
                $_SESSION['error'] = "รองรับเฉพาะไฟล์ JPG/PNG เท่านั้น";
                header("Location: products.php"); exit;
            }
        }

        if ($HAS_IMAGE_COL) {
            $stmt = $conn->prepare("
                INSERT INTO products (product_name, description, price, stock, category_id, image)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $description, $price, $stock, $category_id, $imageName]);
        } else {
            $stmt = $conn->prepare("
                INSERT INTO products (product_name, description, price, stock, category_id)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $description, $price, $stock, $category_id]);
        }

        $_SESSION['success'] = "เพิ่มสินค้าใหม่สำเร็จแล้ว";
        header("Location: products.php"); exit;
    } else {
        $_SESSION['error'] = "กรุณากรอกข้อมูลสินค้าให้ครบถ้วน";
    }
}

// ลบสินค้า
if (isset($_GET['delete'])) {
    $product_id = (int) $_GET['delete'];
    $imageName = null;

    if ($HAS_IMAGE_COL) {
        $stmt = $conn->prepare("SELECT image FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $imageName = $stmt->fetchColumn() ?: null;
    }

    try {
        $conn->beginTransaction();
        $del = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $del->execute([$product_id]);
        $conn->commit();
    } catch (Throwable $e) {
        $conn->rollBack();
        $_SESSION['error'] = "ไม่สามารถลบสินค้าได้: " . $e->getMessage();
        header("Location: products.php"); exit;
    }

    if ($HAS_IMAGE_COL && $imageName) {
        $baseDir = realpath(__DIR__ . '/../product_images');
        $filePath = $baseDir ? realpath($baseDir . '/' . $imageName) : null;
        if ($filePath && $baseDir && strpos($filePath, $baseDir) === 0 && is_file($filePath)) {
            @unlink($filePath);
        }
    }

    $_SESSION['success'] = "ลบสินค้าเรียบร้อยแล้ว";
    header("Location: products.php"); exit;
}

// ดึงข้อมูล
$stmt = $conn->query("
    SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>จัดการสินค้า</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color:#d0ebff; /* ฟ้าอ่อน */
      margin:0; padding:24px 12px;
      display:flex; justify-content:center; min-height:100vh;
    }
    .page-box {
      background:#fff;
      width:100%; max-width:1200px;
      border-radius:12px;
      box-shadow:0 6px 20px rgba(0,0,0,.2);
      padding:24px;
    }
    .btn-pill { border-radius:10px; }
    .dash-title {
      font-size:1.8rem; font-weight:700;
      text-align:center; color:#0d3b66; margin-bottom:.25rem;
    }
    .dash-sub {
      text-align:center; color:#495057; margin-bottom:1.25rem;
    }
    .card-modern {
      border:1px solid rgba(0,0,0,.08);
      border-radius:12px;
      box-shadow:0 4px 14px rgba(0,0,0,.08);
      background:#f8f9fa;
    }
    .card-modern:hover { background:#fff; }
    .table thead th {
      background:#e7f5ff;
      border-bottom:1px solid rgba(0,0,0,.08);
    }
    .table tbody td { border-color:rgba(0,0,0,.06); }
    .btn-primary { background:#339af0; border:none; }
    .btn-primary:hover { background:#228be6; }
    .btn-warning { background:#ffd43b; border:none; color:#5c4b00; }
    .btn-warning:hover { background:#fcc419; }
    .btn-danger { background:#e03131; border:none; }
    .btn-danger:hover { background:#c92a2a; }
  </style>
</head>
<body>
<div class="page-box">
  <div class="dash-title">จัดการสินค้า</div>
  <div class="dash-sub">ยินดีต้อนรับ, <span class="fw-semibold"><?= $username ?></span></div>

  <div class="d-flex justify-content-between mb-3">
    <a href="index.php" class="btn btn-outline-secondary btn-pill">← กลับหน้าผู้ดูแล</a>
    <a href="../logout.php" class="btn btn-outline-secondary btn-pill">ออกจากระบบ</a>
  </div>

  <!-- แจ้งเตือน -->
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($_SESSION['error']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($_SESSION['success']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); endif; ?>

  <!-- ฟอร์มเพิ่มสินค้า -->
  <div class="card card-modern mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">เพิ่มสินค้าใหม่</h5>
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-4">
          <label class="form-label">ชื่อสินค้า</label>
          <input type="text" name="product_name" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">ราคา (บาท)</label>
          <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">จำนวน</label>
          <input type="number" name="stock" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">หมวดหมู่</label>
          <select name="category_id" class="form-select" required>
            <option value="">เลือกหมวดหมู่</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= (int)$cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">รายละเอียด</label>
          <textarea name="description" class="form-control" rows="2"></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">รูปสินค้า (JPG/PNG)</label>
          <input type="file" name="product_image" class="form-control">
          <?php if (!$HAS_IMAGE_COL): ?>
            <div class="form-text text-danger">* DB ยังไม่มีคอลัมน์ image</div>
          <?php endif; ?>
        </div>
        <div class="col-12">
          <button type="submit" name="add_product" class="btn btn-primary btn-pill">บันทึกสินค้า</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ตารางสินค้า -->
  <div class="card card-modern">
    <div class="card-body">
      <h5 class="card-title mb-3">รายการสินค้า</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>ชื่อสินค้า</th>
              <th>หมวดหมู่</th>
              <th>ราคา</th>
              <th>คงเหลือ</th>
              <th class="text-center" style="width:180px;">จัดการ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td><?= number_format((float)$p['price'],2) ?> บาท</td>
                <td><?= (int)$p['stock'] ?></td>
                <td class="text-center">
                  <a href="products.php?delete=<?= (int)$p['product_id'] ?>" 
                     class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบ?');">ลบ</a>
                  <a href="edit_products.php?product_id=<?= (int)$p['product_id'] ?>" 
                     class="btn btn-sm btn-warning">แก้ไข</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
              <tr><td colspan="5" class="text-center text-muted">ยังไม่มีสินค้า</td></tr>
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
