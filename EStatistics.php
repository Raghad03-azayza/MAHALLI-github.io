<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ELogIn.php");
    exit;
}
require 'db_connection.php';

// ترجمات الحالة
$statusTranslations = [
    'Pending' => 'قيد المراجعة',
    'Accepted' => 'مقبول'
];

// ترجمات الفئة
$categoryTranslations = [
    'handcrafts' => 'الحرف اليدوية',
    'careProducts' => 'منتجات العناية الشخصية',
    'clothing' => 'ملابس',
    'accessories' => 'إكسسوارات',
    'souvenirGiveaways' => 'توزيعات'
];

// ترجمات نوع المستخدم
$userTypeTranslations = [
    'distributor' => 'موزع',
    'manufacturer' => 'مصنّع'
];

$activationData = [];
$res = $conn->query("SELECT activated, COUNT(*) as count FROM projects GROUP BY activated");
while ($row = $res->fetch_assoc()) {
    if ($row['activated'] == 1) {
        $activationData['مُفعّل'] = $row['count'];
    } else {
        $activationData['غير مُفعّل'] = $row['count'];
    }
}
// عدد المشاريع حسب الحالة
$statusQuery = "SELECT status, COUNT(*) as count FROM projects GROUP BY status";
$statusResult = $conn->query($statusQuery);
$statusData = [];
while ($row = $statusResult->fetch_assoc()) {
    $label = $statusTranslations[$row['status']] ?? $row['status'];
    $statusData[$label] = $row['count'];
}

// عدد المشاريع حسب الفئة
$categoryQuery = "SELECT category, COUNT(*) as count FROM projects GROUP BY category";
$categoryResult = $conn->query($categoryQuery);
$categoryData = [];
while ($row = $categoryResult->fetch_assoc()) {
    $label = $categoryTranslations[$row['category']] ?? $row['category'];
    $categoryData[$label] = $row['count'];
}

// عدد المشاريع حسب نوع المستخدم
$userTypeQuery = "SELECT user_type, COUNT(*) as count FROM projects GROUP BY user_type";
$userTypeResult = $conn->query($userTypeQuery);
$userTypeData = [];
while ($row = $userTypeResult->fetch_assoc()) {
    $label = $userTypeTranslations[$row['user_type']] ?? $row['user_type'];
    $userTypeData[$label] = $row['count'];
}

// عدد المنتجات لكل مشروع مع اسم المشروع
$productQuery = "
    SELECT prj.id, prj.business_name, COUNT(p.id) as count 
    FROM projects prj
    JOIN product p ON prj.id = p.project_id 
    GROUP BY prj.id, prj.business_name
";
$productResult = $conn->query($productQuery);
$productLabels = [];
$productCounts = [];
while ($row = $productResult->fetch_assoc()) {
    $productLabels[] = $row['business_name'];
    $productCounts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الاحصائيات</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- <link rel="stylesheet" href="EStatistic.css"> -->

    <link rel="stylesheet" href="a.css">

</head>
    
</head>

<body>


<div class="container">
    <aside class="sidebar">
        <h2>لوحة التحكم</h2>
        <ul>
            <li><a href="EIndex.php">الرئيسية</a></li>
            <li><a href="testAdminApi.php" >إدارة الطلبات</a></li>
            <li><a href="UserManagement.php">إدارة المشاريع</a></li>
            <li><a href="EStatistics.php"class="active">الاحصائيات </a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>إحصائيات</h1>
            <button class="logout" onclick="window.location.href='ELogIn.php'">
                <i class="fa fa-sign-out-alt"></i> تسجيل الخروج
            </button>
        </header>

            <div class="cardcontainer">
                <h1>إحصائيات المشاريع</h1>
                <div class="grid">

                    <div class="subGrid">
                        <div class="card">
                            <canvas id="statusChart"></canvas>
                        </div>
                        <div class="card">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>

                    <div class="subGrid">

                        <div class="card">
                            <canvas id="userTypeChart"></canvas>
                        </div>
                        <div class="card">
                            <canvas id="productsChart"></canvas>
                        </div>
                    </div>
                    <div class="subGrid">

                        <div class="card">
                            <canvas id="activationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        const statusChart = new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_keys($statusData)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($statusData)) ?>,
                    backgroundColor: ['#FFA07A', '#90EE90', '#87CEFA']
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'المشاريع حسب الحالة',
                        color: '#090101'
                    },
                    legend: {
                        labels: { color: '#090101' }
                    }
                }
            }
        });

        const categoryChart = new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($categoryData)) ?>,
                datasets: [{
                    label: 'عدد المشاريع',
                    data: <?= json_encode(array_values($categoryData)) ?>,
                    backgroundColor: '#4B0082'
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'المشاريع حسب الفئة',
                        color: '#090101'
                    },
                    legend: {
                        labels: { color: '#090101' }
                    }
                },
                scales: {
                    x: { ticks: { color: '#090101' } },
                    y: {
                        ticks: {
                            color: '#090101',
                            precision: 0
                        }
                    }
                }
            }
        });

        const userTypeChart = new Chart(document.getElementById('userTypeChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($userTypeData)) ?>,
                datasets: [{
                    label: 'عدد المشاريع حسب نوع المستخدم',
                    data: <?= json_encode(array_values($userTypeData)) ?>,
                    backgroundColor: '#20B2AA'
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'المشاريع حسب نوع المستخدم',
                        color: '#090101'
                    },
                    legend: {
                        labels: { color: '#090101' }
                    }
                },
                scales: {
                    x: { ticks: { color: '#090101' } },
                    y: {
                        ticks: {
                            color: '#090101',
                            precision: 0
                        }
                    }
                }
            }
        });

        const productsChart = new Chart(document.getElementById('productsChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($productLabels) ?>,
                datasets: [{
                    label: 'عدد المنتجات',
                    data: <?= json_encode($productCounts) ?>,
                    backgroundColor: '#FF69B4'
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'عدد المنتجات لكل مشروع',
                        color: '#090101'
                    },
                    legend: {
                        labels: { color: '#090101' }
                    }
                },
                scales: {
                    x: { ticks: { color: '#090101' } },
                    y: {
                        ticks: {
                            color: '#090101',
                            precision: 0
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('activationChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($activationData)) ?>,
                datasets: [{ data: <?= json_encode(array_values($activationData)) ?>, backgroundColor: ['#4bc0c0', '#ff9f40'] }]
            },
            options: { plugins: { legend: { labels: { color: '#090101' } } } }
        });
    </script>
</body>

</html>