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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ยินดีต้อนรับสู่หน้าหลัก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #d0ebff;
            /* โทนเดียวกับหน้า Login/Admin */
            margin: 0;
            min-height: 100vh;
        }

        .page-box {
            max-width: 1200px;
            margin: 24px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .20);
            padding: 24px;
        }

        .btn-pill {
            border-radius: 10px;
        }

        .btn-lgx {
            padding: .65rem 1rem;
            font-size: 1.05rem;
        }

        .card-modern {
            border: 1px solid rgba(0, 0, 0, .06);
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .08);
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
            background: #f8f9fa;
        }

        .card-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .12);
            background: #ffffff;
        }

        .card-modern .card-title {
            font-weight: 700;
        }

        .header-bar {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .welcome {
            font-size: 1.1rem;
            color: #495057;
        }

        .grid-gap {
            row-gap: 1rem;
        }
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

        <div class="row g-4"> <!-- EDIT C -->
            <?php foreach ($products as $p): ?>
                <!-- TODO==== เตรียมรูป / ตกแต่ง badge / ดำวรีวิว ==== -->
                <?php
                // เตรียมรูป
                $img = !empty($p['image'])
                    ? 'product_images/' . rawurlencode($p['image'])
                    : 'product_images/no-image.jpg';
                // ตกแต่ง badge: NEW ภำยใน 7 วัน / HOT ถ ้ำสต็อกน้อยกว่ำ 5
                $isNew = isset($p['created_at']) && (time() - strtotime($p['created_at']) <= 7 * 24 * 3600);
                $isHot = (int) $p['stock'] > 0 && (int) $p['stock'] < 5;
                // ดำวรีวิว (ถ ้ำไม่มีใน DB จะโชว์ 4.5 จ ำลอง; ถ ้ำมี $p['rating'] ให้แทน)
                $rating = isset($p['rating']) ? (float) $p['rating'] : 4.5;
                $full = floor($rating); // จ ำนวนดำวเต็ม (เต็ม 1 ดวง) , floor ปัดลง
                $half = ($rating - $full) >= 0.5 ? 1 : 0; // มีดำวครึ่งดวงหรือไม่
                ?>
                <div class="col-12 col-sm-6 col-lg-3"> <!-- EDIT C -->
                    <div class="card product-card h-100 position-relative"> <!-- EDIT C -->
                        <!-- TODO====check $isNew / $isHot ==== -->
                        <?php if ($isNew): ?>
                            <span class="badge bg-success badge-top-left">NEW</span>
                        <?php elseif ($isHot): ?>
                            <span class="badge bg-danger badge-top-left">HOT</span>
                        <?php endif; ?>
                        <!-- TODO====show Product images ==== -->
                        <a href="product_detail.php?id=<?= (int) $p['product_id'] ?>" class="p-3 d-block">
                            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['product_name']) ?>"
                                class="img-fluid w-100 product-thumb">
                        </a>
                        <div class="px-3 pb-3 d-flex flex-column"> <!-- EDIT C -->
                            <!-- TODO====div for category, heart ==== -->
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="product-meta">
                                    <?= htmlspecialchars($p['category_name'] ?? 'Category') ?>
                                </div>
                                <button class="btn btn-link p-0 wishlist" title="Add to wishlist" type="button">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                            <!-- TODO====link, div for product name ==== -->
                            <a class="text-decoration-none" href="product_detail.php?id=<?= (int) $p['product_id'] ?>">
                                <div class="product-title">
                                    <?= htmlspecialchars($p['product_name']) ?>
                                </div>
                            </a>
                            <!-- TODO====div for rating ==== -->
                            <!-- ดำวรีวิว -->
                            <div class="rating mb-2">
                                <?php for ($i = 0; $i < $full; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                                <?php if ($half): ?><i class="bi bi-star-half"></i><?php endif; ?>
                                <?php for ($i = 0; $i < 5 - $full - $half; $i++): ?><i
                                        class="bi bi-star"></i><?php endfor; ?>
                            </div>
                            <!-- TODO====div for price ==== -->
                            <div class="price mb-3">
                                <?= number_format((float) $p['price'], 2) ?> บำท
                            </div>
                            <!-- TODO====div for button check login ==== -->
                            <div class="mt-auto d-flex gap-2">
                                <?php if ($isLoggedIn): ?>
                                    <form action="cart.php" method="post" class="d-inline-flex gap-2">
                                        <input type="hidden" name="product_id" value="<?= (int) $p['product_id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-sm btn-success">เพิ่มในตะกร ้ำ</button>
                                    </form>
                                <?php else: ?>
                                    <small class="text-muted">เขำ้สรู่ ะบบเพอื่ สั่งซอื้ </small>
                                <?php endif; ?>
                                <a href="product_detail.php?id=<?= (int) $p['product_id'] ?>"
                                    class="btn btn-sm btn-outline-primary ms-auto">ดูรำยละเอียด</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>