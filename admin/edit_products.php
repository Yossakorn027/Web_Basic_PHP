<?php
session_start();
require '../config.php';   // เชื่อมต่อ PDO
require 'auth_admin.php';  // ตรวจสอบสิทธิ์ Admin


// ตรวจสอบวำ่ ไดส้ ง่ id สนิ คำ้มำหรอื ไม่
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}
$product_id = $_GET['id'];
// ดงึขอ้ มลู สนิ คำ้
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    echo "<h3>ไม่พบข้อมุลสินค้า</h3>";
    exit;
}
// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
// เมื่อมีกำรสง่ ฟอรม์
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    if ($name && $price > 0) {
        $stmt = $conn->prepare("UPDATE products SET product_name = ?, description = ?, price = ?, stock = ?,
category_id = ? WHERE product_id = ?");
        $stmt->execute([$name, $description, $price, $stock, $category_id, $product_id]);
        header("Location: products.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>เเก้ไขสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
    <h2>เเก้ไขสินค้า</h2>
    <a href="products.php" class="btn btn-secondary mb-3">← กลับไปยังรายการสินค้า</a>
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">ชื่อสินค้า</label>
            <input type="text" name="product_name" class="form-control" value="<?=
                htmlspecialchars($product['product_name']) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">ราคา</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>"
                required>
        </div>
        <div class="col-md-3">
            <label class="form-label">จำนวนในคลัง</label>
            <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">หมวดหมู่</label>
            <select name="category_id" class="form-select" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>" <?= ($product['category_id'] == $cat['category_id']) ?
                          'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">รายละเอียดสินค้า</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'])
                ?></textarea>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">บันทึกการเเก้ไข</button>
        </div>
    </form>
</body>

</html>