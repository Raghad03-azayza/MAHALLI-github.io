
const swiper = new Swiper('.swiper-container', {
    slidesPerView: 3,
    spaceBetween: 30,
    autoplay: {
      delay: 2500, // الانتقال التلقائي كل 5 ثوانٍ
      disableOnInteraction: false, // الاستمرار بعد التفاعل
    },
    navigation: {
      nextEl: '.swiper-button-next', // زر التنقل للأمام
      prevEl: '.swiper-button-prev', // زر التنقل للخلف
    },
    pagination: {
      el: '.swiper-pagination', // النقاط
      clickable: true,
    },
  });
  
  
    document.addEventListener("DOMContentLoaded", () => {
      // إضافة تفاعل عند تمرير الماوس على التصنيفات
      const categoryItems = document.querySelectorAll('.category-item');
    
      categoryItems.forEach(item => {
        const btn = item.querySelector('.btnh');
    
        // عند مرور الماوس على التصنيف، إظهار الزر
        item.addEventListener('mouseover', () => {
          btn.style.opacity = 1; // إظهار الزر
        });
    
        // عند مغادرة الماوس من التصنيف، إخفاء الزر
        item.addEventListener('mouseout', () => {
          btn.style.opacity = 0; // إخفاء الزر
        });
      });
    });
    
    document.addEventListener("DOMContentLoaded", () => {
      // الحصول على عناصر التحكم
      const menuToggle = document.querySelector('.fa-bars'); // الأيقونة
      const sidebar = document.getElementById('sidebar');
      const closeSidebar = document.getElementById('close-sidebar');
    
      // تحقق أن كل العناصر موجودة
      if (menuToggle && sidebar && closeSidebar) {
        // عند النقر على أيقونة القائمة
        menuToggle.addEventListener('click', () => {
          sidebar.classList.add('show');
        });
    
        // عند النقر على زر الإغلاق
        closeSidebar.addEventListener('click', () => {
          sidebar.classList.remove('show');
        });
      } else {
        console.error("أحد العناصر مفقود! تحقق من HTML.");
      }
    });
    document.getElementById("categories-toggle").addEventListener("click", function(e) {
      e.preventDefault(); // منع الانتقال إلى الرابط
      var menuItem = this.parentElement; // الحصول على العنصر الأب
      menuItem.classList.toggle("active"); // إضافة أو إزالة الفئة "active"
    });
    
    function showDetails(name, description, price, contact, imageUrl) {
      // إذا في نافذة منبثقة حالياً، نحذفها
      const existing = document.querySelector('.product-details');
      if (existing) existing.remove();
  
      // إنشاء عناصر النافذة
      const details = document.createElement('div');
      details.classList.add('product-details');
  
      const container = document.createElement('div');
      container.classList.add('details-container');
  
      const info = document.createElement('div');
      info.classList.add('product-info-detail');
  
      info.innerHTML = `<h2>${name}</h2>`;
      info.innerHTML += `<p>${description}</p>`;
      info.innerHTML += `<p>السعر: ${price} دينار</p>`;
  
      // ✅ طرق التواصل: أيقونات جنب بعض
      const contacts = contact.split(';');
      let iconsHTML = `<div class="contact-icons">`;
  
      contacts.forEach(item => {
        item = item.trim();
    
        if (/^(\+?\d{7,15})$/.test(item)) {
            iconsHTML += `<a href="tel:${item}" title="اتصال"><i class="fa fa-phone" style="color: #007BFF;"></i></a>`;
        } else if (item.includes("wa.me")) {
            iconsHTML += `<a href="${item}" target="_blank" title="واتساب"><i class="fab fa-whatsapp" style="color: #25D366;"></i></a>`;
        } else if (item.includes("facebook.com")) {
            iconsHTML += `<a href="${item}" target="_blank" title="فيسبوك"><i class="fab fa-facebook" style="color: #1877F2;"></i></a>`;
        } else if (item.includes("instagram.com")) {
            iconsHTML += `<a href="${item}" target="_blank" title="انستغرام"><i class="fab fa-instagram" style="color: #C13584;"></i></a>`;
        } else {
            iconsHTML += `<a href="${item}" target="_blank" title="رابط"><i class="fa fa-link" style="color: #6c757d;"></i></a>`;
        }
    });
    
  
      iconsHTML += `</div>`;
      info.innerHTML += iconsHTML;
  
      const img = document.createElement('img');
      img.src = imageUrl;
  
      const close = document.createElement('button');
      close.classList.add('closee-btn');
      close.innerHTML = "X";
      close.onclick = () => details.remove();
  
      container.appendChild(info);
      container.appendChild(img);
      container.appendChild(close);
      details.appendChild(container);
      document.body.appendChild(details);
  }
  
  

 
function applyFilters() {
  const name = document.getElementById('filter-name').value.toLowerCase();
  const category = document.getElementById('filter-category').value;
  const min = parseFloat(document.getElementById('filter-price-min').value) || 0;
  const max = parseFloat(document.getElementById('filter-price-max').value) || Infinity;

  const cards = document.querySelectorAll('.product-item');

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
  const productsContainer = document.getElementById('products-container');
  productsContainer.scrollIntoView({ behavior: 'smooth' });
}
  document.getElementById('scroll-to-filters').addEventListener('click', function () {
    const filterSection = document.getElementById('filters-container');
    if (filterSection) {
      filterSection.scrollIntoView({ behavior: 'smooth' });
    }
    
  });
  function filterByCategory(category) {
    const allProducts = document.querySelectorAll('.product-item');
    allProducts.forEach(product => {
      if (category === 'all' || product.dataset.category === category) {
        product.style.display = 'block';
      } else {
        product.style.display = 'none';
      }
    });
  
    // تمرير المستخدم لمكان عرض المنتجات
    const section = document.querySelector('.products-section');
    if (section) {
      section.scrollIntoView({ behavior: 'smooth' });
    }
  }
  //////////////////////////////////////////////////////////// 



//   function displayCart() {
//       let cart = JSON.parse(localStorage.getItem("Cart")) || [];
//       let cartTable = document.getElementById("cartTable");
//       let emptyCartMessage = document.getElementById("emptyCartMessage");

//       cartTable.innerHTML = `
//           <tr>
//               <th>اسم المنتج</th>
//               <th>الكمية</th>
//               <th>سعر الوحدة</th>
//               <th>السعر الإجمالي</th>
//               <th>إجراء</th>
//           </tr>
//       `;

//       if (cart.length === 0) {
//           emptyCartMessage.style.display = 'block';
//           cartTable.style.display = 'none';
//       } else {
//           emptyCartMessage.style.display = 'none';
//           cartTable.style.display = 'table';

//           let totalPrice = 0;

//           cart.forEach(item => {
//               let row = document.createElement('tr');
//               row.innerHTML = `
//                   <td>${item.ProductName}</td>
//                   <td>${item.UnitPrice} د.أ</td>
//                   <td><a href="javascript:void(0)" onclick="removeFromCart(${item.ProductId})">إزالة</a></td>
//               `;
//               cartTable.appendChild(row);
//               totalPrice += item.UnitPrice;
//           });

//           let totalRow = document.createElement('tr');
//           totalRow.innerHTML = `
//               <td colspan="4" style="text-align:right;"><strong>المجموع الكلي</strong></td>
//               <td id="pricee">${totalPrice.toFixed(2)} د.أ</td>
//           `;
//           cartTable.appendChild(totalRow);
//       }
//   }


  
// function EAddToFav(id, name, unitPrice)
// {

//       //let newAmount = avalibleAmountInTheStock;


//       let cart = JSON.parse(localStorage.getItem("Cart")) || [];

//       var existingItem = null;

//       for (let i = 0; i < cart.length; i++) {
//           if (cart[i].ProductId == id) {
//               existingItem = cart[i];

//               break;
//           }

//       }
     


//       if (existingItem != null)
//       {
          
//               // existingItem.quantity++;

          
//       }

//       else
//       {
//           cart.push({
//               ProductId: id,
//               ProductName: name,
//               UnitPrice: unitPrice,
//               // quantity: 1,
//           });
//       }

//       //newAmount--;
//       //document.getElementById(`avalibleAmount-${id}`).innerText = newAmount;

//       localStorage.setItem("Cart", JSON.stringify(cart));
 
//       // UpdateCartSummary();

//       displayCart();
  
 
      
// }

// // function updateQuantity(id,  newQuantity)
// // {
// //     let cart = JSON.parse(localStorage.getItem("Cart")) || [];
// //     for (let i = 0; i < cart.length; i++)
// //     {
// //         if (cart[i].ProductId == id)
// //         {          
// //             let updateQuantity = parseInt(newQuantity);
// //             if (!isNaN(updateQuantity) && updateQuantity > 0)
// //             {
// //                 if (!isNaN(updateQuantity) && updateQuantity > cart[i].maxAmount ) {

// //                     alert("quantity exceeds the available stock.");

// //                     cart[i].quantity = cart[i].maxAmount;
// //                     cart[i].AmountInTheStock = cart[i].maxAmount - cart[i].quantity;
// //                 }
               
// //                 else
// //                     cart[i].quantity = updateQuantity;
// //                 cart[i].AmountInTheStock = cart[i].maxAmount - cart[i].quantity;
// //             }
            

// //             break;

// //         }
// //     }
// //     localStorage.setItem("Cart", JSON.stringify(cart));
// //     displayCart();

// //     UpdateCartSummary();
// // }

//   function removeFromCart(id) {
//       let cart = JSON.parse(localStorage.getItem("Cart")) || [];
//       cart = cart.filter(a => a.ProductId != id);
//       localStorage.setItem("Cart", JSON.stringify(cart));
//       displayCart();
//       // updateCartSummary();
//   }

 
//   document.addEventListener("DOMContentLoaded", function () {
//       displayCart();
//   });



///////////////////////////////////////////////////////



// function displayCart() {
//   let cart = JSON.parse(localStorage.getItem("Cart")) || [];
//   let cartTable = document.getElementById("cartTable");
//   let emptyCartMessage = document.getElementById("emptyCartMessage");

//   cartTable.innerHTML = `
//       <tr>
//           <th>اسم المنتج</th>
//           <th>سعر الوحدة</th>
//           <th>إجراء</th>
//       </tr>
//   `;

//   if (cart.length === 0) {
//       emptyCartMessage.style.display = 'block';
//       cartTable.style.display = 'none';
//   } else {
//       emptyCartMessage.style.display = 'none';
//       cartTable.style.display = 'table';

//       let totalPrice = 0;

//       cart.forEach(item => {
//           let row = document.createElement('tr');
//           row.innerHTML = `
//               <td>${item.ProductName}</td>
//               <td>${item.UnitPrice} د.أ</td>
//               <td><a href="javascript:void(0)" onclick="removeFromCart(${item.ProductId})">إزالة</a></td>
//           `;
//           cartTable.appendChild(row);
//           totalPrice +=item.UnitPrice;
//       });

//       let totalRow = document.createElement('tr');
//       totalRow.innerHTML = `
//           <td colspan="4" style="text-align:right;"><strong>المجموع الكلي</strong></td>
//           <td id="pricee">${totalPrice.toFixed(2)} د.أ</td>
//       `;
//       cartTable.appendChild(totalRow);
//   }
// }



  
function EAddToFav(id, name, unitPrice, image) {
  let cart = JSON.parse(localStorage.getItem("Cart")) || [];
  const heartIcon = document.getElementById(`fav-heart-${id}`);

  const existingIndex = cart.findIndex(item => item.ProductId == id);

  if (existingIndex !== -1) {
      // المنتج موجود، إحذف من المفضلة
      cart.splice(existingIndex, 1);
      localStorage.setItem("Cart", JSON.stringify(cart));

      if (heartIcon) {
          heartIcon.classList.remove("fa-solid", "heart-red");
          heartIcon.classList.add("fa-regular");
      }

  } else {
      // المنتج مش موجود، ضيفه
      cart.push({
          ProductId: id,
          ProductName: name,
          UnitPrice: unitPrice,
          image: image
      });
      localStorage.setItem("Cart", JSON.stringify(cart));

      if (heartIcon) {
          heartIcon.classList.remove("fa-regular");
          heartIcon.classList.add("fa-solid", "heart-red");
      }
  }
}



// function removeFromCart(id) {
//   let cart = JSON.parse(localStorage.getItem("Cart")) || [];
//   cart=cart.filter(a=> a.ProductId != id);
//   localStorage.setItem("Cart", JSON.stringify(cart));
//   displayCart();
// }



