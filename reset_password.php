<?php
require 'db_connection.php';

$msg = "";
$token = $_POST['token'] ?? $_GET['token'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $msg = "❌ كلمتا المرور غير متطابقتين.";
    } elseif (strlen($new_password) < 8) {
        $msg = "❌ يجب أن تكون كلمة المرور 8 خانات على الأقل.";
    } else {
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $msg = "❌ الرابط منتهي أو غير صالح.";
        } else {
            $email = $result->fetch_assoc()['email'];

            $update = $conn->prepare("UPDATE projects SET password = ? WHERE email = ?");
            $update->bind_param("ss", $new_password, $email);
            $update->execute();

            $delete = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $delete->bind_param("s", $email);
            $delete->execute();

            $msg = "✅ تم تحديث كلمة المرور بنجاح.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تحديث كلمة المرور</title>
    <link rel="stylesheet" href="resetPassword.css">

    <!-- <style>
        body { font-family: sans-serif; background: #f0f0f0; }
        .box {
            background: #fff;
            padding: 20px;
            width: 400px;
            margin: 100px auto;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        .message {
            margin-top: 15px;
            font-weight: bold;
            color: #d00;
        }
        .message.success {
            color: green;
        }
    </style> -->
</head>
<body>
<header>
  <img src="uploads/logo.png" alt="Logo">
</header>
<div class="box">
    <h3>تحديث كلمة المرور</h3>
    <div class="login-container">

    <form method="POST" action="reset_password.php" class="login-form">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="input-group">
                <input type="password" name="new_password" placeholder="كلمة المرور الجديدة" required />
            </div>
            <div class="input-group">
                <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" required/>
            </div>
        <button type="submit">تحديث</button>
    </form>
</div>
    <?php if (!empty($msg)): ?>
        <div class="message <?php echo strpos($msg, '✅') !== false ? 'success' : ''; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
