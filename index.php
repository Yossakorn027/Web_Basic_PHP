<?php
session_start();
require_once 'config.php';

$isLoggedIn = isset($_SESSION['user_id']);

$stmt = $conn->query("SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>หน้าหลักสินค้า</title>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Kanit', sans-serif;
      min-height: 100vh;
      background-color: #d0ebff;
      /* ฟ้าอ่อนเหมือนหน้า Login */
    }

    .topbar {
      background-color: #74c0fc;
      box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
    }

    h1 {
      color: #084298;
      font-weight: 800;
    }

    .product-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, .1);
      transition: transform .2s ease, box-shadow .2s ease;
      height: 100%;
    }

    .product-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 28px rgba(0, 0, 0, .15);
    }

    .product-thumb {
      height: 180px;
      object-fit: cover;
      border-radius: 12px;
    }

    .product-title {
      font-size: 1.05rem;
      font-weight: 600;
      color: #0d3b66;
    }

    .product-meta {
      font-size: .85rem;
      color: #6c757d;
      text-transform: uppercase;
    }

    .price {
      font-weight: 700;
      color: #1971c2;
      background: #e7f5ff;
      border-radius: 999px;
      padding: .25rem .75rem;
      display: inline-block;
    }

    .btn {
      border-radius: 999px;
    }
  </style>
</head>

<body>

  <!-- Topbar -->
  <nav class="topbar">
    <div class="container py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
      <h1 class="m-0">รายการสินค้า</h1>
      <div class="d-flex flex-wrap align-items-center gap-2">
        <?php if ($isLoggedIn): ?>
          <span class="me-2 text-white fw-semibold d-none d-md-inline">
            สวัสดี, <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)
          </span>
          <a href="profile.php" class="btn btn-outline-light"><i class="bi bi-person"></i> โปรไฟล์</a>
          <a href="cart.php" class="btn btn-outline-light"><i class="bi bi-bag"></i> ตะกร้า</a>
          <a href="orders.php" class="btn btn-outline-light"><i class="bi bi-bag"></i> ประวัติการสั่งซื้อ</a>
          <a href="logout.php" class="btn btn-outline-light"><i class="bi bi-door-open"></i> ออก</a>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary">เข้าสู่ระบบ</a>
          <a href="register.php" class="btn btn-outline-primary">สมัครสมาชิก</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <!-- Content -->
  <main class="container my-4">
    <div class="row g-4">
      <?php foreach ($products as $p): ?>
        <?php
        $img = !empty($p['image'])
          ? 'product_images/' . rawurlencode($p['image'])
          : 'product_images/no-image.jpg';

        $isNew = isset($p['created_at']) && (time() - strtotime($p['created_at']) <= 7 * 24 * 3600);
        $isHot = (int) $p['stock'] > 0 && (int) $p['stock'] < 5;
        ?>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card product-card h-100 p-3">
            <?php if ($isNew): ?>
              <span class="badge bg-success position-absolute top-0 start-0 m-2">NEW</span>
            <?php elseif ($isHot): ?>
              <span class="badge bg-danger position-absolute top-0 start-0 m-2">HOT</span>
            <?php endif; ?>

            <a href="product_detail.php?id=<?= (int) $p['product_id'] ?>">
              <img src="<?= htmlspecialchars($img) ?>" class="img-fluid w-100 product-thumb mb-3">
            </a>

            <div class="d-flex flex-column">
              <div class="product-meta mb-1"><?= htmlspecialchars($p['category_name'] ?? 'ทั่วไป') ?></div>
              <a href="product_detail.php?id=<?= (int) $p['product_id'] ?>" class="text-decoration-none">
                <div class="product-title"><?= htmlspecialchars($p['product_name']) ?></div>
              </a>
              <div class="price my-2"><?= number_format((float) $p['price'], 2) ?> บาท</div>

              <div class="mt-auto d-flex gap-2">
                <?php if ($isLoggedIn): ?>
                  <form action="cart.php" method="post" class="d-inline-flex gap-2 flex-fill">
                    <input type="hidden" name="product_id" value="<?= (int) $p['product_id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                      <i class="bi bi-bag-plus"></i> เพิ่มในตะกร้า
                    </button>
                  </form>
                <?php else: ?>
                  <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
                <?php endif; ?>
                <a href="product_detail.php?id=<?= (int) $p['product_id'] ?>" class="btn btn-sm btn-outline-primary">
                  รายละเอียด
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (!$products): ?>
        <div class="col-12">
          <div class="alert alert-warning">ยังไม่มีสินค้าในระบบ</div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>