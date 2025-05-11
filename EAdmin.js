// function acceptProject(projectId) {
//     const xhr = new XMLHttpRequest();
//     xhr.open("GET", `EAdmin.php?action=accept&id=${projectId}`, true);
//     xhr.onload = function() {
//         if (xhr.status === 200) {
//             alert("تم قبول الطلب!");
//             window.location.reload();
//         }
//     };
//     xhr.send();
// }

// function rejectProject(projectId) {
//     const xhr = new XMLHttpRequest();
//     xhr.open("GET", `EAdmin.php?action=reject&id=${projectId}`, true);
//     xhr.onload = function() {
//         if (xhr.status === 200) {
//             alert("تم رفض الطلب!");
//             window.location.reload();
//         }
//     };
//     xhr.send();
// }

// function deactivateProject(projectId) {
//     const xhr = new XMLHttpRequest();
//     xhr.open("GET", `EAdmin.php?action=deactivate&id=${projectId}`, true);
//     xhr.onload = function() {
//         if (xhr.status === 200) {
//             alert("تم إلغاء تفعيل المشروع!");
//             window.location.reload();
//         }
//     };
//     xhr.send();
// }

// function activateProject(projectId) {
//     const xhr = new XMLHttpRequest();
//     xhr.open("GET", `EAdmin.php?action=activate&id=${projectId}`, true);
//     xhr.onload = function() {
//         if (xhr.status === 200) {
//             alert("تم تفعيل المشروع!");
//             window.location.reload();
//         }
//     };
//     xhr.send();
// }

// function filterOrders(status) {
//     window.location.href = `EAdmin.php?filter=${status}`;
// }
function acceptProject(projectId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `testAdminApi.php?action=accept&id=${projectId}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                alert("تم قبول الطلب! تم إرسال كلمة المرور إلى البريد الإلكتروني.");
                window.location.reload();
                location.reload();
            } else {
                alert("حدث خطأ: " + response.message);
            }
        }
    };
    xhr.send();
}




function rejectProject(projectId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `testAdminApi.php?action=reject&id=${projectId}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                alert("تم رفض الطلب!");
                window.location.reload();
            } else {
                alert("حدث خطأ: " + response.message);
            }
        }
    };
    xhr.send();
}

function deactivateProject(projectId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `testAdminApi.php?action=deactivate&id=${projectId}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                alert("تم إلغاء تفعيل المشروع!");
                window.location.reload();
            } else {
                alert("حدث خطأ: " + response.message);
            }
        }
    };
    xhr.send();
}

function activateProject(projectId) {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", `testAdminApi.php?action=activate&id=${projectId}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                alert("تم تفعيل المشروع!");
                window.location.reload();
            } else {
                alert("حدث خطأ: " + response.message);
            }
        }
    };
    xhr.send();
}

function filterOrders(status) {
    window.location.href = `testAdminApi.php?filter=${status}`;
}