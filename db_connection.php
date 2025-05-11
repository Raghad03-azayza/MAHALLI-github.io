<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "add_project";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
