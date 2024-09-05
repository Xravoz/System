<?php
session_start();

// التحقق من أن المستخدم هو الأدمن
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$hourly_rate = 100; // سعر الساعة للمعلم

// جلب بيانات المعلمين والدروس
$teachers_file = 'user.json';
$teachers = file_exists($teachers_file) ? json_decode(file_get_contents($teachers_file), true) : [];

$billing_info = [];

foreach ($teachers as $teacher_id => $teacher) {
    $lessons_file = "lessons_{$teacher_id}.json";
    $lessons = file_exists($lessons_file) ? json_decode(file_get_contents($lessons_file), true) : [];

    $total_hours = 0;
    foreach ($lessons as $lesson) {
        $total_hours += $lesson['duration'];
    }

    // حساب السعر بناءً على عدد الساعات
    $total_payment = $total_hours * $hourly_rate; // السعر بناءً على عدد الساعات

    $billing_info[] = [
        'teacher_name' => $teacher['user'], // استخدام المفتاح 'user' لاسم المعلم
        'total_hours' => $total_hours,
        'total_payment' => $total_payment // استخدام السعر المحسوب
    ];
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الفواتير</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="sidebar">
        <a href="admin_dashboard.php">لوحة التحكم</a>
        <a href="billing.php">الفواتير</a>
        <a href="logout.php">تسجيل الخروج</a>
    </div>

    <div class="content">
        <h2>الفواتير</h2>

        <table border="1">
            <tr>
                <th>اسم المعلم</th>
                <th>عدد الساعات</th>
                <th>الإجمالي (جنيه)</th>
            </tr>
            <?php foreach ($billing_info as $info): ?>
            <tr>
                <td><?php echo htmlspecialchars($info['teacher_name']); ?></td>
                <td><?php echo htmlspecialchars($info['total_hours']); ?> ساعة</td>
                <td><?php echo htmlspecialchars($info['total_payment']); ?> جنيه</td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>