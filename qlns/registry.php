<?php
// Kết nối đến cơ sở dữ liệu
$conn = mysqli_connect("localhost", "root", "", "qlns");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Khởi tạo các biến và thông báo lỗi
$username = $password = $fullname = $email = $role = "";
$error_message = "";

// Xử lý khi form đăng ký được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $role = $_POST["role"];

    // Kiểm tra xem tên đăng nhập đã tồn tại chưa
    $check_username_sql = "SELECT * FROM users WHERE username='$username'";
    $check_username_result = mysqli_query($conn, $check_username_sql);
    if (mysqli_num_rows($check_username_result) > 0) {
        $error_message = "Tên đăng nhập đã tồn tại.";
    } else {
        // Thêm người dùng mới vào cơ sở dữ liệu
        $insert_user_sql = "INSERT INTO users (username, password, fullname, email, role) 
                            VALUES ('$username', '$password', '$fullname', '$email', '$role')";
        if (mysqli_query($conn, $insert_user_sql)) {
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Đăng ký thất bại: " . mysqli_error($conn);
        }
    }
}

// Đóng kết nối
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Đăng Ký</h2>
        <?php if(!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Tên Đăng Nhập:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật Khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="fullname">Họ và Tên:</label>
                <input type="text" id="fullname" name="fullname" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit">Đăng Ký</button>
        </form>
    </div>
</body>
</html>
