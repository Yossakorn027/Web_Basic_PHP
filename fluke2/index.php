<?php
require_once 'config.php';

$students = $conn->query("SELECT * FROM tb_664230027 ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">

    <style>
        .container {
            max-width: 1000px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">รายการนักศึกษา</h1>

        <a href="add_student.php" class="btn btn-success mb-3">เพิ่มข้อมูล</a>

        <table id="studentTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ID</th>
                    <th>ชื่อ</th>
                    <th>สกุล</th>
                    <th>อีเมล</th>
                    <th>เบอร์โทร</th>
                    <th>เวลาสร้าง</th>
                    <th>อายุ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $index => $student): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($student['id']) ?></td>
                        <td><?= htmlspecialchars($student['f_name']) ?></td>
                        <td><?= htmlspecialchars($student['L_name']) ?></td>
                        <td><?= htmlspecialchars($student['mail']) ?></td>
                        <td><?= htmlspecialchars($student['tel']) ?></td>
                        <td><?= htmlspecialchars($student['created_at']) ?></td>
                        <td><?= htmlspecialchars($student['age']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    <script>
        // Initialize DataTable
        new DataTable('#studentTable');
    </script>
</body>

</html>