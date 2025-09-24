<?php
session_start();
require '../config.php';
require 'auth_admin.php';

// ตรวจสอบว่ามีการส่ง product_id หรือไม่
if (!isset($_GET['product_id'])) {
    header("Location: products.php");
    exit;
}

$product_id = intval($_GET['product_id']);

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// ถ้าไม่พบข้อมูล
if (!$product) {
    echo "<h3>ไม่พบข้อมูลสินค้าดังกล่าว</h3>";
    exit;
}

// ดึงข้อมูลหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// เมื่อบันทึกฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $category_id = (int) $_POST['category_id'];

    $oldImage = $_POST['old_image'] ?? null;
    $removeImage = isset($_POST['remove_image']);
    $newImageName = $oldImage;

    if ($removeImage) {
        $newImageName = null;
    }

    if (!empty($_FILES['product_image']['name'])) {
        $file = $_FILES['product_image'];
        $allowed = ['image/jpeg', 'image/png'];
        if (in_array($file['type'], $allowed, true) && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newImageName = 'product_' . time() . '.' . $ext;
            $uploadDir = realpath(__DIR__ . '/../product_images');
            $destPath = $uploadDir . DIRECTORY_SEPARATOR . $newImageName;
            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                $newImageName = $oldImage;
            }
        }
    }

    $sql = "UPDATE products
            SET product_name=?, description=?, price=?, stock=?, category_id=?, image=?
            WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $description, $price, $stock, $category_id, $newImageName, $product_id]);

    if (!empty($oldImage) && $oldImage !== $newImageName) {
        $baseDir = realpath(__DIR__ . '/../product_images');
        $filePath = realpath($baseDir . DIRECTORY_SEPARATOR . $oldImage);
        if ($filePath && strpos($filePath, $baseDir) === 0 && is_file($filePath)) {
            @unlink($filePath);
        }
    }

    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>แก้ไขสินค้า</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color:#d0ebff; /* ฟ้าอ่อน */
      font-family:'Kanit',sans-serif;
      min-height:100vh;
      margin:0;
    }
    .topbar {
      background-color:#74c0fc;
      box-shadow:0 4px 12px rgba(0,0,0,.15);
    }
    .page-box {
      max-width:900px;
      margin:32px auto;
      background:#fff;
      border-radius:12px;
      box-shadow:0 6px 20px rgba(0,0,0,.15);
      padding:24px;
    }
    .card-title {
      font-weight:700;
      color:#0d3b66;
    }
    .btn-blue {
      background-color:#339af0;
      border:none;
      color:#fff;
      border-radius:8px;
      padding:.6rem 1.2rem;
      font-weight:500;
    }
    .btn-blue:hover { background-color:#228be6; }
    .btn-outline-blue {
      border:1px solid #339af0;
      color:#1971c2;
      border-radius:8px;
    }
    .btn-outline-blue:hover {
      background:#e7f5ff;
      border-color:#228be6;
      color:#0d3b66;
    }
    .form-control, .form-select {
      border-radius:8px;
    }
  </style>
</head>
<body>
  <!-- Topbar -->
  <nav class="topbar navbar navbar-expand">
    <div class="container">
      <a href="products.php" class="btn btn-outline-light">กลับหน้ารายการสินค้า</a>
      <span class="ms-auto text-white fw-semibold">แก้ไขสินค้า</span>
    </div>
  </nav>

  <!-- Content -->
  <main class="page-box">
    <h3 class="card-title mb-4">แก้ไขสินค้า</h3>

    <form method="post" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">ชื่อสินค้า</label>
        <input type="text" name="product_name" class="form-control"
          value="<?= htmlspecialchars($product['product_name']) ?>" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">ราคาสินค้า</label>
        <input type="number" step="0.01" name="price" class="form-control"
          value="<?= $product['price'] ?>" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">จำนวนในคลัง</label>
        <input type="number" name="stock" class="form-control"
          value="<?= $product['stock'] ?>" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">หมวดหมู่</label>
        <select name="category_id" class="form-select" required>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['category_id'] ?>"
              <?= $cat['category_id'] == $product['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['category_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12">
        <label class="form-label">รายละเอียดสินค้า</label>
        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label d-block">รูปปัจจุบัน</label>
        <?php if (!empty($product['image'])): ?>
          <img src="../product_images/<?= htmlspecialchars($product['image']) ?>" width="140" class="rounded mb-2 shadow-sm">
        <?php else: ?>
          <span class="text-muted d-block mb-2">ไม่มีรูป</span>
        <?php endif; ?>
        <input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image']) ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">อัปโหลดรูปใหม่ (jpg, png)</label>
        <input type="file" name="product_image" class="form-control">
        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
          <label class="form-check-label" for="remove_image">ลบรูปเดิม</label>
        </div>
      </div>

      <div class="col-12 text-end">
        <button type="submit" class="btn btn-blue">บันทึกการแก้ไข</button>
      </div>
    </form>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
