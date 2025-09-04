<?php
require_once 'config.php';

$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $fname = trim($_POST['fname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if (empty($username) || empty($fname) || empty($email) || empty($password) || empty($cpassword)) {
        $error[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "อีเมลไม่ถูกต้อง";
    } elseif ($password !== $cpassword) {
        $error[] = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน";
    } else {
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $error[] = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว";
        }
    }

    if (empty($error)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users(username,full_name,email,password,role) VALUES (?,?,?,?,'member')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $fname, $email, $hashedPassword]);

        header("Location: login.php?register=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #d0ebff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-box {
            background: #ffffff;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 800px;
            animation: fadeIn 1s ease-in-out;
        }

        .form-box h2 {
            font-weight: bold;
            color: #2d3436;
        }

        .btn-primary {
            background: #0984e3;
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #74b9ff;
            color: #2d3436;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="form-box">
        <h2 class="text-center mb-4">สมัครสมาชิก</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($error as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" name="username" class="form-control" id="username"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                        placeholder="Username" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="fname" class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" name="fname" class="form-control" id="fname"
                        value="<?= isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : '' ?>"
                        placeholder="Full Name" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control" id="email"
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" placeholder="Email"
                    required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password"
                        required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="cpassword" class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="cpassword" class="form-control" id="cpassword"
                        placeholder="Confirm Password" required>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5">สมัครสมาชิก</button>
                <a href="login.php" class="btn btn-outline-secondary btn-lg px-5 ms-2">เข้าสู่ระบบ</a>
            </div>
        </form>
    </div>
</body>

</html>