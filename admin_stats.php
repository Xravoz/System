<?php
session_start();

// التحقق من تسجيل الدخول كمسؤول
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// تحميل بيانات المستخدمين من ملف JSON
$users = json_decode(file_get_contents("users.json"), true);

// معالجة إضافة طلاب لكل معلم
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_students'])) {
    $teacher_id = $_POST['teacher_id'];
    $students = explode(',', $_POST['students']); // قائمة الطلاب

    // حفظ الطلاب في ملف JSON مخصص لكل معلم
    $students_file = "students_{$teacher_id}.json";
    
    // قراءة الطلاب الحاليين إذا كانوا موجودين
    $existing_students = file_exists($students_file) ? json_decode(file_get_contents($students_file), true) : [];
    
    // دمج الطلاب الجدد مع الطلاب الحاليين
    $all_students = array_unique(array_merge($existing_students, $students));
    
    // حفظ الطلاب المحدثين في الملف
    file_put_contents($students_file, json_encode($all_students, JSON_PRETTY_PRINT));

    $success = "تم تخصيص الطلاب للمعلم بنجاح.";
}
// الدالة لحساب الإحصائيات للمعلم
function calculateTeacherStats($teacher_id) {
    $lessons_file = "lessons_{$teacher_id}.json";
    $students_file = "students_{$teacher_id}.json";

    // قراءة بيانات الدروس
    $lessons = file_exists($lessons_file) ? json_decode(file_get_contents($lessons_file), true) : [];
    
    // قراءة بيانات الطلاب
    $students = file_exists($students_file) ? json_decode(file_get_contents($students_file), true) : [];

    // حساب عدد الطلاب
    $num_students = count($students);

    // حساب إجمالي عدد الساعات
    $total_hours = 0;
    foreach ($lessons as $lesson) {
        $total_hours += $lesson['duration'];
    }

    // عدد الحصص
    $num_lessons = count($lessons);

    return [
        'num_students' => $num_students,
        'total_hours' => $total_hours,
        'num_lessons' => $num_lessons,
    ];
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم المسؤول</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
}

.sidebar {
    width: 150px;
    height: 100vh;
    background-color: #009e69;
    color: #fff;
    position: fixed;
    top: 0;
    left: 0;
    padding: 20px;
}

.content {
    margin-left: 250px;
    padding: 20px;
}

h2 {
    color: #333;
    margin-top: 0;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th,
td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

th {
    background-color: #f2f2f2;
    color: #333;
}

tr:nth-child(even) {
    background-color: #f6f6f6;
}

tr:hover {
    background-color: #e9e9e9;
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

a:hover {
    background-color: #45a049;
}

@media only screen and (max-width: 600px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: static;
    }

    .content {
        margin-left: 0;
    }
}

#students {
    width: 300px;
    margin-bottom: 10px;
    padding: 0;
    display: flex;
}

.red {
    background-color: red;
    margin-bottom: 20px;
}

.reed {

    margin-bottom: 20px;
}
</style>

<body>
    <div class="sidebar">
        <a href="logout.php">تسجيل الخروج</a>
    </div>

    <div class="content" dir="rtl">
        <h2>مرحبا بك <span>أ/ محمد خاطر</span></h2>
        <h3>إضافة طلاب لكل معلم</h3>
        <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST" action="admin_stats.php">
            <label for="teacher_id">اختيار المعلم:</label>
            <select id="teacher_id" name="teacher_id" required
                style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                <?php foreach ($users as $user): ?>
                <?php if (isset($user['role']) && $user['role'] === 'teacher'): ?>
                <option value="<?php echo $user['teacher_id']; ?>"
                    <?php echo (isset($_POST['teacher_id']) && $_POST['teacher_id'] == $user['teacher_id']) ? 'selected' : ''; ?>>
                    <?php echo $user['username']; ?></option>
                <?php endif; ?>
                <?php endforeach; ?>
            </select>

            <label for="students">إدخال أسماء الطلاب (فصل بين الأسماء بفاصلة):</label>
            <input type="text" id="students" name="students" required
                style="width: 98%; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">

            <button type="submit" name="assign_students" class="reed">تخصيص الطلاب</button>

            <button type="submit" name="reset_stats" class="red" onclick="return confirmReset();">إعادة تعيين
                الإحصائيات</button>
        </form>

        <?php
        // عرض الطلاب المخصصين للمعلم
        if (isset($_POST['teacher_id'])) { // تعديل هنا
            $teacher_id = $_POST['teacher_id']; // تعديل هنا
            $students_file = "students_{$teacher_id}.json";
            if (file_exists($students_file)) {
                $assigned_students = json_decode(file_get_contents($students_file), true);
                echo "<h3>طلاب المعلم   :</h3><ul>";
                foreach ($assigned_students as $student) {
                    echo "<li>" . htmlspecialchars($student) . " <form method='POST' style='display:inline, margin-bottom:20px;'>
                            <input type='hidden' name='teacher_id' value='{$teacher_id}'>
                            <input type='hidden' name='student' value='" . htmlspecialchars($student) . "'>
                            <button type='submit' name='remove_student'>حذف</button>
                          </form></li>";
                }
                echo "</ul>";
            }
        }

        // إضافة عرض إجمالي المبلغ للمعلم
        if (isset($stats)) {
            echo "<h3>إجمالي المبلغ للمعلم: " . number_format($total_payment, 2) . " جنيه</h3>"; // عرض إجمالي المبلغ
        }

        // معالجة حذف الطالب
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_student'])) {
            $teacher_id = $_POST['teacher_id'];
            $student_to_remove = $_POST['student'];
            $students_file = "students_{$teacher_id}.json";
            if (file_exists($students_file)) {
                $assigned_students = json_decode(file_get_contents($students_file), true);
                $assigned_students = array_diff($assigned_students, [$student_to_remove]); // حذف الطالب
                file_put_contents($students_file, json_encode(array_values($assigned_students), JSON_PRETTY_PRINT)); // حفظ التحديثات
                $success = "تم حذف الطالب بنجاح.";
            }
        }

        // معالجة إعادة تعيين الإحصائيات
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_stats'])) {
            foreach ($users as $user) {
                if (isset($user['role']) && $user['role'] === 'teacher') {
                    $teacher_id = $user['teacher_id'];
                    $lessons_file = "lessons_{$teacher_id}.json";
                    
                    // إعادة تعيين البيانات
                    file_put_contents($lessons_file, json_encode([])); // إعادة تعيين الدروس
                    // الطلاب لا يتم حذفهم
                }
            }
            $success = "تم إعادة تعيين الإحصائيات لجميع المعلمين بنجاح.";
        }
        ?>
        <script>
        // إضافة حدث لتحديث الطلاب عند تغيير اختيار المعلم
        document.getElementById('teacher_id').addEventListener('change', function() {
            this.form.submit(); // إرسال النموذج عند تغيير الاختيار
        });

        function confirmReset() {
            return confirm("هل أنت متأكد من إعادة تعيين الإحصائيات؟"); // تأكيد إعادة التعيين
        }
        </script>
    </div>
    <div class="content" dir="rtl">
        <h2>إحصائيات المعلمين</h2>

        <table>
            <thead>
                <tr>
                    <th>اسم المعلم</th>
                    <th>عدد الطلاب</th>
                    <th>إجمالي عدد الساعات</th>
                    <th>عدد الحصص</th>
                    <th>تواريخ الحصص</th>
                    <th>إجمالي المبلغ (جنيه)</th> <!-- عمود عرض إجمالي المبلغ -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <?php if (isset($user['role']) && $user['role'] === 'teacher'): ?>
                <?php
        // تحقق من وجود بيانات الدروس قبل الحساب
        $lessons_file = "lessons_{$user['teacher_id']}.json"; // ملف الدروس
        if (file_exists($lessons_file) && filesize($lessons_file) > 0) {
            $stats = calculateTeacherStats($user['teacher_id']);
            $lesson_dates = json_decode(file_get_contents($lessons_file), true);

            // حساب إجمالي عدد الساعات بالدقائق
            $total_minutes = 0;
            foreach ($lesson_dates as $lesson) {
                $total_minutes += $lesson['duration']; // تأكد من أن مدة الدرس بالدقائق
            }

            // تحويل الدقائق إلى ساعات
            $total_hours = $total_minutes / 60;

            // حساب إجمالي المبلغ بناءً على عدد الساعات
            $total_payment = $total_hours * 100; // حساب إجمالي المبلغ
        } else {
            $total_hours = 0; // إذا لم توجد بيانات، اجعل الساعات 0
            $total_payment = 0; // اجعل المبلغ 0
        }
    ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($stats['num_students']); ?></td>
                    <td><?php echo htmlspecialchars($total_hours); ?></td> <!-- عرض إجمالي الساعات المحسوبة -->
                    <td><?php echo htmlspecialchars($stats['num_lessons']); ?></td>
                    <td>
                        <select>
                            <?php if (!empty($lesson_dates)): ?>
                            <?php foreach ($lesson_dates as $lesson): ?>
                            <option><?php echo htmlspecialchars($lesson['date']); ?></option>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <option>غير متوفر</option>
                            <?php endif; ?>
                        </select> <!-- عرض تواريخ الحصص في قائمة منسدلة -->
                    </td>
                    <td><?php echo number_format($total_payment, 2); ?> جنيه</td> <!-- عرض إجمالي المبلغ بالجنيه -->
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>


        </table>
    </div>
    <style>
    @media (max-width:768px) {

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            overflow-x: auto;
            /* إضافة التمرير الأفقي */
            display: block;
            /* جعل الجدول كتلة */
        }

        th,
        td {
            min-width: 100px;
            /* تحديد عرض الحد الأدنى للخلية */
        }
    }
    </style>
</body>

</html>

</html>

</html>

</html>