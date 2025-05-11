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

$categoriesTranslation = [
  'handcrafts' => 'الحرف اليدوية',
  'careProducts' => 'منتجات العناية الشخصية',
  'clothing' => 'ملابس',
  'accessories' => 'إكسسوارات',
  'souvenirGiveaways' => 'توزيعات'
];


?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>العروض المميزة</title>
  <link rel="stylesheet" href="EStyle.css">
  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    

   
  </style>
</head>

<body>
  
  <!-- الهيدر -->
  <header>
    <div class="header-top">
      <div class="logo"><img src="uploads/logo.png" alt="Logo"></div>
      <div class="search-bar">
      <input type="text" id="filter-name" placeholder="ابحث بالاسم...">
      <button onclick="applyFilters()">بحث</button>
        <button style="border: 2px solid transparent; background-color: transparent; padding: 10px;" id="scroll-to-filters">
          <i class="fa-solid fa-filter" style="font-size: 23px;" ></i>
        </button>
      </div>
      <ul class="nav-menu">
        <li><a href="EIndex.php">الرئيسية</a></li>
        <li><a href="ERegisteration.php">انضم الينا كشريك </a></li>
        <li><a href="EAddToFav.php"><i class="fa-solid fa-heart" style="color: red;"></i></a></li>
        <li><a href="#" id="menu-toggle"><i class="fa-solid fa-bars"></i></a></li>
      </ul>
    </div>

     <!-- البانر -->
  <!-- <section class="banner">
    <h1>خليك مميز وادعم المنتج المحلي اللي على قدك</h1>
  </section> -->

    <div class="video-container">
    <video autoplay loop muted playsinline>
      <source src="uploads/WhatsApp Video 2025-04-12 at 11.12.30 PM.mp4" type="video/mp4">
      المتصفح لا يدعم تشغيل الفيديو.
    </video>
    <div class="overlay-text">خليك مميز وادعم المنتج المحلي </div>

    <div id="sidebar" class="sidebar">
      <div class="sidebar-content">
        <button id="close-sidebar" class="close-btn">&times;</button>
        <ul>
          <li><a href="AboutUs.php">من نحن</a></li>
          <li class="menu-item">
            <a href="#" id="categories-toggle">التصنيفات <i id="categories-arrow" class="fa-solid fa-chevron-down"></i></a>
            <ul class="dropdown">
            <li><a href="#" onclick="filterByCategory('handcrafts')">الحرف اليدوية</a></li>
            <li><a href="#" onclick="filterByCategory('careProducts')">منتجات العناية الشخصية</a></li>
            <li><a href="#" onclick="filterByCategory('clothing')">الملابس</a></li>
            <li><a href="#" onclick="filterByCategory('accessories')">الإكسسوارات</a></li>
            <li><a href="#" onclick="filterByCategory('souvenirGiveaways')">الهدايا</a></li>
            <li><a href="#" onclick="filterByCategory('all')">عرض الكل</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </header>

 

  <!-- السلايدر -->
  <h2 class="section-title">التصنيفات</h2>
<div id="filters-container"></div>
  <!-- قسم العروض المميزة -->
  <section class="deals-section">
    <div class="swiper-container">
      <div class="swiper-wrapper">
        <!-- بطاقة عرض 1 -->
        <div class="swiper-slide deal-card">
          <img src="https://bournecrisp.com.au/wp-content/uploads/2019/07/accessories-make-or-break-1100x733.jpg"
            alt="عرض 1">

          <!-- <p> اكسسوارات</p> -->
        </div>
        <!-- بطاقة عرض 2 -->
        <div class="swiper-slide deal-card">
          <img src="https://n.nordstrommedia.com/id/873a83cd-9cef-487a-88eb-20d884d7c5a6.jpeg?h=600&w=750" alt="عرض 2">

          <!-- <p>ملابس </p> -->
        </div>
        <!-- بطاقة عرض 3 -->
        <div class="swiper-slide deal-card">
          <img src="https://cdn.elhiwar.dz/wp-content/uploads/2022/03/19_2017-636423795628263531-826.jpg" alt="عرض 3">

          <!-- <p>حرف يدوية </p> -->
        </div>
        <!-- بطاقة عرض 4 -->
        <div class="swiper-slide deal-card">
          <img
            src="https://cdn.prod.website-files.com/6406fd43722f32d77e16570f/64d0553fa6135dd0af46b935_many-wedding-gift-boxes-with-scissor-wooden-background%20(1)%20(2).jpg"
            alt="عرض 4">

          <!-- <p>توزيعات</p> -->
        </div>
        <!-- بطاقة عرض 5 -->
        <div class="swiper-slide deal-card">
          <img
            src="https://media.istockphoto.com/id/584574708/photo/soap-bar-and-liquid-shampoo-shower-gel-towels-spa-kit.jpg?s=612x612&w=0&k=20&c=TFeQmTwVUwKY0NDKFFORe3cwDCxRtotFgEujMswn3dc="
            alt="عرض 5">

          <!-- <p>منتجات العناية االشخصية</p> -->
        </div>

      </div>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>

  </section>
<!-- الفلاتر -->
<h2 class="section-title">تصفية المنتجات</h2>
<div class="filters-container">
    <input type="hidden" id="filter-name" placeholder="ابحث بالاسم...">
    
    <select id="filter-category">
      <option value="">كل التصنيفات</option>
      <?php
    // عرض التصنيفات مع الترجمة
    foreach ($categories as $cat) {
        $categoryTranslated = isset($categoriesTranslation[$cat['category']]) ? $categoriesTranslation[$cat['category']] : $cat['category'];
        echo "<option value='" . $cat['category'] . "'>" . $categoryTranslated . "</option>";
    }
    ?>
    </select>
    <label > من</label>

    <input type="number" id="filter-price-min" placeholder="أقل سعر">
    <label > الى</label>
    <input type="number" id="filter-price-max" placeholder="أعلى سعر">
    
    <button onclick="applyFilters()">تطبيق التصفية</button>
  </div>

<div id="products-container"></div>
<h2 class="section-title">منتجاتنا</h2>

<div class="products-section" >
    <?php foreach ($products as $product): ?>
      <div class="product-item"
      data-name="<?= strtolower($product['product_name']) ?>"
           data-category="<?= $product['category'] ?>"  
           data-price="<?= $product['price'] ?>">
        <img   class="product-img" onclick="showDetails('<?= $product['product_name'] ?>', '<?= $product['description'] ?>', '<?= $product['price'] ?>', '<?= $product['contact_number'] ?>', '<?= $product['image_url'] ?>')" src="<?= $product['image_url'] ?>" alt="<?= $product['product_name'] ?>" >
        <div class="product-info">

        <h3><?= $product['product_name'] ?></h3>
        <p class="price"><?= $product['price'] ?> دينار</p>
        <!-- <p><?= $product['category'] ?></p>  تم تعديل هذا الجزء -->
        <button class="view-btn"
                onclick="showDetails('<?= $product['product_name'] ?>', '<?= $product['description'] ?>', '<?= $product['price'] ?>', '<?= $product['contact_number'] ?>', '<?= $product['image_url'] ?>')">👁️</button>
        <button class="heart-btn" onclick="EAddToFav('<?= $product['id'] ?>','<?= $product['product_name'] ?>', '<?= $product['price'] ?>', '<?= $product['image_url'] ?>')">
        <i id="fav-heart-<?= $product['id'] ?>" class="fa-regular fa-heart"></i>
      </div>
      </div>

    <?php endforeach; ?>
  </div>

  <!-- الفوتر -->
  <footer>
    <div class="footer-container">
      
      <p class="footer-note">© 2024 على قدك. جميع الحقوق محفوظة.</p>
    </div>
  </footer>
  <script>
    window.addEventListener("DOMContentLoaded", () => {
        const cart = JSON.parse(localStorage.getItem("Cart")) || [];

        cart.forEach(item => {
            const heartIcon = document.getElementById(`fav-heart-${item.ProductId}`);
            if (heartIcon) {
                heartIcon.classList.remove("fa-regular");
                heartIcon.classList.add("fa-solid","heart-red");
            }
        });
    });
</script>


  <script src="EIndex.js"></script>

</body>
