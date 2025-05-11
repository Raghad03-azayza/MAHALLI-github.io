//   // عرض رسالة الخطأ إذا كانت موجودة
//   <?php if (!empty($error)): ?>
//   showDialog("<?= $error ?>");
// <?php endif; ?>

function showDialog(message) {
  document.getElementById('dialog-message').textContent = message;
  document.getElementById('dialog-box').style.display = 'block';
  document.getElementById('dialog-overlay').style.display = 'block';

  // تفريغ الحقول
  document.querySelector('[name="email"]').value = '';
  document.querySelector('[name="password"]').value = '';
}

function closeDialog() {
  document.getElementById('dialog-box').style.display = 'none';
  document.getElementById('dialog-overlay').style.display = 'none';
}


// الانتقال إلى الصفحة الرئيسية
// function goToHomepage() {
//   window.location.href = "ERegisteration.php";
// }

