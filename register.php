<?php
require_once 'config.php';

$error = [];  // Array to hold error messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //รับค่าจาก form 
    $username = trim($_POST['username']);
    $fname = trim($_POST['fname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];


    //ตรวจสอบว่ากรอกข้ออมูลมาครบหรือไม่ (empty)
    if (empty($username) || empty($fname) || empty($email) || empty($password) || empty($cpassword)) {
        $error[] = "กรุณากรอกข้อมุลให้ครบทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //ตรวจสอบรูปแแบบemail
        $error[] = "อีเมลไม่ถูกต้อง";
    } elseif ($password !== $cpassword) {
        //ตรวจสอบว่ารหัสผ่านเเละยืนยันรหัสผ่านตรงกันหรือไม่
        $error[] = "รหัสผ่ำนเเละยืนยันรหัสผ่านไม่ตรงกัน";
    } else {
        //ตรวจสอบว่ามีชื่อผู้ใช้หรืออีเมลถูกใช้ไปเเล้วหรือไม่
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $error[] = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว";
        }

    }
    if (empty($error)) { //ถ้าไม่มีข้อผิดพลาดใดๆ
        //นำข้อมูลบันทึกลงฐานข้อมูล
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(username,full_name,email,password,role) VALUES (?,?,?,?,'member')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $fname, $email, $hashedPassword]);

        //ถ้าบันทึกสำเร็จ ให้เปลี่ยนเส้นทางไปหน้า login
        header("Location: login.php?register=success");
        exit(); //หยุดการทำงานของสคริปต์หลังจากเปลี่ยนเส้นทาง

    }





}


?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #d0ebff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-box {
            background-color: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 900px;
        }

        h2 {
            font-size: 2rem;
        }

        label {
            font-size: 1.1rem;
        }

        input {
            font-size: 1.05rem;
            padding: 0.75rem;
        }
    </style>
</head>

<body>

    <div class="form-box">
        <h2 class="mb-4 text-center">Register</h2>

        <?php if (!empty($error)): // ถ ้ำมีข ้อผิดพลำด ให้แสดงข ้อควำม ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($error as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                        <!-- ใช ้ htmlspecialchars เพื่อป้องกัน XSS -->
                        <!-- < ? = คือ short echo tag ?> -->
                        <!-- ถ ้ำเขียนเต็ม จะได ้แบบด ้ำนล่ำง -->
                        <?php // echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">User</label>
                    <input type="text" name="username" class="form-control" id="username" placeholder="ชื่อผู้ใช้"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
                </div>
                <div class=" col-md-6 mb-3">
                    <label for="fname" class="form-label">Full Name</label>
                    <input type="text" name="fname" class="form-control" id="fname" placeholder="ชื่อ - นามสกุล"
                        value="<?= isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : '' ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="อีเมล"
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="รหัสผ่าน"
                        required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cpassword" class="form-label">Confirm Password</label>
                    <input type="password" name="cpassword" class="form-control" id="cpassword"
                        placeholder="ยืนยันรหัสผ่าน" required>
                </div>
            </div>

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">Register</button>
                <a href="login.php" class="btn btn-link btn-lg">Login</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>