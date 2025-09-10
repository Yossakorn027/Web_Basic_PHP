<?php
session_start();
require_once '../config.php';
require_once 'auth_admin.php';

// ลบสมาชิก
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    // ป้องกันลบตัวเอง
    if ($user_id != ($_SESSION['user_id'] ?? 0)) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
        $stmt->execute([$user_id]);
    }
    header("Location: users.php");
    exit;
}

// ดึงข้อมูลสมาชิก
$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'member' ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ชื่อผู้ใช้สำหรับแสดงผล
$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสมาชิก</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ใช้เวอร์ชันเดียวกับหน้า Login/Admin -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background-color:#d0ebff; /* โทนเดียวกับหน้า Login/Admin */
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:flex-start;
            margin:0;
            padding:24px 12px;
        }
        .dash-box{
            background:#fff;
            width:100%;
            max-width:1100px;
            padding:2rem;
            border-radius:12px;
            box-shadow:0 6px 20px rgba(0,0,0,.20);
        }
        .dash-title{
            font-size:1.8rem;
            font-weight:700;
            margin-bottom:.25rem;
            text-align:center;
        }
        .dash-sub{
            text-align:center;
            color:#6c757d;
            margin-bottom:1.5rem;
        }
        .btn-pill{
            border-radius:10px;
            padding:.6rem 1rem;
        }
        .table thead th{
            background:#f1f5f9;
            border-bottom:1px solid rgba(0,0,0,.08);
        }
        .table tbody td{
            border-color:rgba(0,0,0,.06);
        }
        .top-bar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
            margin-bottom:1rem;
        }
    </style>
</head>
<body>
    <div class="dash-box">
        <div class="dash-title">จัดการสมาชิก</div>
        <div class="dash-sub">ยินดีต้อนรับ, <span class="fw-semibold"><?= $username ?></span></div>

        <div class="top-bar">
            <a href="index.php" class="btn btn-outline-secondary btn-pill">← กลับหน้าผู้ดูแล</a>
            <a href="../logout.php" class="btn btn-outline-secondary btn-pill">ออกจากระบบ</a>
        </div>

        <?php if (count($users) === 0): ?>
            <div class="alert alert-warning mb-0">ยังไม่มีสมาชิกในระบบ</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ชื่อผู้ใช้</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th>อีเมล</th>
                            <th>วันที่สมัคร</th>
                            <th class="text-center" style="width:180px;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['created_at']) ?></td>
                                <td class="text-center">
                                    <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-warning btn-sm btn-pill me-1">แก้ไข</a>
                                    <a href="users.php?delete=<?= $user['user_id'] ?>"
                                       class="btn btn-danger btn-sm btn-pill"
                                       onclick="return confirm('คุณต้องการลบสมาชิกหรือไม่?');">ลบ</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
