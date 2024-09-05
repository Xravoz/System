<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
    margin: 0;
    padding: 0;
}

.sidebar {
    /* background-color: #333; */
    color: white;
    text-align: right;
    width: 200px;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    overflow-x: hidden;
    padding-top: 20px;
}

.content {
    margin-left: 200px;
    padding: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: right;
}

th {
    background-color: #333;
    color: white;
}

@media screen and (max-width: 600px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .content {
        margin-left: 0;
    }
}

.logo img {
    width: 100px;
}
</style>

<body dir="rtl">

    <div class="sidebar">
        <div class="logo">
            <img src="images/logo-header.png" alt="">
        </div>
        <a href="add_lesson.php">إضافة درس</a>
        <a href="teacher_stats.php">عرض الإحصائيات</a>
        <a href="logout.php">تسجيل الخروج</a>

    </div>

    <div class="content">
        <h1>مرحبًا، <?php echo $_SESSION['username']; ?></h1>
        <p>اختر من القائمة الجانبية ما تريد القيام به.</p>
    </div>
    <script>
    function toggleSidebar() {
        var sidebar = document.getElementById("sidebar");
        var content = document.getElementById("content");
        if (sidebar.style.left === "-200px") {
            sidebar.style.left = "0";
            content.style.marginLeft = "200px";
        } else {
            sidebar.style.left = "-200px";
            content.style.marginLeft = "0";
        }
    }
    </script>
</body>

</html>