 // عند اختيار الفئة يتم تحديث الخيارات
 function updateOptions() {
    const category = document.getElementById("category").value;
    const userTypeSection = document.getElementById("userTypeSection");
    const documents = document.getElementById("documents");

    // عرض أو إخفاء الحقول بناءً على الفئة
    if (category === "careProducts") {
        userTypeSection.style.display = "block";
    } else {
        userTypeSection.style.display = "block"; // إظهار موزع/مصنع لكل الفئات
        documents.style.display = "none"; // إخفاء الوثائق إذا لم تكن "منتجات العناية الشخصية"
    }
}

// عرض الوثائق مباشرة إذا كان "مصنع" والفئة "منتجات العناية الشخصية"
function toggleDocumentsDirectly() {
    const category = document.getElementById("category").value;
    const type = document.getElementById("type").value;
    const documents = document.getElementById("documents");

    if (category === "careProducts" && type === "manufacturer") {
        documents.style.display = "block";
    } else {
        documents.style.display = "none";
    }
}

// منع الإرسال الافتراضي والتأكد من صحة الإدخالات
document.getElementById("projectForm").addEventListener("submit", function (event) {
    event.preventDefault();

    // التحقق من صحة الإدخالات قبل الإرسال
    if (!validateJordanianMobileNumber()) {
        showDialog("الرقم المدخل غير صحيح، يرجى إعادة إدخال الرقم.");
        document.getElementById("contactNumber").style.borderColor = "red";
        return;
    }

    const category = document.getElementById("category").value;
    const type = document.getElementById("type") ? document.getElementById("type").value : "";
    const documents = document.getElementById("careDocuments");

    if (category === "careProducts" && type === "manufacturer" && documents.files.length === 0) {
        showDialog("يجب إرفاق الوثائق المطلوبة لمنتجات العناية الشخصية.");
        return;
    }

    // إرسال النموذج إلى الخادم
    showDialog("جاري إرسال الطلب، يرجى الانتظار...");
    this.submit();
});

// التحقق من رقم الهاتف الأردني
function validateJordanianMobileNumber() {
    const phoneNumber = document.getElementById("contactNumber").value;
    const jordanianMobileRegex = /^(079|078|077)\d{7}$/;

    return jordanianMobileRegex.test(phoneNumber);
}

// عرض رسالة في نافذة حوار
function showDialog(message) {
    closeDialog(); // إغلاق أي نافذة موجودة سابقًا
    const dialogBox = document.createElement("div");
    dialogBox.className = "dialog-box";
    dialogBox.innerHTML = `
<p>${message}</p>
<button onclick="closeDialog()">إغلاق</button>
`;
    document.body.appendChild(dialogBox);
}

// إغلاق نافذة الحوار
function closeDialog() {
    const dialogBox = document.querySelector(".dialog-box");
    if (dialogBox) {
        document.body.removeChild(dialogBox);
    }
}

// الانتقال إلى صفحة تسجيل الدخول
function handleLogin() {
    window.location.href = "ELogIn.php";
}

// الانتقال إلى الصفحة الرئيسية
function goToHomepage() {
    window.location.href = "EIndex.php";
}
// إظهار حقل رمز التحقق عند الضغط على "تحقق"
function showVerificationField() {
    const verificationContainer = document.getElementById("verificationContainer");
    verificationContainer.style.display = "block"; // إظهار الحقل
    const verificationField = document.getElementById("verificationField");
    verificationField.value = ""; // تفريغ أي قيمة سابقة
    verificationField.focus(); // تحديد الحقل تلقائيًا
}