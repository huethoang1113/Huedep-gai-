

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Nhân Viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .gender-img {
            width: 30px;
            height: auto;
            vertical-align: middle;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    
    
<?php
// Thông tin cơ sở dữ liệu
$servername = "localhost"; // Địa chỉ máy chủ MySQL
$username = "root"; // Tên người dùng MySQL
$password = ""; // Mật khẩu MySQL
$dbname = "qlns"; // Tên cơ sở dữ liệu MySQL

// Số lượng bản ghi trên mỗi trang
$records_per_page = 5;

session_start(); // Bắt đầu phiên đăng nhập

try {
    // Kết nối đến cơ sở dữ liệu
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Thiết lập chế độ báo lỗi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Lấy vai trò của người dùng
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user_role = $stmt->fetch(PDO::FETCH_ASSOC)['role'];

    // Kiểm tra quyền của người dùng
    if ($user_role != 'admin' && $user_role != 'user') {
        // Nếu không phải admin hoặc user, chuyển hướng đến trang đăng nhập
        header("Location: login.php");
        exit();
    }

    // Xác định trang hiện tại
    $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
    // Tính toán offset (bắt đầu từ bản ghi nào)
    $offset = ($current_page - 1) * $records_per_page;

    // Truy vấn thông tin nhân viên với phân trang
    $sql = "SELECT Nhanvien.MA_NV, Nhanvien.TEN_NV, Nhanvien.PHAI, Nhanvien.NOI_SINH, PHONGBAN.Ten_Phong, Nhanvien.LUONG 
            FROM Nhanvien 
            INNER JOIN PHONGBAN ON Nhanvien.MA_PHONG = PHONGBAN.Ma_Phong 
            ORDER BY Nhanvien.MA_NV 
            LIMIT $offset, $records_per_page";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Hiển thị dữ liệu trong bảng
    echo "<h1>THÔNG TIN NHÂN VIÊN</h1>";
    echo "<table>";
    echo "<tr><th>Mã Nhân Viên</th><th>Tên Nhân Viên</th><th>Giới Tính</th><th>Nơi Sinh</th><th>Tên Phòng</th><th>Lương</th><th>Thao Tác</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['MA_NV'] . "</td>";
        echo "<td>" . $row['TEN_NV'] . "</td>";
        echo "<td>" . $row['PHAI'] . "</td>";
        echo "<td>" . $row['NOI_SINH'] . "</td>";
        echo "<td>" . $row['Ten_Phong'] . "</td>";
        echo "<td>" . $row['LUONG'] . "</td>";
        echo "<td>";
        // Hiển thị biểu tượng chỉnh sửa nếu là admin
        if ($user_role == 'admin') {
            echo "<a href='edit.php?id=" . $row['MA_NV'] . "'><img src='edit.png' alt='Chỉnh sửa'></a> ";
        }
        // Hiển thị biểu tượng xóa nếu là admin
        if ($user_role == 'admin') {
            echo "<a href='delete.php?id=" . $row['MA_NV'] . "'><img src='delete.png' alt='Xóa'></a>";
        }
        echo "</td>";
        echo "</tr>";
    } 
    echo "</table>";

    // Tính toán số lượng trang
    $sql = "SELECT COUNT(*) AS total FROM Nhanvien";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $total_records = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_records / $records_per_page);

    // Hiển thị phân trang
    echo "<div>";
    for ($i = 1; $i <= $total_pages; $i++) {
        echo "<a href='?page=$i'>$i</a> ";
    }
    echo "</div>";

} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
$conn = null; // Đóng kết nối
?>





