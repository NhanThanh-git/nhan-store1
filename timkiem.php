<?php
// Kết nối MySQL
$servername = "127.0.0.1";
$username = "root";
$password = ""; // hoặc mật khẩu bạn đã đặt
$dbname = "sanpham";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
  die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy từ khóa tìm kiếm từ form
$keyword = isset($_GET['q']) ? $_GET['q'] : '';

// Truy vấn dữ liệu có chứa từ khóa
$sql = "SELECT * FROM sanpham WHERE sanpham LIKE ?";
$stmt = $conn->prepare($sql);
$tim = "%" . $keyword . "%";
$stmt->bind_param("s", $tim);
$stmt->execute();

$result = $stmt->get_result();

// Hiển thị kết quả
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "Tên sản phẩm: " . $row["ten_sanpham"] . "<br>";
  }
} else {
  echo "Không tìm thấy sản phẩm nào.";
}

$conn->close();
?>
