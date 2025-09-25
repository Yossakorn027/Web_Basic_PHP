<?php
session_start();
require 'config.php';

// ตรวจสอบการล็อกอิน 
if (!isset($_SESSION['user_id'])) {
    // TODO: ใส่ session ของผู้ใช้
    header("Location: login.php"); // TODO: หน้า login
    exit;
}

$user_id = $_SESSION['user_id']; // กำหนด user_id  

// ดึงรายการสินค้าจากตะกร้า
$stmt = $conn->prepare("SELECT cart.cart_id, cart.quantity, cart.product_id, products.product_name, products.price FROM cart JOIN products ON cart.product_id = products.product_id WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -----------------------------
// คำนวณราคาสินค้ารวม
// -----------------------------
$total = 0;
foreach ($items as $item) {
    $total += $item['quantity'] * $item['price']; // คำนวณจำนวน * ราคา
}
// เมอื่ ผใู้ชก้ดยนื ยันค ำสั่งซอื้ (method POST)
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address']); // TODO: ชอ่ งกรอกทอี่ ยู่
    $city = trim($_POST['city']); // TODO: ชอ่ งกรอกจังหวัด
    $postal_code = trim($_POST['postal_code']); // TODO: ชอ่ งกรอกรหัสไปรษณีย์
    $phone = trim($_POST['phone']); // TODO: ชอ่ งกรอกเบอรโ์ ทรศัพท์
// ตรวจสอบกำรกรอกข ้อมูล
    if (empty($address) || empty($city) || empty($postal_code) || empty($phone)) {
        $errors[] = "กรุณำกรอกข ้อมูลให้ครบถ ้วน"; // TODO: ข ้อควำมแจ้งเตือนกรอกไม่ครบ
    }
    if (empty($errors)) {
        // เริ่ม transaction
        $conn->beginTransaction();
        try {
            // บันทกึขอ้ มลู กำรสั่งซอื้
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$user_id, $total]);
            $order_id = $conn->lastInsertId();
            // บันทกึ รำยกำรสนิ คำ้ใน order_items
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?,
?, ?)");
            foreach ($items as $item) {
                $stmtItem->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                // TODO: product_id, quantity, price
            }
            // บันทกึขอ้ มลู กำรจัดสง่
            $stmt = $conn->prepare("INSERT INTO shipping (order_id, address, city, postal_code, phone) VALUES (?,
?, ?, ?, ?)");
            $stmt->execute([$order_id, $address, $city, $postal_code, $phone]);
            // ลำ้งตะกรำ้สนิ คำ้
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            // ยืนยันกำรบันทึก
            $conn->commit();
            header("Location: orders.php?success=1"); // TODO: หนำ้แสดงผลค ำสั่งซอื้
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $errors[] = "เกิดข ้อผิดพลำด: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ยืนยันการสั่งซื้อ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
    <h2>ยืนยันการสั่งซื้อ</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- แสดงรายการสินค้าจากตะกร้า -->
    <h5>รายการสินค้าจากตะกร้า</h5>
    <ul class="list-group mb-4">
        <?php foreach ($items as $item): ?>
            <li class="list-group-item">
                <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?> =
                <?= number_format($item['price'] * $item['quantity'], 2) ?> บาท
            </li>
        <?php endforeach; ?>
        <li class="list-group-item text-end"><strong>รวมทั้งหมด: <?= number_format($total, 2) ?> บาท</strong></li>
    </ul>

    <!-- ฟอร์มกรอกข้อมูลการจัดส่ง -->
    <form method="post" class="row g-3">
        <div class="col-md-6">
            <label for="address" class="form-label">ที่อยู่</label>
            <input type="text" name="address" id="address" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label for="city" class="form-label">จังหวัด</label>
            <input type="text" name="city" id="city" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label for="postal_code" class="form-label">รหัสไปรษณีย์</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
            <input type="text" name="phone" id="phone" class="form-control">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">ยืนยันการสั่งซื้อ</button>
            <a href="cart.php" class="btn btn-secondary">← กลับไปที่ตะกร้า</a> <!-- TODO: หน้า cart -->
        </div>
    </form>
</body>

</html>