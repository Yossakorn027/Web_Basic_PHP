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
            box-shadow: 0px 6px 20px rgba(0,0,0,0.25); 
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
    <form action="" method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="username" class="form-label">User</label>
                <input type="text" name="username" class="form-control" id="username" placeholder="ชื่อผู้ใช้" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="fname" class="form-label">Full Name</label>
                <input type="text" name="fname" class="form-control" id="fname" placeholder="ชื่อ - นามสกุล" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="อีเมล" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="รหัสผ่าน" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cpassword" class="form-label">Confirm Password</label>
                <input type="password" name="cpassword" class="form-control" id="cpassword" placeholder="ยืนยันรหัสผ่าน" required>
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
