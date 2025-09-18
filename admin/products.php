<?php
session_start();
require '../config.php'; // TODO: เชื่อมต่อฐานข้อมูลด้วย PDO
require 'auth_admin.php';

// เพิ่มสินค้าใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);   // floatval() ใช้แปลงเป็น float
    $stock = intval($_POST['stock']);     // intval() ใช้แปลงเป็น integer
    $category_id = intval($_POST['category_id']);
    // ค่าที่ได้จากฟอร์มเป็น string เสมอ

    if (!empty($name) && $price > 0) { // ตรวจสอบชื่อและราคาสินค้า

        $imageName = null;
        if (!empty($_FILES['product_image']['name'])) {
            $file = $_FILES['product_image'];
            $allowed = ['image/jpeg', 'image/png'];
            if (in_array($file['type'], $allowed)) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $imageName = 'product_' . time() . '.' . $ext;
                $path = __DIR__ . '/../porduct_images/' . $imageName;
                move_uploaded_file($file['tmp_name'], $path);
            }
        }
        $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, stock, category_id, image)
        VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $category_id, $imageName]);

        header("Location: products.php");
        exit;
    }
}

// ลบสินค้า
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    header("Location: products.php");
    exit;
}

// ดึงรายการสินค้า
$stmt = $conn->query("SELECT p.*, c.category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.category_id 
                    ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงหมวดหมู่
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ใช้เวอร์ชันเดียวกับหน้าที่ปรับก่อนหน้า -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #d0ebff;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 24px 12px;
        }

        .page-box {
            background: #fff;
            width: 100%;
            max-width: 1200px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, .20);
            padding: 24px;
        }

        .btn-pill {
            border-radius: 10px;
        }

        .card-modern {
            border: 1px solid rgba(0, 0, 0, .06);
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .08);
            background: #f8f9fa;
        }

        .card-modern:hover {
            background: #ffffff;
        }

        .dash-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: .25rem;
            text-align: center;
        }

        .dash-sub {
            text-align: center;
            color: #6c757d;
            margin-bottom: 1.25rem;
        }

        .table thead th {
            background: #f1f5f9;
            border-bottom: 1px solid rgba(0, 0, 0, .08);
        }

        .table tbody td {
            border-color: rgba(0, 0, 0, .06);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="page-box">
        <!-- หัวเรื่อง -->
        <div class="dash-title">จัดการสินค้า</div>
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

        <!-- ฟอร์มเพิ่มสินค้าใหม่ -->
        <div class="card card-modern mb-4">
            <div class="card-body">
                <h5 class="card-title mb-2">เพิ่มสินค้าใหม่</h5>
                <form method="post" enctype=" multipart/form-data" class=" row g-3 mt-1">
                    <div class="col-12 col-md-4">
                        <label class="form-label">ชื่อสินค้า</label>
                        <input type="text" name="product_name" class="form-control" placeholder="เช่น เสื้อยืดคอกลม"
                            required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label">ราคา(บาท)</label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label">จำนวน</label>
                        <input type="number" name="stock" class="form-control" placeholder="0" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">หมวดหมู่</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">เลือกหมวดหมู่</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['category_id'] ?>">
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">รายละเอียดสินค้า</label>
                        <textarea name="description" class="form-control" rows="2"
                            placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">รูปสินค้า(jpg, png)</label>
                        <input type="file" name="product_image" class="form-control">
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
                <h5 class="card-title mb-2">รายการสินค้า</h5>
                <div class="table-responsive mt-2">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ชื่อสินค้า</th>
                                <th>หมวดหมู่</th>
                                <th>ราคา</th>
                                <th>คงเหลือ</th>
                                <th class="text-center" style="width:190px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['product_name']) ?></td>
                                    <td><?= htmlspecialchars($p['category_name']) ?></td>
                                    <td><?= number_format((float) $p['price'], 2) ?> บาท</td>
                                    <td><?= (int) $p['stock'] ?></td>
                                    <td class="text-center">
                                        <a href="edit_products.php?id=<?= (int) $p['product_id'] ?>"
                                            class="btn btn-warning btn-sm btn-pill me-1">แก้ไข</a>
                                        <a href="products.php?delete=<?= (int) $p['product_id'] ?>"
                                            class="btn btn-danger btn-sm btn-pill"
                                            onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?');">ลบ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?><?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">ยังไม่มีสินค้า</td>
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