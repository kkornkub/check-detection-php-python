<?php
// กำหนดค่า error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

function analyzeCrack($image_path) {
    // ตรวจสอบว่าไฟล์มีอยู่จริง
    if (!file_exists($image_path)) {
        return array('error' => 'Image file not found');
    }

    try {
        // อ่านไฟล์รูปและแปลงเป็น base64
        $image_data = base64_encode(file_get_contents($image_path));
        
        // ระบุ path ที่แน่นอนของ Python interpreter และ script
        $python_path = 'C:\\Users\\kkorn\\AppData\\Local\\Programs\\Python\\Python313\\python.exe'; // แก้ไขตาม path ที่ติดตั้ง Python
        $script_path = __DIR__ . '\\crack_detection.py'; // ใช้ __DIR__ เพื่อหา path ปัจจุบัน
        
        // เรียกใช้ Python script
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout
            2 => array("pipe", "w")   // stderr
        );
        
        // สร้าง command สำหรับเรียก Python
        $command = sprintf('"%s" "%s"', $python_path, $script_path);
        
        $process = proc_open($command, $descriptorspec, $pipes);
        
        if (is_resource($process)) {
            // ส่งข้อมูลรูปภาพไปยัง Python script
            fwrite($pipes[0], $image_data);
            fclose($pipes[0]);
            
            // รับผลลัพธ์
            $output = stream_get_contents($pipes[1]);
            $errors = stream_get_contents($pipes[2]);
            
            fclose($pipes[1]);
            fclose($pipes[2]);
            
            $return_value = proc_close($process);
            
            // ตรวจสอบข้อผิดพลาด
            if ($return_value !== 0 || !empty($errors)) {
                return array('error' => $errors);
            }
            
            // แปลงผลลัพธ์เป็น array
            $results = json_decode($output, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return array('error' => 'Invalid JSON response from Python script');
            }
            
            return $results;
        }
        
        return array('error' => 'Failed to start Python process');
    } catch (Exception $e) {
        return array('error' => $e->getMessage());
    }
}

// ตรวจสอบการอัปโหลดไฟล์
if(isset($_POST["submit"])) {
    $target_dir = "uploads/"; // สร้างโฟลเดอร์ uploads ในโปรเจค
    
    // สร้างโฟลเดอร์ถ้ายังไม่มี
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // ตรวจสอบว่าเป็นไฟล์รูปภาพจริง
    if(isset($_FILES["fileToUpload"]["tmp_name"]) && !empty($_FILES["fileToUpload"]["tmp_name"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // ตรวจสอบนามสกุลไฟล์
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Sorry, only JPG, JPEG & PNG files are allowed.";
        $uploadOk = 0;
    }

    // ตรวจสอบว่าสามารถอัปโหลดได้หรือไม่
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            // วิเคราะห์รอยร้าว
            $results = analyzeCrack($target_file);
            
            if (isset($results['error'])) {
                echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($results['error']) . "</div>";
            } else {
                echo "<div class='alert alert-success'>Analysis completed successfully!</div>";
                echo "<div class='results'>";
                echo "<p>Average crack width: " . htmlspecialchars($results['average_width']) . " cm</p>";
                echo "<p>Severity: " . htmlspecialchars($results['severity']) . "</p>";
                echo "<div class='row'>";
                echo "<div class='col-md-6'>";
                echo "<h4>Original Image:</h4>";
                echo "<img src='" . htmlspecialchars($target_file) . "' class='img-fluid'>";
                echo "</div>";
                echo "<div class='col-md-6'>";
                echo "<h4>Processed Image:</h4>";
                echo "<img src='data:image/jpeg;base64," . $results['processed_image'] . "' class='img-fluid'>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crack Analysis System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">ระบบวิเคราะห์รอยร้าว</h2>
            </div>
            <div class="card-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label for="fileToUpload" class="form-label">เลือกไฟล์รูปภาพ:</label>
                        <input type="file" class="form-control" name="fileToUpload" id="fileToUpload" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary w-100">วิเคราะห์รูปภาพ</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>