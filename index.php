<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <!-- Main Content -->
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 500px; width:100%;">
            <div class="card-body text-center">
                <h3 class="mb-4 text-success">ยินดีต้อนรับ </h3>
                <p class="fs-5">ผู้ใช้ : 
                    <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                    (<?= htmlspecialchars($_SESSION['role']) ?>)
                </p>
                <a href="logout.php" class="btn btn-danger w-100">ออกจากระบบ</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
