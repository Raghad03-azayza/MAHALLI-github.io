<?php
header('Content-Type: application/json');

$host = "localhost";
$dbname = "add_project";
$username = "root";
$password = "12345678";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    echo json_encode(['error' => 'فشل الاتصال بقاعدة البيانات']);
    exit;
}

// جلب البيانات
$name = isset($_GET['name']) ? $_GET['name'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';

// إعداد الاستعلام
$query = "SELECT p.* FROM product p
          INNER JOIN projects pr ON p.project_id = pr.id
          WHERE 1=1";

$params = [];

if ($name !== '') {
    $query .= " AND p.product_name LIKE ?";
    $params[] = "%$name%";
}

if ($category !== '') {
    $query .= " AND pr.category = ?";
    $params[] = $category;
}

if ($minPrice !== '') {
    $query .= " AND p.price >= ?";
    $params[] = $minPrice;
}

if ($maxPrice !== '') {
    $query .= " AND p.price <= ?";
    $params[] = $maxPrice;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products);
