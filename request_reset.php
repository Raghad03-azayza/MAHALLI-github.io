<?php
require 'db_connection.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM projects WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $message = "❌ البريد غير مسجل.";
    } else {
        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", strtotime("+24 hours"));
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires);
        $stmt->execute();

        $link = "http://localhost//edited//verify_token.php?token=$token";

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yousefamjdgpt@gmail.com';
        $mail->Password = 'nytxvvjhrwpjtist';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = "UTF-8";

        $mail->setFrom('yousefamjdgpt@gmail.com', 'محلي ');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'إعادة تعيين كلمة المرور';
        $mail->Body = "اضغط على الرابط التالي لتعيين كلمة مرور جديدة:<br><a href='$link'>$link</a>";

        $mail->send();
        $message = "✅ تم إرسال رابط الاستعادة إلى بريدك.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلب استعادة كلمة المرور</title>
    <link rel="stylesheet" href="resetPassword.css">

    <!-- <style>
        body { font-family: sans-serif; background: #f0f0f0; }
        .box { background: #fff; padding: 20px; width: 400px; margin: 100px auto; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        .box input[type=email], .box button { width: 100%; padding: 10px; margin-top: 10px; }
        .message { margin-top: 15px; color: green; }
    </style> -->
</head>
<body>
<header>
  <img src="uploads/logo.png" alt="Logo">
</header>
<div class="box">
    <h3>استعادة كلمة المرور</h3>
    <div class="login-container">
    <form method="POST" class="login-form">
        <div class="input-group">

        <input type="email" name="email" placeholder="أدخل بريدك الإلكتروني" required>
        </div>
        <button type="submit">إرسال رابط الاستعادة</button>

    </form>
    </div>
    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
</div>
</body>
</html>
