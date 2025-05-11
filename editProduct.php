<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "add_project";

// إنشاء الاتصال
$mysqli = new mysqli($servername, $username, $password, $dbname);

// $mysqli = new mysqli("localhost", "D", "DB_PASSWORD", "add_project");
if ($mysqli->connect_errno)
    die("فشل الاتصال: " . $mysqli->connect_error);
if (!isset($_GET['id']) || !is_numeric($_GET['id']))
    die('معرف غير صالح');
$id = (int) $_GET['id'];
$stmt = $mysqli->prepare("SELECT * FROM product WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$prodRes = $stmt->get_result();
if ($prodRes->num_rows !== 1)
    die('غير موجود');
$product = $prodRes->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = $_POST['product_name'];
    $d = $_POST['description'];
    $p = $_POST['price'];
    $c = $_POST['contact_number'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $t = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $t);
        $img = $t;
    } else
        $img = $product['image_url'];
    $u = $mysqli->prepare("UPDATE product SET product_name=?,description=?,price=?,contact_number=?,image_url=? WHERE id=?");
    $u->bind_param('ssdssi', $n, $d, $p, $c, $img, $id);
    if ($u->execute())
        header('Location:UserManagement.php');
    else
        $error = 'فشل التحديث';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تعديل المنتج</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="b.css">
</head>

<body>
    
<div class="container">
    <aside class="sidebar">
        <h2>لوحة التحكم</h2>
        <ul>
            <li><a href="EIndex.php">الرئيسية</a></li>
            <li><a href="EAdmin.php" class="active">إدارة الطلبات</a></li>
            <li><a href="UserManagement.php">إدارة المستخدمين</a></li>
            <li><a href="EStatistics.php">الاحصائيات </a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>إدارة الطلبات</h1>
            <button class="logout" onclick="window.location.href='ELogIn.php'">
                <i class="fa fa-sign-out-alt"></i> تسجيل الخروج
            </button>
        </header>
    <div class="container2">
        <div class="main-content2">
            <h2>تعديل: <?= htmlspecialchars($product['product_name']) ?></h2>
            <?php if (!empty($error))
                echo "<p class='error'>$error</p>"; ?>
            <form action="" method="post" enctype="multipart/form-data">
                <label>اسم:</label><input type="text" name="product_name"
                    value="<?= htmlspecialchars($product['product_name']) ?>" required />
                <label>الوصف:</label><textarea name="description"
                    required><?= htmlspecialchars($product['description']) ?></textarea>
                <label>السعر:</label><input type="text" step="0.01" name="price" value="<?= $product['price'] ?>"
                    required />


                    <label>طرق التواصل : " الرجاء وضع ; في حال وجود اكتر من طريقه "</label>
                    <input type="text" name="contact_number"
                    value="<?=htmlspecialchars($product['contact_number']) ?>" required>

                <label>الصورة الحالية:</label><img src="<?= $product['image_url'] ?>" style="max-width:150px;" />
                <label>صورة جديدة:</label><input type="file" name="image" accept="image/*" />
                <div class="button-group">
                <button type="submit" id="g">حفظ</button>
                <button class="logout" id="r" onclick="window.location.href='UserManagement.php'">رجوع</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>


            
