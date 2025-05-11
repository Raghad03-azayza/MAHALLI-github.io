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

    // استرداد تفاصيل المشروع للبائع
    $sql = "SELECT * FROM projects WHERE email = :email AND status = 'Accepted' ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        $project_id = $project['id'];

        // إضافة منتج جديد
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
            $product_name = trim($_POST['product_name']);
            $description = trim($_POST['description']);
            $price = trim($_POST['price']);
            $contact_number = trim($_POST['contact_number']);

            // رفع الصورة
            if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == 0) {
                $image_name = $_FILES['image_url']['name'];
                $image_tmp_name = $_FILES['image_url']['tmp_name'];
                $image_path = 'uploads/' . basename($image_name);
                
                // رفع الصورة إلى المجلد
                move_uploaded_file($image_tmp_name, $image_path);
            } else {
                $image_path = ''; // إذا لم يتم رفع صورة
            }

            // إدخال المنتج في قاعدة البيانات
            $sql = "INSERT INTO product (product_name, description, price, image_url, contact_number, project_id) 
                    VALUES (:product_name, :description, :price, :image_url, :contact_number, :project_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':product_name', $product_name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':image_url', $image_path);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':project_id', $project_id);

            if ($stmt->execute()) {
                $success_message = "تم إضافة المنتج بنجاح.";
            } else {
                $error_message = "حدث خطأ أثناء إضافة المنتج.";
            }
        }

        // استرداد جميع المنتجات المرتبطة بالبائع
        $sql = "SELECT * FROM product WHERE project_id = :project_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // تعديل المنتج
        $product_to_edit = null;

if (isset($_GET['id'])) {
    $product_id_to_edit = $_GET['id'];

    // استرداد تفاصيل المنتج من قاعدة البيانات
    $sql = "SELECT * FROM product WHERE id = :product_id AND project_id IN (SELECT id FROM projects WHERE email = :email)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_id', $product_id_to_edit);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $product_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product_to_edit) {
        $error_message = "المنتج غير موجود أو ليس لديك صلاحية تعديل هذا المنتج.";
    }
}


    // معالجة التعديل عند تقديم النموذج
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
        $product_name = trim($_POST['product_name']);
        $description = trim($_POST['description']);
        $price = trim($_POST['price']);
        $contact_number = trim($_POST['contact_number']);
        $image_path = $_POST['current_image_url']; // الاحتفاظ بالصورة الحالية إذا لم يتم رفع صورة جديدة

        // رفع صورة جديدة إذا تم رفعها
        if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == 0) {
            $image_name = $_FILES['image_url']['name'];
            $image_tmp_name = $_FILES['image_url']['tmp_name'];
            $image_path = 'uploads/' . basename($image_name);
            move_uploaded_file($image_tmp_name, $image_path);
        }

        // تحديث المنتج في قاعدة البيانات
        $sql = "UPDATE product SET product_name = :product_name, description = :description, price = :price, image_url = :image_url, contact_number = :contact_number WHERE id = :product_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image_url', $image_path);
        $stmt->bindParam(':contact_number', $contact_number);
        $stmt->bindParam(':product_id', $product_id_to_edit);

        if ($stmt->execute()) {
            $success_message = "تم تعديل المنتج بنجاح.";
            header("Location: ESeller.php"); // إعادة توجيه بعد التعديل
            exit;
        } else {
            $error_message = "حدث خطأ أثناء تعديل المنتج.";
        }
    }

        // حذف المنتج
        if (isset($_GET['delete_product'])) {
            $product_id_to_delete = $_GET['delete_product'];
            $sql = "DELETE FROM product WHERE id = :product_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':product_id', $product_id_to_delete);
            if ($stmt->execute()) {
                $success_message = "تم حذف المنتج بنجاح.";
                header("Location: ESeller.php"); // إعادة تحميل الصفحة بعد الحذف
                exit;
            } else {
                $error_message = "حدث خطأ أثناء حذف المنتج.";
            }
        }

    } else {
        $error_message = "لا يمكنك إضافة أو تعديل المنتجات لأن حالة مشروعك ليست 'Accepted'."; 
    }
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>



<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة منتج</title>
    <link rel="stylesheet" href="ESeller.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>
<body>
    
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>لوحة البائع</h2>
            <ul class="nav">
            <li><a href="ESeller.php" ><i class="fas fa-box"></i> منتجاتي</a></li>
                <li><a href="project_profile.php"><i class="fas fa-user"></i> الملف الشخصي</a></li>
                <li><a href="Addproduct.php"class="active"><i class="fas fa-plus-circle"></i> إضافة منتج </a></li>
                
            </ul>
        </aside>

        <!-- محتوى الصفحة -->
        <main class="main-content">
        <header class="header">
            <h1>إضافة و تعديل المنتجات  </h1>
            <button class="logout" onclick="window.location.href='ELogIn.php'">
                <i class="fa fa-sign-out-alt"></i> تسجيل الخروج
            </button>
        </header>

<?php if (isset($success_message)): ?>
    <div class="message success"><?= $success_message ?></div>
<?php elseif (isset($error_message)): ?>
    <div class="message error"><?= $error_message ?></div>
<?php endif; ?>

<!-- إضافة منتج -->
<form method="POST" enctype="multipart/form-data">
    <label>اسم المنتج :<span style ="color: red">*</span></label>
    <input type="text" name="product_name" value="<?= isset($product_to_edit) ? $product_to_edit['product_name'] : '' ?>" required>

    <label> وصف المنتج :<span style ="color: red">*</span></label>
    <textarea name="description"  required><?= isset($product_to_edit) ? $product_to_edit['description'] : '' ?></textarea>

    <label> سعر المنتج : <span style ="color: red">*</span></label>
    <input type="text" name="price" value="<?= isset($product_to_edit) ? $product_to_edit['price'] : '' ?>"  step="0.01" required>

    <label>طرق التواصل : " الرجاء وضع ; في حال وجود اكتر من طريقه "<span style ="color: red">*</span></label>
            <input type="text" name="contact_number"
                value="<?= isset($product_to_edit) ? $product_to_edit['contact_number'] : '' ?>" required>

    <?php if (isset($product_to_edit)): ?>
        <input type="hidden" name="current_image_url" value="<?= $product_to_edit['image_url'] ?>">
        <label>إضافة الصورة : </label>
        <input type="file" name="image_url">
        <img src="<?= $product_to_edit['image_url'] ?>" alt="Product Image" width="100">
        <input type="submit" name="edit_product" value="تأكيد التعديل" class="button">
    <?php else: ?>
        <label> ادراج صورة للمنتج : <span style ="color: red">*</span></label>
        <input type="file" name="image_url" required>
        <input type="submit" name="add_product" value="إضافة المنتج" class="button">
    <?php endif; ?>
</form>


        </main>
    </div>
</body>
</html>
