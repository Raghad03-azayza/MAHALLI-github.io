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
        if (isset($_GET['edit'])) {
            $product_id_to_edit = $_GET['edit'];
            $sql = "SELECT * FROM product WHERE id = :product_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':product_id', $product_id_to_edit);
            $stmt->execute();
            $product_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
            $product_name = trim($_POST['product_name']);
            $description = trim($_POST['description']);
            $price = trim($_POST['price']);
            $contact_number = trim($_POST['contact_number']);
            $image_path = $_POST['current_image_url']; // الاحتفاظ بالصورة الحالية إذا لم يتم رفع صورة جديدة

            // رفع الصورة جديدة إذا تم رفعها
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ESeller.css">
    <title>صفحة البائع</title>
    
</head>
<body>
                
<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ELogIn.php");
    exit;
}

// ... باقي PHP نفسه دون تعديل ...
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>صفحة البائع</title>
    <link rel="stylesheet" href="ESeller.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>لوحة البائع</h2>
            <ul>
                <li><a href="ESeller.php" class="active"><i class="fas fa-box"></i> منتجاتي</a></li>
                <li><a href="project_profile.php"><i class="fas fa-user"></i> الملف الشخصي</a></li>
                <li><a href="Addproduct.php"><i class="fas fa-plus-circle"></i> إضافة منتج </a></li>
                
               
            </ul>
        </aside>

        <!-- محتوى الصفحة -->
        <main class="main-content">
        <header class="header">
            <h1>منتجاتي </h1>
            <button class="logout" onclick="window.location.href='ELogIn.php'">
                <i class="fa fa-sign-out-alt"></i> تسجيل الخروج
            </button>
        </header>


            <?php if (isset($success_message)): ?>
                <div class="message success"><?= $success_message ?></div>
            <?php elseif (isset($error_message)): ?>
                <div class="message error"><?= $error_message ?></div>
            <?php endif; ?>

           
            <!-- عرض المنتجات -->
            <section>
                
                <div class="product-container">
                    <?php foreach ($products as $product): ?>
                        <div class="product-item">
                            <img src="<?= $product['image_url'] ?>" alt="Product Image">
                            <p><?= $product['product_name'] ?></p>
                            <p>السعر: <?= $product['price'] ?></p>
                            <p><?= $product['description'] ?></p>
                            <p>
                        <strong>طرق التواصل:</strong>
                    <div class="contact-icons">
                        <?php
                        $contactRaw = $product['contact_number'];
                        $items = explode(';', $contactRaw);
                        foreach ($items as $item) {
                            $item = trim($item);

                            // تحديد النوع تلقائياً
                            if (preg_match('/^(\+?\d{7,15})$/', $item)) {
                                // رقم هاتف - أزرق
                                echo "<a href='tel:$item' title='اتصال'><i class='fa fa-phone' style='color: #007BFF;'></i></a>";
                            } elseif (strpos($item, 'wa.me') !== false) {
                                // واتساب - أخضر
                                echo "<a href='$item' target='_blank' title='واتساب'><i class='fab fa-whatsapp' style='color: #25D366;'></i></a>";
                            } elseif (strpos($item, 'facebook.com') !== false) {
                                // فيسبوك - أزرق
                                echo "<a href='$item' target='_blank' title='فيسبوك'><i class='fab fa-facebook' style='color: #1877F2;'></i></a>";
                            } elseif (strpos($item, 'instagram.com') !== false) {
                                // انستغرام - وردي بنفسجي
                                echo "<a href='$item' target='_blank' title='انستغرام'><i class='fab fa-instagram' style='color: #C13584;'></i></a>";
                            } else {
                                // رابط عام - رمادي
                                echo "<a href='$item' target='_blank' title='رابط'><i class='fa fa-link' style='color: #6c757d;'></i></a>";
                            }
                            
                        }
                        ?>
                    </div>
                    </p>

                            <div class="action-buttons">
                            <a href="addproduct.php?id=<?= $product['id'] ?>">تعديل</a>

                                <a href="?delete_product=<?= $product['id'] ?>" onclick="return confirm('هل أنت متأكد من حذف المنتج؟')">حذف</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>



