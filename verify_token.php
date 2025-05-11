<?php
require 'db_connection.php';

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("الرابط غير صالح أو منتهي.");
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعيين كلمة مرور جديدة</title>
    <link rel="stylesheet" href="resetPassword.css">

    <!-- <style>
        body { font-family: sans-serif; background: #f9f9f9; }
        .box { background: #fff; padding: 20px; width: 400px; margin: 100px auto; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        .box input, .box button { width: 100%; padding: 10px; margin-top: 10px; }
    </style> -->
</head>
<body>
<header>
  <img src="uploads/logo.png" alt="Logo">
</header>
<div class="box">
    <h3>تعيين كلمة مرور جديدة</h3>
    <div class="login-container">

        <form method="POST" action="reset_password.php" class="login-form">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>"/>
            <div class="input-group">
                <input type="password" name="new_password" placeholder="كلمة المرور الجديدة" required />
            </div>
            <div class="input-group">
                <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" required/>
            </div>
            <button type="submit">تحديث</button>
        </form>
    </div>
</div>
</body>
</html>
