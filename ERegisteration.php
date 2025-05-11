<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب تقديم مشروع</title>
    <link rel="stylesheet" href="ERegisteration.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>

<?php
require 'db_connection.php';

// تعريف الرسائل الافتراضية
$successMessage = "";
$errorMessage = "";

// معالجة الطلب عند الإرسال
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استقبال البيانات من النموذج
    $email = $_POST['email'];
    $category = $_POST['category'];
    $businessName = $_POST['businessName'];
    $contactNumber = $_POST['contactNumber'];
    $description = $_POST['description'];
    $userType = isset($_POST['type']) ? $_POST['type'] : null;

    // التحقق من صحة البريد الإلكتروني
    if (substr($email, -10) !== '@gmail.com') {
        $errorMessage = "❌ يرجى استخدام بريد إلكتروني ينتهي بـ @gmail.com.";
    } else {
        // إذا كان البريد الإلكتروني صحيحًا، تابع تنفيذ الكود
        // التحقق من وجود البريد الإلكتروني مسبقًا
        $emailCheckQuery = "SELECT * FROM projects WHERE email = ?";
        $emailCheckStmt = $conn->prepare($emailCheckQuery);
        $emailCheckStmt->bind_param("s", $email);
        $emailCheckStmt->execute();
        $emailCheckResult = $emailCheckStmt->get_result();

        if ($emailCheckResult->num_rows > 0) {
            $errorMessage = "❌ البريد الإلكتروني الذي أدخلته موجود بالفعل. يرجى استخدام بريد إلكتروني آخر.";
        } else {
            // معالجة ملف careDocuments فقط إذا كانت الفئة "منتجات العناية الشخصية" والنوع "مصنع"
            $careDocumentContent = null;
            if ($category == 'careProducts' && $userType == 'manufacturer') {
                if (!empty($_FILES['careDocuments']['tmp_name'])) {
                    $careDocumentContent = file_get_contents($_FILES['careDocuments']['tmp_name']); // قراءة محتوى الملف
                } else {
                    $careDocumentContent = ''; // تعيين قيمة فارغة إذا لم يتم رفع الملف
                }
            } else {
                $careDocumentContent = ''; // تعيين قيمة فارغة إذا كانت الفئة غير "منتجات العناية الشخصية" أو النوع ليس "مصنع"
            }

            // إدخال البيانات في قاعدة البيانات
            $stmt = $conn->prepare("INSERT INTO projects (category, user_type, business_name, contact_number, email, description, care_documents) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $category, $userType, $businessName, $contactNumber, $email, $description, $careDocumentContent);

            if ($stmt->execute()) {
                $successMessage = "✅ تم إضافة المشروع ورفع الملف بنجاح!";
            } else {
                $errorMessage = "❌ حدث خطأ أثناء إضافة المشروع: " . $stmt->error;
            }

            $stmt->close();
        }

        $emailCheckStmt->close();
    }
}

$conn->close();
?>

<body>

<header class="header">
  <div class="logo">
    <img src="uploads/logo.png" alt="Logo">
  </div>
  <button class="login-button" onclick="handleLogin()">تسجيل الدخول
  <i class="fas fa-sign-in-alt" onclick="goToLoginPage()"></i>
  </button>
</header>


    <div class="container">
        <!-- <div class="header-message">
            <h1>إذا كان مشروعك على قدك</h1>
            <p>ما عليك إلا تحكي معنا لنساعدك في تحقيق أهدافك</p>
        </div> -->

        <div class="form-container">
            <h1>طلب تقديم مشروع</h1>

            <?php if (!empty($successMessage)): ?>
                <p style="color: green;"><?= $successMessage ?></p>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <p style="color: red;"><?= $errorMessage ?></p>
            <?php endif; ?>

            <form id="projectForm" method="POST" enctype="multipart/form-data">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" placeholder="example@gmail.com" required
                    pattern="^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|outlook\.com)$"
                    title="يرجى إدخال بريد إلكتروني صحيح ينتهي بـ @gmail.com أو @yahoo.com أو @outlook.com">

                <label>الفئة</label>
                <select id="category" name="category" required>
                    <option value="" disabled selected>اختر</option>
                    <option value="careProducts">منتجات العناية الشخصية</option>
                    <option value="handcrafts">الحرف اليدوية</option>
                    <option value="clothing">الملابس</option>
                    <option value="accessories">الإكسسوارات</option>
                    <option value="souvenirGiveaways">التوزيعات </option>
                </select>

                <label>هل أنت موزع أم مصنع؟</label>
                <select id="type" name="type" required>
                    <option value="" disabled selected>اختر</option>
                    <option value="distributor">موزع</option>
                    <option value="manufacturer">مصنع</option>
                </select>

                <label>اسم المشروع</label>
                <input type="text" id="businessName" name="businessName" required>

                <div class="input-container">
                    <label for="contactNumber">رقم الاتصال</label>
                    <input type="tel" id="contactNumber" name="contactNumber" placeholder="أدخل رقمك" required>
                    <!-- <button type="button" id="verifyContactButton" onclick="showVerificationField()">تحقق</button> -->
                </div>


                <!-- <div id="verificationContainer" style="display: none;">
                    <label for="verificationField">أدخل رمز التحقق</label>
                    <input type="text" id="verificationField" name="verification" placeholder="ادخل الرمز" required>
                </div> -->

                <label>وصف المشروع</label>
                <textarea id="description" name="description" required></textarea>

                <label>رفع وثيقة ( 'في حال تصنيع منتجات العناية الشخصية' )</label>
                <input type="file" id="careDocuments" name="careDocuments">

                <button type="submit">تقديم</button>
            </form>
        </div>
    </div>

    <script src="ERegisteration.js"></script>
</body>

</html>