<?php
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// تحميل قائمة الطلاب من ملف JSON
$students_file = "students_{$teacher_id}.json";
$students = file_exists($students_file) ? json_decode(file_get_contents($students_file), true) : [];

// معالجة إضافة درس جديد
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lesson_name = $_POST['lesson_name'];
    $date = $_POST['date'];
    $duration = $_POST['duration'];
    $selected_students = $_POST['students']; // قائمة الطلاب المختارين

    // تحقق من ملء جميع البيانات
    if (empty($lesson_name) || empty($date) || empty($duration) || empty($selected_students)) {
        $error = "يرجى ملء جميع الحقول."; // رسالة خطأ إذا كانت البيانات غير مكتملة
    } else {
        // تحديد اسم ملف JSON للمعلم بناءً على ID المعلم
        $file = "lessons_{$teacher_id}.json";

        // تحقق من وجود الملف، إذا لم يكن موجودًا، أنشئه بمصفوفة فارغة
        if (!file_exists($file)) {
            file_put_contents($file, json_encode([]));
        }

        // حمل محتوى ملف JSON
        $lessons = json_decode(file_get_contents($file), true);

        // أضف الدرس الجديد إلى المصفوفة
        $lessons[] = [
            "lesson_name" => $lesson_name,
            "date" => $date,
            "duration" => $duration,
            "students" => implode(", ", $selected_students) // قائمة الطلاب بصيغة نصية
        ];

        // احفظ البيانات الجديدة في ملف JSON
        file_put_contents($file, json_encode($lessons, JSON_PRETTY_PRINT));

        // رسالة نجاح
        $success = "تم إضافة الدرس بنجاح.";

        // بعد إضافة الدرس بنجاح، إعادة توجيه لتجنب إعادة الإرسال عند التحديث
        header("Location: add_lesson.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة درس</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
form input {
    width: 90%;
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

#students {
    width: 100%;
    /* عرض كامل */
    height: 150px;
    /* ارتفاع محدد */
    border: 1px solid #ccc;
    /* حدود خفيفة */
    border-radius: 5px;
    /* زوايا مدورة */
    padding: 5px;
    /* حشوة داخلية */
    font-size: 16px;
    /* حجم خط مناسب */
    background-color: #f9f9f9;
    /* لون خلفية فاتح */
    transition: border-color 0.3s;
    margin-bottom: 20px;
    /* تأثير انتقال */
}

#students:focus {
    border-color: #007bff;
    /* لون الحدود عند التركيز */
    outline: none;
    /* إزالة الإطار الافتراضي */
}
</style>

<body>
    <div class="sidebar">
        <a href="dashboard.php">العودة إلى لوحة التحكم</a>
    </div>

    <div class="content">
        <h2>إضافة درس جديد</h2>
        <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <form method="POST" action="add_lesson.php">
            <label for="lesson_name">اسم الدورة:</label>
            <input type="text" id="lesson_name" name="lesson_name" required>

            <label for="date">التاريخ:</label>
            <input type="date" id="date" name="date" required>

            <label for="duration">مدة الدرس (بالدقائق):</label>
            <input type="number" id="duration" name="duration" required>

            <label for="students">اختار الطالب:</label>
            <select id="students" name="students[]" multiple required>
                <?php foreach ($students as $student): ?>
                <option value="<?php echo htmlspecialchars($student); ?>"><?php echo htmlspecialchars($student); ?>
                </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">إضافة</button>
        </form>
    </div>
</body>

</html>