<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "add_project";

// إنشاء الاتصال
$mysqli = new mysqli($servername, $username, $password, $dbname);

// صفحة إدارة المشاريع - Admin Dashboard
// $mysqli = new mysqli("localhost", username: "DB_USER", "", "add_project");
if ($mysqli->connect_errno) {
    die("فشل الاتصال بقاعدة البيانات: " . $mysqli->connect_error);
}
// ترجمة أسماء التصنيفات إلى العربية
$catNames = ['handcrafts' => 'الحرف اليدوية', 'careProducts' => 'منتجات العناية الشخصية', 'clothing' => 'ملابس', 'accessories' => 'إكسسوارات', 'souvenirGiveaways' => 'توزيعات'];
// جلب المشاريع المقبولة
$resProj = $mysqli->query("SELECT * FROM projects WHERE status='Accepted' ORDER BY category, business_name");
while ($row = $resProj->fetch_assoc()) {
    $projectsByCat[$row['category']][] = $row;
}
// جلب المنتجات
$resProd = $mysqli->query("SELECT * FROM product");
while ($row = $resProd->fetch_assoc()) {
    $productsByProj[$row['project_id']][] = $row;
}

if (isset($_GET['delete_product']) && is_numeric($_GET['delete_product'])) {
    $del_id = intval($_GET['delete_product']);
    $del_stmt = $mysqli->prepare("DELETE FROM product WHERE id = ?");
    $del_stmt->bind_param('i', $del_id);
    $del_stmt->execute();
    header("Location: UserManagement.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>إدارة المشاريع</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="b.css">
</head>

<body>

    <div class="container">
        <aside class="sidebar">
            <h2>لوحة التحكم</h2>
            <ul>
                <li><a href="EIndex.php">الرئيسية</a></li>
                <li><a href="testAdminApi.php">إدارة الطلبات</a></li>
                <li><a href="UserManagement.php"class="active">إدارة المشاريع</a></li>
                <li><a href="EStatistics.php">الاحصائيات </a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>إدارة المشاريع</h1>
                <button class="logout" onclick="window.location.href='ELogIn.php'">
                    <i class="fa fa-sign-out-alt"></i> تسجيل الخروج
                </button>
            </header>
            <div class="container2">
                <div class="main-content2">
                    <div class="search-filter">
                        <input type="text" id="projectSearch" placeholder="اسم المشروع" />
                        <input type="text" id="productSearch" placeholder="اسم المنتج" />
                        <select id="categoryFilter">
                            <option value="">جميع التصنيفات</option>
                            <?php foreach ($catNames as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option><?php endforeach; ?>
                        </select>
                        <button id="applyFilter">تطبيق الفلترة</button>
                    </div>
                    <?php foreach ($catNames as $catKey => $catLabel): ?>
                        <h2 class="category-title"><?= $catLabel ?></h2>
                        <?php if (!empty($projectsByCat[$catKey])):
                            foreach ($projectsByCat[$catKey] as $proj): ?>
                                <div class="project-item" data-project="<?= strtolower($proj['business_name']) ?>"
                                    data-category="<?= $catKey ?>">
                                    <span class="arrow-icon">▶</span>
                                    <span class="project-name"><?= $proj['business_name'] ?></span>
                                    <div class="product-list" style="display:none; margin-right:20px;">
                                        <?php if (!empty($productsByProj[$proj['id']])):
                                            foreach ($productsByProj[$proj['id']] as $prod): ?>
                                                <div class="card product-card">
                                                    <img src="<?= $prod['image_url'] ?>" alt="<?= $prod['product_name'] ?>" />
                                                    <p><?= $prod['product_name'] ?></p>
                                                    <div class="actions">
                                                        <a href="?delete_product=<?= $prod['id'] ?>"
                                                            onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                                            <button>حذف</button>
                                                        </a> <a href="editProduct.php?id=<?= $prod['id'] ?>"><button>تعديل</button></a>
                                                    </div>
                                                </div>
                                            <?php endforeach; else: ?>
                                            <p>لا توجد منتجات</p><?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                            <p class="no-data">لا توجد مشاريع مقبولة في هذا القسم</p><?php endif; endforeach; ?>
                </div>
            </div>
            <script>
                document.getElementById('applyFilter').addEventListener('click', () => {
                    const proj = document.getElementById('projectSearch').value.toLowerCase();
                    const prod = document.getElementById('productSearch').value.toLowerCase();
                    const cat = document.getElementById('categoryFilter').value;
                    document.querySelectorAll('.project-item').forEach(item => {
                        const okProj = item.dataset.project.includes(proj);
                        const okCat = !cat || item.dataset.category === cat;
                        let okProd = !prod;
                        if (!okProd) item.querySelectorAll('.product-card p').forEach(p => { if (p.textContent.toLowerCase().includes(prod)) okProd = true; });
                        item.style.display = (okProj && okCat && okProd) ? 'flex' : 'none';
                    });
                });
                document.querySelectorAll('.project-item').forEach(item => {
                    const arrow = item.querySelector('.arrow-icon');
                    arrow.addEventListener('click', () => {
                        const list = item.querySelector('.product-list');
                        const isHidden = window.getComputedStyle(list).display === 'none';
                        list.style.display = isHidden ? 'flex' : 'none';
                        arrow.textContent = isHidden ? '▼' : '▶';
                    });
                });
                // function deleteProduct(id) { if (!confirm('حذف المنتج؟')) return; fetch('deleteProduct.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }) }).then(r => r.json()).then(d => d.success ? location.reload() : alert('فشل')) }
            </script>
</body>

</html>