<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سلة الشراء</title>
    <style>
             body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color:  #f0f2f5;

            margin: 0;
            padding: 0;
            color: #555;
        }

        .shoppingInfo {
            max-width: 1000px;
            margin: 50px auto;

            background-color: #f0f2f5;

            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border: 1px solidrgb(7, 0, 0);
        }

        h5 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: center;
        }

        th {
            background-color: #1e635a;
            color: #fff;
            font-size: 18px;
        }

        td {
            background-color:  #f0f2f5;
            font-size: 16px;
        }

        td:hover {
            background-color:rgb(214, 233, 242);
        }

        input[type="text"] {
            width: 60px;
            padding: 5px;
            text-align: center;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        a {
            color: #d9534f;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        a:hover {
            color: #c9302c;
            text-decoration: underline;
        }

        #emptyCartMessage {
            text-align: center;
            font-size: 18px;
            color: #777;
            margin-top: 30px;
        }

        #pricee {
            font-weight: bold;
            color: #008080;
            font-size: 20px;
        }

        

        .cart-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-item .product-name {
            text-align: left;
            font-size: 16px;
            font-weight: bold;
        }

        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: none;
            font-size: 16px;
            z-index: 1000;
        }
    </style>
</head>
<body>

<div class="shoppingInfo">
    <h5>سلة مشترياتك تحتوي على العناصر التالية:</h5>

    <div id="emptyCartMessage" style="display: none;">
        <h3>سلة المشتريات فارغة</h3>
    </div>

    <form method="post">
        <table border="1" id="cartTable" style="display: none;"></table>
    </form>
</div>
<div id="notification" class="notification">
    تم إضافة المنتج إلى المفضلة!
</div>
<script>
    window.addEventListener("load", displayCart);

    function displayCart() {
        let cart = JSON.parse(localStorage.getItem("Cart")) || [];
        let cartTable = document.getElementById("cartTable");
        let emptyCartMessage = document.getElementById("emptyCartMessage");

        cartTable.innerHTML = `
            <tr>
                <th>صوره المنتج</th>
                <th>اسم المنتج</th>
                <th>سعر الوحدة</th>
                <th>إجراء</th>
            </tr>
        `;

        if (cart.length === 0) {
            emptyCartMessage.style.display = 'block';
            cartTable.style.display = 'none';
        } else {
            emptyCartMessage.style.display = 'none';
            cartTable.style.display = 'table';

            let totalPrice = 0;

            cart.forEach(item => {
                let row = document.createElement('tr');
                row.innerHTML = `
<td><img src="${item.image}" class="cart-image" alt="${item.ProductName}" ></td>
                    <td>${item.ProductName}</td>
                    <td>${item.UnitPrice} د.أ</td>
                    <td><a href="javascript:void(0)" onclick="removeFromCart(${item.ProductId})">إزالة</a></td>
                `;
                cartTable.appendChild(row);
                totalPrice +=item.UnitPrice;
            });

            let totalRow = document.createElement('tr');
            totalRow.innerHTML = `
                <td colspan="4" style="text-align:right;"><strong>المجموع الكلي</strong></td>
                <td id="pricee">${totalPrice.toFixed(2)} د.أ</td>
            `;
            cartTable.appendChild(totalRow);
        }
    }

   

    function removeFromCart(id) {
        let cart = JSON.parse(localStorage.getItem("Cart")) || [];
        cart=cart.filter(a=> a.ProductId != id);
        localStorage.setItem("Cart", JSON.stringify(cart));
        displayCart();
    }

   
    // function addToFavorites(id) {
    //     let favorites = JSON.parse(localStorage.getItem("Favorites")) || [];
    //     let cart = JSON.parse(localStorage.getItem("Cart")) || [];
    //     let item = cart.find(product => product.ProductId === id);

    //     if (!favorites.some(fav => fav.ProductId === id)) {
    //         favorites.push(item);
    //         localStorage.setItem("Favorites", JSON.stringify(favorites));

    //         // تخزين إشعار الإضافة في localStorage
    //         localStorage.setItem("notification", "تم إضافة المنتج إلى المفضلة!");

    //         // إعادة تحميل الصفحة بعد إضافة المنتج
    //         location.reload();
    //     } else {
    //         alert("هذا المنتج موجود بالفعل في المفضلة");
    //     }
    // }
</script>

</body>
</html>
