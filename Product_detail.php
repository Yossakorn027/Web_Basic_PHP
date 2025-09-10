<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id'])) {
  header('Location: index.php');
  exit();
}
$product_id = (int)$_GET['id'];
$isLoggedIn = isset($_SESSION['user_id']);

$stmt = $conn->prepare("
  SELECT p.*, c.category_name
  FROM products p
  LEFT JOIN categories c ON p.category_id = c.category_id
  WHERE p.product_id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// ถ้าไม่พบสินค้า
if (!$product) {
  header('Location: index.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>รายละเอียดสินค้า</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- ใช้เวอร์ชันเดียวกับหน้าก่อนหน้า -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body{
      background-color:#d0ebff; /* โทนเดียวกับหน้า Login/Admin */
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
      max-width:1000px;
      border-radius:12px;
      box-shadow:0 6px 20px rgba(0,0,0,.20);
      padding:24px;
    }
    .btn-pill{ border-radius:10px; }
    .card-modern{
      border:1px solid rgba(0,0,0,.06);
      border-radius:12px;
      box-shadow:0 4px 14px rgba(0,0,0,.08);
      background:#f8f9fa;
    }
    .card-modern:hover{ background:#ffffff; }
    .product-title{ font-weight:700; }
    .meta{ color:#6c757d; }
    .qty-input{ max-width:140px; }
    .img-wrap{
      width:100%;
      border-radius:12px;
      overflow:hidden;
      background:#e9ecef;
      display:flex;
      align-items:center;
      justify-content:center;
      aspect-ratio: 16/9;
    }
    .img-wrap img{
      width:100%;
      height:100%;
      object-fit:cover;
    }
  </style>
</head>

<body>
  <div class="page-box">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <a href="index.php" class="btn btn-outline-secondary btn-pill">← กลับหน้ารายการสินค้า</a>
    </div>

    <div class="card card-modern">
      <div class="card-body">
        <div class="row g-4">
          <!-- ภาพสินค้า (ถ้ามีคอลัมน์ image หรือ image_url จะโชว์อัตโนมัติ) -->
          <div class="col-12 col-md-6">
            <div class="img-wrap">
              <?php if (!empty($product['image_url'] ?? $product['image'] ?? null)): ?>
                <img src="<?= htmlspecialchars(($product['image_url'] ?? $product['image'])) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
              <?php else: ?>
                <div class="text-muted">ไม่มีภาพสินค้า</div>
              <?php endif; ?>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <h3 class="product-title mb-1"><?= htmlspecialchars($product['product_name']) ?></h3>
            <div class="meta mb-3">หมวดหมู่: <?= htmlspecialchars($product['category_name'] ?? 'ไม่ระบุหมวดหมู่') ?></div>

            <?php if (!empty($product['description'])): ?>
              <p class="mb-3"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <?php endif; ?>

            <p class="mb-1"><strong>ราคา:</strong> <?= number_format((float)$product['price'], 2) ?> บาท</p>
            <p class="mb-3">
              <strong>คงเหลือ:</strong>
              <?=
                is_numeric($product['stock']) ? (int)$product['stock'] . ' ชิ้น' : htmlspecialchars($product['stock']) . ' ชนิด'
              ?>
            </p>

            <?php if ($isLoggedIn): ?>
              <form action="cart.php" method="post" class="row g-2 align-items-end">
                <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                <div class="col-auto">
                  <label for="quantity" class="form-label mb-1">จำนวน</label>
                  <input
                    type="number"
                    class="form-control qty-input"
                    name="quantity"
                    id="quantity"
                    value="1"
                    min="1"
                    max="<?= htmlspecialchars($product['stock']) ?>"
                    required
                  >
                </div>
                <div class="col-auto">
                  <button type="submit" class="btn btn-success btn-pill">เพิ่มในตะกร้า</button>
                </div>
              </form>
            <?php else: ?>
              <div class="alert alert-info mt-3 mb-0">กรุณาเข้าสู่ระบบเพื่อสั่งสินค้า</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
