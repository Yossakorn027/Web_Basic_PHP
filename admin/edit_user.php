<?php
session_start();
require '../config.php';   // เชื่อมต่อ PDO
require 'auth_admin.php';  // ตรวจสอบสิทธิ์ Admin

// ตรวจสอบว่ามีพารามิเตอร์ id มาจริงไหม
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int)$_GET['id'];

// ดึงข้อมูลสมาชิก (เฉพาะ role = member)
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'member'");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // ใช้สไตล์เดียวกับหน้าอื่น ๆ
    echo '<!doctype html><html lang="th"><head>
            <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>ไม่พบสมาชิก</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
              body{background-color:#d0ebff;min-height:100vh;margin:0;display:flex;align-items:center;justify-content:center;padding:24px 12px;}
              .page-box{background:#fff;width:100%;max-width:700px;border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,.20);padding:24px;}
              .btn-pill{border-radius:10px;}
            </style>
          </head><body>
          <div class="page-box">
            <div class="alert alert-danger mb-3">ไม่พบสมาชิก</div>
            <a href="users.php" class="btn btn-outline-secondary btn-pill">← กลับหน้ารายชื่อสมาชิก</a>
          </div>
          </body></html>';
    exit;
}

$error = null;

// เมื่อกด Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username'] ?? '');
    $full_name  = trim($_POST['full_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    // ตรวจสอบความครบถ้วน + รูปแบบอีเมล
    if ($username === '' || $email === '') {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    // ตรวจสอบซ้ำ (username/email ชนกับคนอื่น)
    if (!$error) {
        $chk = $conn->prepare("SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
        $chk->execute([$username, $email, $user_id]);
        if ($chk->fetch()) {
            $error = "Username หรือ Email มีอยู่ในระบบแล้ว";
        }
    }

    // ตรวจรหัสผ่าน (กรณีต้องการเปลี่ยน)
    $updatePassword = false;
    $hashed = null;
    if (!$error && ($password !== '' || $confirm !== '')) {
        if (strlen($password) < 6) {
            $error = "รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร";
        } elseif ($password !== $confirm) {
            $error = "รหัสผ่านใหม่กับยืนยันรหัสผ่านไม่ตรงกัน";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $updatePassword = true;
        }
    }

    // อัปเดตข้อมูล
    if (!$error) {
        if ($updatePassword) {
            $sql  = "UPDATE users SET username = ?, full_name = ?, email = ?, password = ? WHERE user_id = ?";
            $args = [$username, $full_name, $email, $hashed, $user_id];
        } else {
            $sql  = "UPDATE users SET username = ?, full_name = ?, email = ? WHERE user_id = ?";
            $args = [$username, $full_name, $email, $user_id];
        }
        $upd = $conn->prepare($sql);
        $upd->execute($args);

        header("Location: users.php");
        exit;
    }

    // สะท้อนค่ากลับในฟอร์ม
    $user['username']  = $username;
    $user['full_name'] = $full_name;
    $user['email']     = $email;
}

// แสดงชื่อผู้ใช้ (ถ้ามี)
$usernameDisplay = htmlspecialchars($_SESSION['username'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสมาชิก</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ใช้เวอร์ชันเดียวกับหน้าที่ปรับก่อนหน้า -->
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
            max-width:900px;
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
        .dash-title{
            font-size:1.8rem;
            font-weight:700;
            margin-bottom:.25rem;
            text-align:center;
        }
        .dash-sub{
            text-align:center;
            color:#6c757d;
            margin-bottom:1.25rem;
        }
        .top-bar{
            display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:1rem;
        }
    </style>
</head>
<body>

<div class="page-box">
    <div class="dash-title">แก้ไขข้อมูลสมาชิก</div>
    <div class="dash-sub">ยินดีต้อนรับ, <span class="fw-semibold"><?= $usernameDisplay ?></span></div>

    <div class="top-bar">
        <a href="users.php" class="btn btn-outline-secondary btn-pill">← กลับหน้ารายชื่อสมาชิก</a>
        <a href="../logout.php" class="btn btn-outline-secondary btn-pill">ออกจากระบบ</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card card-modern">
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control" required
                           value="<?= htmlspecialchars($user['username']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" name="full_name" class="form-control"
                           value="<?= htmlspecialchars($user['full_name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required
                           value="<?= htmlspecialchars($user['email']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">รหัสผ่านใหม่ <small class="text-muted">(เว้นว่างหากไม่ต้องการเปลี่ยน)</small></label>
                    <input type="password" name="password" class="form-control" autocomplete="new-password">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                    <input type="password" name="confirm_password" class="form-control" autocomplete="new-password">
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary btn-pill">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
