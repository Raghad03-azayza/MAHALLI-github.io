<?php

session_start();

// إنشاء ملف لوق عام للاختبار
$debugLog = __DIR__ . "/logs/debug_log.txt";
if (!file_exists(__DIR__ . "/logs")) {
    mkdir(__DIR__ . "/logs", 0777, true);
}

file_put_contents($debugLog, "=== بدأت الصفحة ===\n", FILE_APPEND);

// التحقق من إذا كان البائع قد سجل الدخول
if (!isset($_SESSION['email'])) {
    file_put_contents($debugLog, "الجلسة غير موجودة\n", FILE_APPEND);
    header("Location: ELogIn.php");
    exit;
}

require 'db_connection.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// توليد كلمة مرور عشوائية
function generateRandomPassword($length = 8){
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomPassword;
}

// دالة مساعدة لإرسال البريد الإلكتروني
function sendEmail($to, $subject, $body, $debugLog) {
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yousefamjdgpt@gmail.com';
        $mail->Password = 'nytxvvjhrwpjtist'; // استخدم App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('yousefamjdgpt@gmail.com', 'محلي');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
        file_put_contents($debugLog, "تم إرسال الإيميل إلى $to\n", FILE_APPEND);
        return true;
    } catch (Exception $e) {
        file_put_contents($debugLog, "فشل في إرسال الإيميل: " . $e->getMessage() . "\n", FILE_APPEND);
        return false;
    }
}

// معالجة العمليات
if (isset($_GET['action']) && isset($_GET['id'])) {
    $projectId = $_GET['id'];
    $action = $_GET['action'];
    file_put_contents($debugLog, "الإجراء: $action على ID: $projectId\n", FILE_APPEND);

    if ($action == "accept") {
        $randomPassword = generateRandomPassword();
        $query = "UPDATE projects SET status = 'Accepted', password = ?, activated = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $randomPassword, $projectId);

        if ($stmt->execute()) {
            $emailQuery = "SELECT email FROM projects WHERE id = ?";
            $emailStmt = $conn->prepare($emailQuery);
            $emailStmt->bind_param("i", $projectId);
            $emailStmt->execute();
            $result = $emailStmt->get_result();
            $user = $result->fetch_assoc();
            $userEmail = $user['email'];

            $subject = 'تم قبولك في النظام';
            $body = "
                <h3>تم قبول مشروعك!</h3>
                <p>يمكنك الدخول إلى البوابة الخاصة بك باستخدام البيانات التالية:</p>
                <p><strong>البريد الإلكتروني:</strong> $userEmail</p>
                <p><strong>كلمة المرور:</strong> $randomPassword</p>
            ";

            sendEmail($userEmail, $subject, $body, $debugLog);
            echo json_encode(["status" => "success", "password" => $randomPassword]);
        } else {
            file_put_contents($debugLog, "فشل في تحديث قاعدة البيانات: " . $stmt->error . "\n", FILE_APPEND);
            echo json_encode(["status" => "error", "message" => "فشل في قبول المشروع."]);
        }
        exit;
    }

    elseif ($action == "reject") {
        $query = "UPDATE projects SET status = 'Rejected', activated = NULL WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $projectId);

        if ($stmt->execute()) {
            $emailQuery = "SELECT email FROM projects WHERE id = ?";
            $emailStmt = $conn->prepare($emailQuery);
            $emailStmt->bind_param("i", $projectId);
            $emailStmt->execute();
            $result = $emailStmt->get_result();
            $user = $result->fetch_assoc();
            $userEmail = $user['email'];

            $subject = 'رفض مشروعك';
            $body = "<h3>نأسف!</h3><p>لقد تم رفض مشروعك من قبل الإدارة.</p>";

            sendEmail($userEmail, $subject, $body, $debugLog);
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "فشل في رفض المشروع."]);
        }
        exit;
    }

    elseif ($action == "deactivate") {
        $query = "UPDATE projects SET activated = 0 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $projectId);

        if ($stmt->execute()) {
            $emailQuery = "SELECT email FROM projects WHERE id = ?";
            $emailStmt = $conn->prepare($emailQuery);
            $emailStmt->bind_param("i", $projectId);
            $emailStmt->execute();
            $result = $emailStmt->get_result();
            $user = $result->fetch_assoc();
            $userEmail = $user['email'];

            $subject = 'تم إلغاء تفعيل مشروعك';
            $body = "<h3>تنبيه!</h3><p>تم إلغاء تفعيل مشروعك بشكل مؤقت من النظام.</p>";

            sendEmail($userEmail, $subject, $body, $debugLog);
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "فشل في إلغاء التفعيل."]);
        }
        exit;
    }

    elseif ($action == "activate") {
        $query = "UPDATE projects SET activated = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $projectId);

        if ($stmt->execute()) {
            $emailQuery = "SELECT email FROM projects WHERE id = ?";
            $emailStmt = $conn->prepare($emailQuery);
            $emailStmt->bind_param("i", $projectId);
            $emailStmt->execute();
            $result = $emailStmt->get_result();
            $user = $result->fetch_assoc();
            $userEmail = $user['email'];

            $subject = 'تم تفعيل مشروعك';
            $body = "<h3>مبروك!</h3><p>تم تفعيل مشروعك مرة أخرى في النظام.</p>";

            sendEmail($userEmail, $subject, $body, $debugLog);
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "فشل في تفعيل المشروع."]);
        }
        exit;
    }
}

// فلترة الطلبات حسب الحالة
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Pending';

$query = "SELECT id, business_name, user_type, category, email, contact_number, care_documents, description, status, activated, password FROM projects WHERE status = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $filter);
$stmt->execute();
$result = $stmt->get_result();

// حذف الطلبات المرفوضة
$q = "DELETE FROM projects WHERE status = 'Rejected'";
$conn->query($q);

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="a.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>إدارة الطلبات</title>
</head>
<body>

<div class="container">
    <aside class="sidebar">
        <h2>لوحة التحكم</h2>
        <ul>
                <li><a href="EIndex.php">الرئيسية</a></li>
                <li><a href="EAdmin.php" class="active">إدارة الطلبات</a></li>
                <li><a href="UserManagement.php">إدارة المشاريع</a></li>
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

        <div class="search-filter">
            <button onclick="filterOrders('Pending')">قيد الانتظار</button>
            <button onclick="filterOrders('Accepted')">مقبول</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>رقم المشروع</th>
                    <th>اسم المشروع</th>
                    <th>نوع المستخدم</th>
                    <th>الفئة</th>
                    <th>البريد الإلكتروني</th>
                    <th>رقم التواصل</th>
                    <th>الوثائق</th>
                    <th>الوصف</th>
                    <th>الحالة</th>
                    <th>مفعّل</th>
                    <th>كلمة المرور</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr id="row-<?= $row['id'] ?>">
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['business_name']) ?></td>
                            <td><?= htmlspecialchars($row['user_type']) ?></td>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['contact_number']) ?></td>
                            <td>
                                    <?php if (!empty($row['care_documents'])): ?>
                                        <a href="view_document.php?id=<?= $row['id'] ?>" target="_blank">عرض</a>
                                    <?php else: ?>
                                        لا يوجد
                                    <?php endif; ?>
                                </td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td id="status-<?= $row['id'] ?>"><?= htmlspecialchars($row['status']) ?></td>
                            <td id="activated-<?= $row['id'] ?>"><?= $row['activated'] == 1 ? 'نعم' : 'لا' ?></td>
                            <td id="password-<?= $row['id'] ?>"><?= htmlspecialchars($row['password'] ?? '—') ?></td>
                            <td>
                                <?php if ($filter == 'Pending'): ?>
                                    <button id="g" onclick="acceptProject(<?= $row['id'] ?>)">قبول</button>
                                    <button id="r" onclick="rejectProject(<?= $row['id'] ?>)">رفض</button>
                                <?php elseif ($filter == 'Accepted'): ?>
                                    <button id="r" onclick="deactivateProject(<?= $row['id'] ?>)">إلغاء التفعيل</button>
                                    <button id="g" onclick="activateProject(<?= $row['id'] ?>)">تفعيل</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12">لا توجد طلبات متاحة.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>

<script>
function acceptProject(projectId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `testAdminApi.php?action=accept&id=${projectId}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                alert("تم قبول الطلب! تم إرسال كلمة المرور إلى البريد الإلكتروني.");
                window.location.reload();
            } else {
                alert("حدث خطأ: " + response.message);
            }
        }
    };
    xhr.send();
}

function rejectProject(projectId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `testAdminApi.php?action=reject&id=${projectId}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                alert("تم رفض الطلب!");
                window.location.reload();
            } else {
                alert("حدث خطأ: " + response.message);
            }
        }
    };
    xhr.send();
}

function deactivateProject(projectId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `testAdminApi.php?action=deactivate&id=${projectId}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                alert("تم إلغاء تفعيل المشروع!");
                window.location.reload();
            } else {
                alert("حدث خطأ: " + response.message);
            }
        }
    };
    xhr.send();
}

function activateProject(projectId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `testAdminApi.php?action=activate&id=${projectId}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                alert("تم تفعيل المشروع!");
                window.location.reload();
            } else {
                alert("حدث خطأ: " + response.message);
            }
        }
    };
    xhr.send();
}

function filterOrders(status) {
    window.location.href = `testAdminApi.php?filter=${status}`;
}
</script>
</body>
</html>