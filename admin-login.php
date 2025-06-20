<?php
session_start();

// Cấu hình cơ sở dữ liệu
$host = 'localhost';
$db = 'shopdb';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Kết nối đến cơ sở dữ liệu
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Không thể kết nối cơ sở dữ liệu: ' . $e->getMessage());
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
    } else {
        // Tìm kiếm người dùng trong cơ sở dữ liệu
        $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && $password === $user['password']) {

            // Đăng nhập hợp lệ
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            // Chuyển hướng dựa trên vai trò
            if ($user['role'] === 'admin') {
                header('Location: index.html');
                exit;
            } else {
                header('Location: customer_dashboard.php');
                exit;
            }
        } else {
            $errors[] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cửa hàng trực tuyến - Đăng nhập</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    
   body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    @media (max-width: 768px) {
      .hide-on-mobile {
        display: none !important;
      }


      .menu a .text {
        display: none;
      }

      .menu a ion-icon {
        font-size: 20px;
      }

      .menu a {
        font-size: 0;
        /* ẩn khoảng trắng dư */
        padding: 3px;
      }

      .menuphai {
        gap: 10px;
      }
    }

    .menu {
      background-color: #222;
      color: white;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .menu a {
      color: white;
      text-decoration: none;
      font-size: 13px;
    }

    .menu a:hover {
      text-decoration: underline;
    }

   
    .menuphai {
      display: flex;
      gap: 20px;
      margin-left: auto;

    }

    .center {
      display: flex;
      justify-content: space-between;
      padding: 0 50px;
      height: 100%;
      max-height: 70px;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
      background-color: white;

    }

    li {
      list-style: none;
    }

    a {
      text-decoration: none;
      color: black;
    }

    .danhmuc {
      flex: 2;
      display: flex;

    }

    .danhmuc>li:hover .sub-danhmuc {
      display: block;
      visibility: visible;

    }

    .danhmuc>li {
      padding: 0 12px;
      position: relative;
    }

    .sub-danhmuc {
      position: absolute;
      width: 280px;
      border: 1px solid #ccc;
      padding: 20px 0;
      background-color: white;
      visibility: hidden;
      transition: 0.3s;
    }

    .danhmuc a {
      padding: 10px;
      font-size: 15px;
      color: black;
      font-family: Arial, Helvetica, sans-serif;
      font-weight: bold;


    }

    .danhmuccon {
      position: relative;
      display: inline-block;
    }

    .danhmuccon::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      height: 2px;
      width: 0;
      background-color: #000;
      transition: width 0.3s ease-in-out;
    }

    .danhmuccon:hover::after {
      width: 100%;
    }

    .sub-danhmuc ul {

      padding-left: 5px;

    }

    .sub-danhmuc ul a {
      display: block;
      font-weight: normal;
      font-size: 15px !important;
      gap: 2px;
      color: #000;
      padding: 5px;
    }

    .sub-danhmuc ul a:hover {
      color: rgb(17, 179, 228);
      text-decoration: none;

    }

    .tenshop {
      flex: 1;
      font-weight: bold;
      font-size: 22px;
    }

    .other {
      flex: 1;
    }

    .thanhngang {
      width: 100%;
      height: 35px;
      background-color: #ece7e7;
    }

    
    .menu-toggle {
      display: none;
      font-size: 24px;
      background: none;
      border: none;
      color: #333;
      cursor: pointer;
    }

    @media (max-width: 1024px) {
      .center {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        position: relative;
        position: sticky;
        top: 0;
        z-index: 1000;
      background-color: white;

      }

      .sub-danhmuc {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none;
        /* Ngăn tương tác */
      }

      .danhmuc>li:hover .sub-danhmuc {
        display: none !important;
        visibility: hidden !important;
      }

      .menu-toggle {
        display: block;
        font-size: 24px;
        background: none;
        border: none;
        color: #333;
        cursor: pointer;
        z-index: 2;
      }

      .btmenu {
        margin-left: auto;
      }


      .menu-content {
        display: none;
        flex-direction: column;
        background-color: rgba(255, 255, 255, 0.95);
        /* nền đục nhẹ */
        padding: 10px;
        width: 100%;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        /* tạo khối nổi */

        z-index: 999;
        /* giúp nổi lên trên ảnh nền */
      }


      .menu-content.show {
        display: flex;
      }

      .center {
        flex-direction: column;
        align-items: flex-start;
        padding: 10px 0px;
      }

      .tenshop {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        font-size: 20px;
        z-index: 1;
      }

      .others {
        width: 100%;
      }
    }

    .ttlh {
      width: 100%;
      height: 200px;
      background-color: #637181;
      /* tăng kích thước icon nếu muốn */
    }

    .mxh {
      display: flex;
      justify-content: center;
      /* căn giữa theo chiều ngang */
      gap: 20px;
      /* khoảng cách giữa các icon */
      margin-top: 20px;
      /* cách phần trên */
      font-size: 40px;
      padding-top: 50px;
    }

    .hehe {
      display: flex;
      justify-content: center;
      /* căn giữa theo chiều ngang */
      gap: 20px;
      /* khoảng cách giữa các icon */
      margin-top: 0px;
      /* cách phần trên */
      font-size: 20px;
      font-family: 'Courier New', monospace;
    }
    .search-box {
  position: relative;
  display: flex;
  align-items: center;
  background-color: #f5f5f5;
  border-radius: 20px;
  padding: 5px 10px;
  width: 250px;
  max-width: 100%;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s;
}

.search-box input[type="text"] {
  border: none;
  outline: none;
  background: transparent;
  padding: 8px 10px;
  font-size: 14px;
  flex: 1;
}

.search-box i.fas.fa-search {
  color: #555;
  font-size: 16px;
  cursor: pointer;
  transition: color 0.3s;
}

.search-box i.fas.fa-search:hover {
  color: #007BFF;
}

@media (max-width: 768px) {
  .search-box {
    width: 90%;
    margin-top: 10px;
  }
}

.content {
      width: 100%;
      max-width: 600px;
      margin: 40px auto;
      height: 500px;
      padding: 20px;
      background: #f9f9f9;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-light);
      display: flex;
      flex-direction: column;
      gap: 32px;
}
     h1 {
      font-size: 36px;
      text-align: center;
    }
    .option-buttons {
      display: flex;
      flex-direction: column;
      gap: 50px;
      padding: 100px;
    }
    p {
        text-align: center;
    }
    
    /* Responsive */
    @media (max-width: 480px) {
      main {
        margin: 40px 12px;
      }
      h1 {
        font-size: 36px;
      }
      button.option-btn {
        font-size: 18px;
        padding: 16px;
      }
    }
    .error {
  background: #fee2e2;
  color: #b91c1c;
  border-radius: 0.5rem;
 
  margin-bottom: 20px; /* Thay đổi margin-bottom để tạo khoảng cách */
  font-weight: 600;
 
}
.dangnhap (
 padding:100px;
  )


/* Bọc toàn bộ form */
form {
  max-width: 400px;
  margin: 80px auto;
  padding: 30px;
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Label */
form label {
  display: block;
  margin-bottom: 8px;
  font-weight: bold;
  color: #333;
}

/* Input */
form input[type="text"],
form input[type="password"] {
  width: 95%;
  padding: 12px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 16px;
  transition: border-color 0.3s ease;
}

form input:focus {
  border-color: #b7154a;
  outline: none;
}

/* Nút submit */
form button[type="submit"] {
  width: 70%;
  padding: 12px;
  display: block;
  margin: 20px auto 0 auto;
  background-color:#b7154a;
  border: none;
  color: white;
  font-size: 16px;  
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

form button:hover {
  background: color #a40e3a;;
}

  </style>
</head>

<body>
    <div class="menu">
      <div class="menuphai">
      <a href="theo-doi-don-hang.jtml"><ion-icon name="bag-handle-outline"></ion-icon> <span class="text">Theo dõi</span></a>
      <a href="lien-he" class="hide-on-mobile"><ion-icon name="call"></ion-icon> <span class="text">Liên hệ</span></a>
      <a href="giohang-1.html"><ion-icon name="cart-outline"></ion-icon> <span class="text">Giỏ hàng</span></a>
      <a href="login.html"><ion-icon name="person-outline"></ion-icon> <span class="text">Đăng nhập</span></a>
    </div>
  </div>

  <div class="center">
    <div class="tenshop">
      <a href="index.html">NHÀN STORE</a>
    </div>
    <div class="btmenu">
      <!-- Nút menu thu gọn -->
      <button class="menu-toggle" onclick="toggleMenu()">&#9776;</button>
    </div>
    <!-- Menu ẩn/hiện -->
    <div class="danhmuc menu-content">
      <li><a href="tatca.html" class="danhmuccon"> SẢN PHẨM <ion-icon name="chevron-down-outline"></ion-icon></a>
        <div class="sub-danhmuc">
          <ul>
            <li> <a href="Nam.html">Giày</a></li>
            <li> <a href="ao.html">Áo</a></li>
            <li> <a href="quan.html">Quần</a></li>
          </ul>
        </div>
      </li>
      <li><a href="hot.html" class="danhmuccon">HOT</a></li>
      <li><a href="treEm.html" class="danhmuccon">TRẺ EM</a></li>
      <li><a href="thethao.html" class="danhmuccon">THỂ THAO</a></li>
      <li><a href="sale.html" class="danhmuccon">SALE</a></li>
    </div>

    <div class="others menu-content">
      <ul>
        <li class="search-box">
          <input type="text" placeholder="Tìm kiếm...">
          <i class="fas fa-search"></i>
        </li>
      </ul>
    </div>
  </div>

  <div class="thanhngang">

  </div>
  

  <div class="content">
    <h1>Đăng nhập</h1>
    <?php if (!empty($errors)): ?>
      <div class="error" role="alert">
        <?php foreach ($errors as $error): ?>
          <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
          <div class="dangnhap">
    <form method="POST" novalidate autocomplete="off">
      <label for="username">Tên đăng nhập</label>
      <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required />

      <label for="password">Mật khẩu</label>
      <input type="password" id="password" name="password" required />

      <button type="submit">Đăng nhập</button>
    </form>
      </div>
  </div>

  <footer>
    <div class="ttlh">
      <div class="mxh">
        <a href="https://www.facebook.com/nhan.1805/">
          <ion-icon name="logo-facebook"></ion-icon></a>
        <a href="https://www.instagram.com/tah_nhan.1805/?hl=vi">
          <ion-icon name="logo-instagram"></ion-icon></a>
      </div>
      <div class="hehe">
        <i>here with me..</i>
      </div>
    </div>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>
