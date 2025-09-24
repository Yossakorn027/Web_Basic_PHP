<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id'])) {
  header('Location: indexmember.php');
  exit();
}
$isLoggedIn = isset($_SESSION['user_id']);

$product_id = (int)$_GET['id'];

$stmt = $conn->prepare("
  SELECT p.*, c.category_name
  FROM products p
  LEFT JOIN categories c ON p.category_id = c.category_id
  WHERE p.product_id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// เตรียมรูป: รองรับทั้ง image / image_url / image_path และ fallback
$imgField = null;
if (!empty($product['image']))       { $imgField = $product['image']; }
elseif (!empty($product['image_url'])){ $imgField = $product['image_url']; }
elseif (!empty($product['image_path'])){ $imgField = $product['image_path']; }

if ($imgField) {
  // ถ้าเป็น URL ภายนอกให้ใช้ตรง ๆ, ถ้าเป็นชื่อไฟล์ให้ชี้ไปโฟลเดอร์ product_images
  $img = (preg_match('~^https?://~i', $imgField))
        ? $imgField
        : 'product_images/' . rawurlencode($imgField);
} else {
  $img = 'product_images/no-image.jpg';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>รายละเอียดสินค้า</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{
      background-color:#d0ebff; /* ธีมเดียวกับหน้าอื่น ๆ */
      min-height:100vh;
      margin:0;
      display:flex;
      flex-direction:column;
    }
    .topbar{
      background:#ffffffaa;
      backdrop-filter: blur(6px);
      border-bottom:1px solid rgba(0,0,0,.06);
    }
    .page-box{
      width:100%;
      max-width:1100px;
      margin:24px auto;
      padding:0 12px;
    }
    .card-modern{
      border:1px solid rgba(0,0,0,.06);
      border-radius:12px;
      background:#f8f9fa;
      box-shadow:0 6px 20px rgba(0,0,0,.12);
      transition: background .15s ease;
    }
    .card-modern:hover{ background:#ffffff; }
    .btn-pill{ border-radius:10px; }
    .img-wrap{
      width:100%;
      border-radius:12px;
      overflow:hidden;
      background:#e9ecef;
      display:flex;
      align-items:center;
      justify-content:center;
      aspect-ratio: 1/1; /* ดูดีบนมือถือ */
    }
    .img-wrap img{
      width:100%; height:100%; object-fit:cover; display:block;
    }
    .price-tag{
      font-weight:700;
      padding:.4rem .75rem;
      border-radius:999px;
      background:#0d6efd;
      color:#fff;
      display:inline-flex;
      align-items:center;
      gap:.4rem;
      box-shadow:0 6px 16px rgba(13,110,253,.25);
    }
  </style>
</head>

<body>
  <!-- Top bar -->
  <nav class="topbar">
    <div class="page-box d-flex align-items-center" style="padding-top:12px;padding-bottom:12px;">
      <a href="indexmember.php" class="btn btn-outline-secondary btn-pill">← กลับหน้ารายการสินค้า</a>
      <span class="ms-auto text-muted small">รายละเอียดสินค้า</span>
    </div>
  </nav>

  <main class="page-box">
    <?php if (!$product): ?>
      <div class="alert alert-warning">ไม่พบสินค้า</div>
      <a href="indexmember.php" class="btn btn-outline-secondary btn-pill mt-2">กลับหน้ารายการ</a>
    <?php else: ?>
      <div class="card card-modern">
        <div class="card-body p-3 p-md-4">
          <div class="row g-4">
            <!-- ภาพสินค้า -->
            <div class="col-12 col-md-5">
              <div class="img-wrap">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
              </div>
            </div>

            <!-- รายละเอียด -->
            <div class="col-12 col-md-7 d-flex flex-column">
              <h3 class="mb-1"><?= htmlspecialchars($product['product_name']) ?></h3>
              <div class="text-muted mb-2">
                หมวดหมู่: <?= htmlspecialchars($product['category_name'] ?? 'ทั่วไป') ?>
              </div>

              <div class="text-secondary mb-3" style="white-space:pre-line;">
                <?= nl2br(htmlspecialchars($product['description'] ?? '')) ?>
              </div>

              <?php
                $price = isset($product['price']) ? number_format((float)$product['price'], 2) : '0.00';
                $stock = (int)($product['stock'] ?? 0);
              ?>
              <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="price-tag">฿<?= $price ?></span>
                <?php if ($stock > 0): ?>
                  <span class="badge text-bg-success rounded-pill">คงเหลือ: <?= $stock ?></span>
                <?php else: ?>
                  <span class="badge text-bg-danger rounded-pill">สินค้าหมด</span>
                <?php endif; ?>
              </div>

              <?php if ($isLoggedIn): ?>
                <form action="cart.php" method="post" class="row g-2 align-items-end mt-auto">
                  <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                  <div class="col-auto">
                    <label for="quantity" class="form-label mb-1">จำนวน</label>
                    <input
                      type="number"
                      class="form-control"
                      style="max-width:140px"
                      name="quantity"
                      id="quantity"
                      value="1"
                      min="1"
                      max="<?= max($stock, 0) ?>"
                      <?= $stock <= 0 ? 'disabled' : '' ?>
                      required
                    >
                  </div>
                  <div class="col-auto">
                    <button type="submit" class="btn btn-success btn-pill" <?= $stock <= 0 ? 'disabled' : '' ?>>
                      เพิ่มในตะกร้า
                    </button>
                  </div>
                </form>
              <?php else: ?>
                <div class="alert alert-info mt-3 mb-0">กรุณาเข้าสู่ระบบเพื่อสั่งซื้อ</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
