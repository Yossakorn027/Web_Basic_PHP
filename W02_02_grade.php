<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculate Grade</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">PHP check Grade A-E from Score</h1>
        <hr>
        <p class="text-center">กรุณากรอกคะเเนนเพื่อทำการตรวจสอบว่าได้เกรดอะไร</p>
        
        <form action="" method="post" class="text-center ">
            <div class="form-group">
                <input type="number" name="Score" id="Score" value="<?php echo isset($_POST['Score'])  ? $_POST['Score'] : '' ; ?>" class="form-control w-50 mx-auto" placeholder="Enter a Score" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3 mb-3">check</button>
            <button type="button" class="btn btn-secondary mt-3 mb-3 " onclick="clearGrade()">reset</button>
        </form>
        


        
        <!-- เเสดงผลลัพธ์ -->
         <div id="grade">
        <?php 
        $get_Score = $_POST['Score'] ?? null;
        if($get_Score >= 80 ){
            echo "<h3 class='text-success text-center'>Your grade is A</h3>";
        }else if($get_Score >= 70){
            echo "<h3 class='text-success text-center'>Your grade is B</h3>";
        }else if($get_Score >= 60){
            echo "<h3 class='text-success text-center'>Your grade is C</h3>";
        }else if($get_Score >= 50){
            echo "<h3 class='text-success text-center'>Your grade is D</h3>";
        }else {
            echo "<h3 class='text-danger text-center'>Your grade is E</h3>";
        }
    ?>
    </div>
    <hr>
</div>
    <a href="index.php">HOME</a>




    <script>
        // ฟังก์ชันสำหรับล้างผลลัพธ์เกรดและช่องกรอกคะแนน
        function clearGrade() {
            document.getElementById('grade').innerHTML = '';
            document.getElementById('Score').value = '';
        }  
    </script>

</body>
</html>