<?php
// الاتصال بقاعدة البيانات
$host = "localhost";
$dbname = "add_project";
$username = "root";
$password = "12345678";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}

// استعلام كل المنتجات مع التصنيف المرتبط
$query = "SELECT p.*, pr.category FROM product p JOIN projects pr ON p.project_id = pr.id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// استعلام التصنيفات من جدول المشاريع
$query_categories = "SELECT DISTINCT category FROM projects";
$stmt_categories = $pdo->prepare($query_categories);
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>منتجات محلية</title>
  <style>
    body {
      font-family: 'Cairo', sans-serif;
      background-color: #fdfaf6;
      margin: 0;
      padding: 0;
    }

    .filters-container {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      align-items: center;
      margin: 20px auto;
      padding: 15px;
      background-color: #fff8ef;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      max-width: 95%;
    }

    .filters-container input,
    .filters-container select,
    .filters-container button {
      padding: 10px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    .filters-container button {
      background-color: #ff6f00;
      color: white;
      cursor: pointer;
      transition: 0.3s;
    }

    .filters-container button:hover {
      background-color: #e65c00;
    }

    .products-section {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      padding: 30px;
    }

    .product-card {
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 12px;
      width: 250px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
      transition: 0.3s;
    }

    .product-card:hover {
      transform: scale(1.03);
    }

    .product-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
    }

    .product-card h3 {
      font-size: 18px;
      margin: 10px 0 5px;
    }

    .product-card p {
      font-size: 14px;
      margin: 4px 0;
    }
  </style>
</head>
<body>

  <!-- ✅ الفلاتر -->
  <div class="filters-container">
    <input type="text" id="filter-name" placeholder="ابحث بالاسم...">
    
    <select id="filter-category">
      <option value="">كل التصنيفات</option>
      <?php
        // عرض التصنيفات من جدول المشاريع
        foreach ($categories as $cat) {
            echo "<option value='" . $cat['category'] . "'>" . $cat['category'] . "</option>";
        }
      ?>
    </select>
    
    <input type="number" id="filter-price-min" placeholder="أقل سعر">
    <input type="number" id="filter-price-max" placeholder="أعلى سعر">
    
    <button onclick="applyFilters()">تطبيق الفلاتر</button>
  </div>

  <!-- ✅ المنتجات -->
  <div class="products-section" id="products-container">
    <?php foreach ($products as $product): ?>
      <div class="product-card"
           data-name="<?= strtolower($product['product_name']) ?>"
           data-category="<?= $product['category'] ?>"  
           data-price="<?= $product['price'] ?>">
        <img src="<?= $product['image_url'] ?>" alt="<?= $product['product_name'] ?>">
        <h3><?= $product['product_name'] ?></h3>
        <p>السعر: <?= $product['price'] ?> د.</p>
        <p>التصنيف: <?= $product['category'] ?></p>  <!-- تم تعديل هذا الجزء -->
        <p>تواصل: 
          <?php if (str_starts_with($product['contact_number'], 'http')): ?>
            <a href="<?= $product['contact_number'] ?>" target="_blank">اضغط هنا</a>
          <?php else: ?>
            <a href="tel:<?= $product['contact_number'] ?>"><?= $product['contact_number'] ?></a>
          <?php endif; ?>
        </p>
      </div>
    <?php endforeach; ?>
  </div>

  <script>
    function applyFilters() {
      const name = document.getElementById('filter-name').value.toLowerCase();
      const category = document.getElementById('filter-category').value;
      const min = parseFloat(document.getElementById('filter-price-min').value) || 0;
      const max = parseFloat(document.getElementById('filter-price-max').value) || Infinity;

      const cards = document.querySelectorAll('.product-card');

      cards.forEach(card => {
        const productName = card.dataset.name;
        const productCategory = card.dataset.category;
        const productPrice = parseFloat(card.dataset.price);

        const matchName = productName.includes(name);
        const matchCategory = !category || productCategory === category;
        const matchPrice = productPrice >= min && productPrice <= max;

        if (matchName && matchCategory && matchPrice) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    }
  </script>

</body>
</html>
