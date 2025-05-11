<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ELogIn.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>تسجيل الدخول</title>
</head>
<?php
session_start();
$host = 'localhost';
$dbname = 'add_project';
$user = 'root';
$password = '12345678';

try {
    // الاتصال بقاعدة البيانات
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $inputPassword = trim($_POST['password']);

        // التحقق من وجود المشروع في قاعدة البيانات وحالته "Accepted"
        $sql = "SELECT * FROM admin WHERE email = :email AND password = :password ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $inputPassword);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // تسجيل الدخول ناجح، فتح صفحة البائع
            $_SESSION['email'] = $email;  // تخزين البريد الإلكتروني في الجلسة
            header("Location: testAdminApi.php");
            exit;
        } else {
            $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة ";
        }
    }
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}






try {
    // الاتصال بقاعدة البيانات
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        $inputPassword = trim($_POST['password']);

        // التحقق من وجود المشروع في قاعدة البيانات وحالته "Accepted"
        $sql = "SELECT * FROM projects WHERE email = :email AND password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $inputPassword);
        $stmt->execute();

        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($project) {
            // إذا تم العثور على المشروع في قاعدة البيانات
            if ($project['activated'] == 0) {
                // إذا كانت حالة التفعيل = 0
                $error = "الحساب غير مفعل. يُرجى مراجعة الدعم الفني.";
            } else {
                // تسجيل الدخول ناجح، فتح صفحة البائع
                $_SESSION['email'] = $email;  // تخزين البريد الإلكتروني في الجلسة
                $_SESSION['project_name'] = $project['business_name'];  // تأكد أن اسم العمود في قاعدة البيانات هو فعلاً business_name
                header("Location: ESeller.php");
                exit;
            }
        } else {
            // إذا لم يتم العثور على المشروع في قاعدة البيانات
            $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة.";
        }

    }
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}

?>



<body>
<header>
  <img src="uploads/logo.png" alt="Logo">
</header>

  <div class="login-container">
    <div class="icon-wrapper">
      <i class="fas fa-user"></i> 
    </div>
    <form  method="POST" class="login-form">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="email"name="email" placeholder="البريد الإلكتروني" required>
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="كلمة المرور" required>
      </div>
      <div class="options">
        
        <a href="request_reset.php">نسيت كلمة المرور؟</a>
      </div>
      <button type="submit">تسجيل الدخول</button>
    </form>
    <div id="dialog-overlay"></div>
    <div id="dialog-box">
        <p id="dialog-message"></p>
        <button onclick="closeDialog()">حسنًا</button>
    </div>
  </div>

  <script >
     <?php if (!empty($error)): ?>
  showDialog("<?= $error ?>");
<?php endif; ?>

function showDialog(message) {
  document.getElementById('dialog-message').textContent = message;
  document.getElementById('dialog-box').style.display = 'block';
  document.getElementById('dialog-overlay').style.display = 'block';

  // تفريغ الحقول
  document.querySelector('[name="email"]').value = '';
  document.querySelector('[name="password"]').value = '';
}

function closeDialog() {
  document.getElementById('dialog-box').style.display = 'none';
  document.getElementById('dialog-overlay').style.display = 'none';
}

  </script>
  
</body>

</html>