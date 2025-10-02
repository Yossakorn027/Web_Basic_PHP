<?php
require_once 'config.php';

$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student = trim($_POST['Key']);
    $username = trim($_POST['f_name']);
    $Lname = trim($_POST['L_name']);
    $email = trim($_POST['mail']);
    $number = trim($_POST['tel']);
    $age = trim($_POST['age']);

    if (empty($student) || empty($username) || empty($Lname) || empty($email) || empty($number) || empty($age)) {
        $error[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "อีเมลไม่ถูกต้อง";
    } else {
        $sql = "SELECT * FROM tb_664230027 WHERE f_name = ? OR mail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $error[] = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO tb_664230027 (std_id, f_name, L_name, mail, tel, age, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'member')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$student, $username, $Lname, $email, $number, $age]);

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
    <title>เพิ่มนักศึกษา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>

    </style>
</head>

<body>
    <div class="form-box">
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="card shadow-lg p-4 w-100" style="max-width: 700px;">

                <h2 class="text-center mb-4 ">เพิ่มนักศึกษา</h2>

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
                        <div class=" mb-3">
                            <label for="Key" class="form-label">รหัสนักศึกษา</label>
                            <input type="text" name="Key" class="form-control" id="Key"
                                value="<?= isset($_POST['Key']) ? htmlspecialchars($_POST['Key']) : '' ?>"
                                placeholder="เช่น 650123456" required>
                        </div>

                        <div class=" mb-3">
                            <label for="f_name" class="form-label">ชื่อ</label>
                            <input type="text" name="f_name" class="form-control" id="f_name"
                                value="<?= isset($_POST['f_name']) ? htmlspecialchars($_POST['f_name']) : '' ?>"
                                placeholder="ชื่อ" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class=" mb-3">
                            <label for="L_name" class="form-label">นามสกุล</label>
                            <input type="L_name" name="L_name" class="form-control" id="L_name" placeholder="นามสกุล"
                                required>
                        </div>

                        <div class=" mb-3">
                            <label for="mail" class="form-label">อีเมล</label>
                            <input type="mail" name="mail" class="form-control" id="mail"
                                value="<?= isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : '' ?>"
                                placeholder="example@gmail.com" required>
                        </div>


                        <div class=" mb-3">
                            <label for="tel" class="form-label">เบอร์โทร</label>
                            <input type="tel" name="tel" class="form-control" id="tel" placeholder="08XXXXXXXX"
                                required>
                        </div>
                    </div>

                    <div class=" mb-3">
                        <label for="age" class="form-label">อายุ</label>
                        <input type="age" name="age" class="form-control" id="age" placeholder="000" required>
                    </div>

                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-outline-secondary btn-lg px-5 ms-2">ดูรายการ</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5">เพิ่มข้อมูล</button>
                    </div>
            </div>

            </form>
        </div>

</body>

</html>