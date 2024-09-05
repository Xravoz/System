<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];
$username = $_SESSION['username']; // الحصول على اسم المستخدم


// تحميل قائمة الطلاب من ملف JSON
$students_file = "students_{$teacher_id}.json";
$students = file_exists($students_file) ? json_decode(file_get_contents($students_file), true) : [];

// تحقق من كون $students مصفوفة، وإذا لم تكن كذلك اجعلها مصفوفة فارغة
if (!is_array($students)) {
    $students = [];
}

// تحميل بيانات الدروس
$lessons_file = "lessons_{$teacher_id}.json";
$lessons = file_exists($lessons_file) ? json_decode(file_get_contents($lessons_file), true) : [];

// حساب إحصائيات المعلم
$num_students = count($students);
$total_hours = 0;
$num_lessons = count($lessons);

foreach ($lessons as $lesson) {
    $total_hours += $lesson['duration'];
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المعلم</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
table {
    width: 100%;
    border-collapse: collapse;
}

table,
th,
td {
    border: 1px solid black;
}

th,
td {
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

a {
    color: #fff;
    text-decoration: none;
    background-color: #c69023;
    align-items: center;
    text-align: center;
    padding: 10px 15px;
    border-radius: 5px;
}

/* إضافة تنسيق ريسبونسيف */
@media (max-width: 600px) {

    table,
    thead,
    tbody,
    th,
    td,
    tr {
        display: block;
    }

    th {
        display: none;
        /* إخفاء العناوين في العرض الصغير */
    }

    tr {
        margin-bottom: 15px;
        /* إضافة مسافة بين الصفوف */
    }

    td {
        text-align: right;
        /* محاذاة النص لليمين */
        position: relative;
        padding-left: 50%;
        /* إضافة مساحة لعرض البيانات */
    }

    td::before {
        content: attr(data-label);
        /* استخدام البيانات كعنوان */
        position: absolute;
        left: 0;
        width: 50%;
        padding-left: 10px;
        font-weight: bold;
        text-align: left;
    }
}
</style>

<body>
    <div class="sidebar">
        <a href="logout.php">تسجيل الخروج</a>
    </div>

    <div class=" content" dir="rtl">
        <h2>لوحة تحكم المعلم</h2>

        <div class="stats">
            <h3>الإحصائيات:</h3>
            <table class="responsive-table">
                <tr>
                    <td><strong>عدد الطلاب:</strong></td>
                    <td><?php echo htmlspecialchars($num_students); ?></td>
                </tr>
                <tr>
                    <td><strong>إجمالي عدد الساعات:</strong></td>
                    <td><?php echo htmlspecialchars($total_hours); ?> دقيقه</td>
                </tr>
                <tr>
                    <td><strong>عدد الحصص:</strong></td>
                    <td><?php echo htmlspecialchars($num_lessons); ?></td>
                </tr>
            </table>
        </div>

        <div class="teacher-stats">
            <h3>الطلاب:</h3>
            <table class="responsive-table">
                <tbody>
                    <?php if (empty($students)): ?>
                    <tr>
                        <td>لا يوجد طلاب مخصصين.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <h3>الدروس:</h3>
        <div class="responsive-table">
            <table>
                <thead>
                    <tr>
                        <th>اسم الدرس</th>
                        <th>التاريخ</th>
                        <th>المدة (دقيقة)</th>
                        <th>عدد الطلاب</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lessons as $lesson): ?>
                    <tr>
                        <td data-label="اسم الدرس"><?php echo htmlspecialchars($lesson['lesson_name']); ?></td>
                        <td data-label="التاريخ"><?php echo htmlspecialchars($lesson['date']); ?></td>
                        <td data-label="المدة (دقيقة)"><?php echo htmlspecialchars($lesson['duration']); ?></td>
                        <td data-label="عدد الطلاب"><?php echo htmlspecialchars($lesson['students']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
<?php foreach ($lessons as $lesson): ?>
<tr>
    <td><?php echo htmlspecialchars($lesson['lesson_name']); ?></td>
    <td><?php echo htmlspecialchars($lesson['date']); ?></td>
    <td><?php echo htmlspecialchars($lesson['duration']); ?></td>
    <td><?php echo htmlspecialchars($lesson['students']); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
</body>

</html>