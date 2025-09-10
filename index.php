<?php
session_start();
require_once 'config.php'; // เชื่อมต่อฐานข้อมูล

$isLoggedIn = isset($_SESSION['user_id']);

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$stmt = $conn->query("
    SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// if (!isset($_SESSION['username'])) {
//   header('Location: login.php'); exit;
// }
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ยินดีต้อนรับสู่หน้าหลัก</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body{
      background-color:#d0ebff; /* โทนเดียวกับหน้า Login/Admin */
      margin:0;
      min-height:100vh;
    }
    .page-box{
      max-width:1200px;
      margin:24px auto;
      background:#fff;
      border-radius:12px;
      box-shadow:0 6px 20px rgba(0,0,0,.20);
      padding:24px;
    }
    .btn-pill{ border-radius:10px; }
    .btn-lgx{ padding:.65rem 1rem; font-size:1.05rem; }
    .card-modern{
      border:1px solid rgba(0,0,0,.06);
      border-radius:12px;
      box-shadow:0 4px 14px rgba(0,0,0,.08);
      transition:transform .15s ease, box-shadow .15s ease, background .15s ease;
      background:#f8f9fa;
    }
    .card-modern:hover{
      transform:translateY(-2px);
      box-shadow:0 10px 24px rgba(0,0,0,.12);
      background:#ffffff;
    }
    .card-modern .card-title{ font-weight:700; }
    .header-bar{
      display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap;
      margin-bottom:16px;
    }
    .welcome{
      font-size:1.1rem; color:#495057;
    }
    .grid-gap{ row-gap:1rem; }
  </style>
</head>

<body>
  <div class="page-box">
    <div class="header-bar">
      <h1 class="m-0">รายการสินค้า</h1>

      <div class="d-flex align-items-center gap-2">
        <?php if ($isLoggedIn): ?>
          <span class="welcome me-2">
            ยินดีต้อนรับ, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
            (<?= htmlspecialchars($_SESSION['role']) ?>)
          </span>
          <a href="profile.php" class="btn btn-outline-info btn-pill btn-lgx">ข้อมูลส่วนตัว</a>
          <a href="cart.php" class="btn btn-warning btn-pill btn-lgx">ดูตะกร้า</a>
          <a href="logout.php" class="btn btn-outline-secondary btn-pill btn-lgx">ออกจากระบบ</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-success btn-pill btn-lgx">เข้าสู่ระบบ</a>
          <a href="register.php" class="btn btn-primary btn-pill btn-lgx">สมัครสมาชิก</a>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!$products): ?>
      <div class="alert alert-info">ยังไม่มีสินค้าในระบบ</div>
    <?php endif; ?>

    <!-- รายการสินค้า -->
    <div class="row grid-gap">
      <?php foreach ($products as $product): ?>
        <div class="col-12 col-sm-6 col-lg-4 mb-3">
          <div class="card card-modern h-100">
            <!-- ถ้ามีรูปสินค้า สามารถเพิ่ม <img class="card-img-top" src="..." alt=""> ได้ที่นี่ -->
            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-1"><?= htmlspecialchars($product['product_name']) ?></h5>
              <h6 class="card-subtitle text-muted mb-2">
                <?= htmlspecialchars($product['category_name'] ?? 'ไม่ระบุหมวดหมู่') ?>
              </h6>

              <p class="card-text mb-2">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
              </p>

              <p class="mb-3">
                <strong>ราคา:</strong> <?= number_format((float)$product['price'], 2) ?> บาท
              </p>

              <div class="mt-auto d-flex align-items-center justify-content-between">
                <?php if ($isLoggedIn): ?>
                  <form action="cart.php" method="post" class="m-0">
                    <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-success btn-sm btn-pill">เพิ่มในตะกร้า</button>
                  </form>
                <?php else: ?>
                  <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
                <?php endif; ?>

                <a href="Product_detail.php?id=<?= (int)$product['product_id'] ?>"
                   class="btn btn-outline-primary btn-sm btn-pill">
                  ดูรายละเอียด
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
