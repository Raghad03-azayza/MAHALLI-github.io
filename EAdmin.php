<?php

session_start();

// التحقق من إذا كان البائع قد سجل الدخول
if (!isset($_SESSION['email'])) {
    header("Location: ELogIn.php");
    exit;
}

require 'db_connection.php';

function generateRandomPassword($length = 8) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $projectId = $_GET['id'];

    if ($_GET['action'] == "accept") {
        $randomPassword = generateRandomPassword();
        $query = "UPDATE projects SET status = 'Accepted', password = ?, activated = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $randomPassword, $projectId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "password" => $randomPassword]);
        } else {
            echo json_encode(["status" => "error", "message" => "فشل في قبول المشروع."]);
        }
        exit;
    } elseif ($_GET['action'] == "reject") {
        $query = "UPDATE projects SET status = 'Rejected', activated = NULL WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $projectId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "فشل في رفض المشروع."]);
        }
        exit;
    } elseif ($_GET['action'] == "deactivate") {
        $query = "UPDATE projects SET activated = 0 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $projectId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "فشل في إلغاء التفعيل."]);
        }
        exit;
    } elseif ($_GET['action'] == "activate") {
        $query = "UPDATE projects SET activated = 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $projectId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "فشل في تفعيل المشروع."]);
        }
        exit;
    }
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'Pending';

$query = "SELECT id, business_name, user_type, category, email, contact_number, care_documents, description, status, activated, password FROM projects WHERE status = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $filter);
$stmt->execute();
$result = $stmt->get_result();

// استعلام لحذف جميع السجلات التي حالتها Rejected
$q = "DELETE FROM projects WHERE status = 'Rejected'";

// تنفيذ الاستعلام
if ($conn->query($q) === TRUE) {
    // يمكنك إضافة تنبيه إذا أردت أن تظهر رسالة للمسؤول
    // echo "تم حذف المشاريع المرفوضة بنجاح.";
} else {
    // في حال حدوث خطأ أثناء تنفيذ الاستعلام
    // echo "حدث خطأ أثناء حذف المشاريع المرفوضة: " . $conn->error;
}


?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="stylesheet" href="a.css">
    <meta charset="UTF-8">
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

<script src="EAdmin.js"></script>
</body>
</html>
