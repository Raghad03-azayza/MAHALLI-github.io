<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>عن "على قدك"</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Cairo', sans-serif;
      margin: 0;
      background-color: #f9f9f9;
      color: #333;
      overflow-x: hidden;
    }

    .video-header {
      position: relative;
      height: 80vh;
      overflow: hidden;
    }

    .video-header video {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      min-width: 100%;
      min-height: 100%;
      object-fit: cover;
    }

    .video-overlay {
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background: rgba(0, 0, 0, 0.4);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: white;
      text-align: center;
      padding: 20px;
    }

    .video-overlay h1 {
      font-size: 42px;
      margin-bottom: 10px;
    }

    .video-overlay p {
      font-size: 20px;
      max-width: 600px;
    }

    .section {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      padding: 60px 20px;
      gap: 40px;
      justify-content: center;
      opacity: 0;
      transform: translateY(40px);
      transition: all 1s ease-in-out;
    }

    .section.show {
      opacity: 1;
      transform: translateY(0);
    }

    .section:nth-child(even) {
      background-color: #f0f4f4;
    }

    .section-content {
      flex: 1;
      min-width: 280px;
      max-width: 500px;
    }

    .section-image {
      flex: 1;
      min-width: 280px;
      max-width: 400px;
    }

    .section-image img {
      width: 100%;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.4s ease;
    }

    .section-image img:hover {
      transform: scale(1.05) rotate(-1deg);
    }

    .section-title {
      font-size: 24px;
      color: #00695c;
      margin-bottom: 15px;
    }

    .section-text {
      font-size: 17px;
      line-height: 1.8;
    }

    footer {
      text-align: center;
      padding: 30px 20px;
      font-size: 14px;
      color: #777;
      background-color: #e0e0e0;
    }

    @media (max-width: 768px) {
      .section {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="video-header">
    <video autoplay loop muted playsinline>
      <source src="uploads/WhatsApp Video 2025-04-12 at 11.12.30 PM.mp4" type="video/mp4">
      المتصفح لا يدعم تشغيل الفيديو.
    </video>
    <div class="video-overlay">
      <h1>قصة "محلي"</h1>
      <p>منصة تهدف إلى تمكين البائعين المحليين وربطهم بالمشترين الباحثين عن التميز في المنتجات المحلية</p>
    </div>
  </div>

  <section class="section">
    <div class="section-image">
      <img src="https://blog.khamsat.com/wp-content/uploads/2020/10/%D8%A3%D9%81%D9%83%D8%A7%D8%B1-%D9%85%D8%B4%D8%A7%D8%B1%D9%8A%D8%B9-%D9%85%D8%A8%D8%AA%D9%83%D8%B1%D8%A9-1.png" alt="founding image">
    </div>
    <div class="section-content">
      <h2 class="section-title">تأسيس المنصة</h2>
      <p class="section-text">تأسست منصة "على قدك" بدافع دعم المنتج المحلي وتوفير مساحة رقمية للبائعين الصغار والحرفيين لعرض منتجاتهم والوصول إلى جمهور أوسع بطريقة عصرية.</p>
    </div>
  </section>

  <section class="section">
    <div class="section-content">
      <h2 class="section-title">من هو جمهورنا؟</h2>
      <p class="section-text">يستهدف الموقع فئتين رئيسيتين: البائعين المحليين مثل الحرفيين وصغار التجار، والمشترين الذين يفضلون دعم المنتجات المحلية والتميز بجودة فريدة.</p>
    </div>
    <div class="section-image">
      <img src="https://img.freepik.com/vector-premium/trabajador-profesional-supermercado-tienda-tienda-stocktacking-merchandising-contabilidad-caja_573942-1723.jpg" alt="audience">
    </div>
  </section>

  <section class="section">
    <div class="section-image">
      <img src="https://image.freepik.com/free-vector/project-teamwork-concept-illustration_42077-708.jpg" alt="join us">
    </div>
    <div class="section-content">
      <h2 class="section-title">كيف تنضم إلينا؟</h2>
      <p class="section-text">يمكن لأي صاحب مشروع التقديم بسهولة من خلال صفحة "انضم إلينا"، وذلك بتعبئة النموذج وتحميل المستندات الخاصة بالمشروع ليتم مراجعته ونشره.</p>
    </div>
  </section>

  <section class="section">
    <div class="section-content">
      <h2 class="section-title">التواصل معنا</h2>
      <p class="section-text">في حال وجود أي استفسار، يمكنكم التواصل معنا عبر صفحة "تواصل معنا" أو البريد الإلكتروني التالي: mahalli@gmail.com</p>
    </div>
    <div class="section-image">
      <img src="https://matixerp.com/wp-content/uploads/2023/06/Group-39852-1.png" alt="contact">
    </div>
  </section>

  <footer>
    © 2024 محلي. جميع الحقوق محفوظة.
  </footer>

  <script>
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
        }
      });
    }, {
      threshold: 0.2
    });

    document.querySelectorAll('.section').forEach(section => {
      observer.observe(section);
    });
  </script>
</body>
</html>
