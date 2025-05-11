<?php
session_start();

// التحقق من إذا كان البائع قد سجل الدخول
if (!isset($_SESSION['email'])) {
    header("Location: ELogIn.php");
    exit;
}

$host = 'localhost';
$dbname = 'add_project';
$user = 'root';
$password = '12345678';

try {
    // الاتصال بقاعدة البيانات
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // استرداد البريد الإلكتروني للبائع من الجلسة
    $email = $_SESSION['email'];

    // استرداد تفاصيل البائع من قاعدة البيانات
    $sql = "SELECT * FROM projects WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $project = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بروفايل المشروع</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="ESeller.css">
  

</head>
<body> <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>لوحة البائع</h2>
            <ul class="nav">
                <li><a href="ESeller.php" ><i class="fas fa-box"></i> منتجاتي</a></li>
                <li><a href="project_profile.php"class="active"><i class="fas fa-user"></i> الملف الشخصي</a></li>
                <li><a href="Addproduct.php"><i class="fas fa-plus-circle"></i> إضافة منتج </a></li>
              
               
            </ul>
        </aside>
        <main class="main-content">
<!-- زر الرجوع -->
<a href="javascript:history.back()" class="back-btn">
    <svg xmlns="http://www.w3.org/2000/svg" height="22" viewBox="0 0 24 24" width="22" fill="#3c5f4b">
        <path d="M0 0h24v24H0z" fill="none"/>
        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
    </svg>
    <span>رجوع للصفحة السابقة</span>
</a>


<!-- بيانات المشروع -->
<div class="profile-container">
    <h1>معلومات المشروع</h1>

    <div class="info"><strong>اسم المشروع:</strong> <?= htmlspecialchars($project['business_name']) ?></div>
    <div class="info"><strong>نوع المستخدم:</strong> <?= htmlspecialchars($project['user_type']) ?></div>
    <div class="info"><strong>رقم التواصل:</strong> <?= htmlspecialchars($project['contact_number']) ?></div>
    <div class="info"><strong>الإيميل:</strong> <?= htmlspecialchars($project['email']) ?></div>
    <div class="info"><strong>الوصف:</strong> <?= nl2br(htmlspecialchars($project['description'])) ?></div>
    <div class="info"><strong>التصنيف:</strong> <?= htmlspecialchars($project['category']) ?></div>
    <div class="info">
        <strong>الحالة:</strong>
        <span class="status <?= $project['activated'] ? 'active' : 'inactive' ?>">
            <?= $project['activated'] ? 'مفعل' : 'غير مفعل' ?>
        </span>
    </div>
</div>

</main>
</div>
</body>
</html>
