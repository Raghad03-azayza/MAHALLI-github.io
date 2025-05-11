<?php
  require 'db_connection.php';


if (isset($_GET['id'])) {
    $projectId = $_GET['id'];
    $query = "SELECT care_documents FROM projects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($care_documents);
    $stmt->fetch();

    if ($care_documents) {
        // تحديد نوع MIME بشكل مناسب
        header("Content-Type: application/pdf"); // إذا كان مستند PDF مثلاً        
        
        
        // أو نوع الملف المناسب
        echo $care_documents;
    } else {
        echo "الوثيقة غير موجودة.";
    }
} else {
    echo "معرف المشروع غير موجود.";
}

$conn->close();
?>