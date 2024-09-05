<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $users = json_decode(file_get_contents("users.json"), true);

   
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

           
            if ($user['role'] === 'teacher') {
                $_SESSION['teacher_id'] = $user['teacher_id'];
            }

       
            if ($user['role'] === 'admin') {
                header("Location: admin_stats.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        }
    }

    // إذا كان اسم المستخدم أو كلمة المرور غير صحيحة
    $error = "اسم المستخدم أو كلمة المرور غير صحيحة.";
}
?>

<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="logo">
        <img src="images/logo-header.png" alt="">
    </div>
    <div class="text-top">
        <h1>مرحبا بكم في <span>أكاديمية الفتح</span></h1>
    </div>
    <div class="content">
        <h2>تسجيل الدخول</h2>
        <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <label for="username">اسم المستخدم:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">كلمة المرور:</label>
            <input type="password" id="password" name="password" required>
            <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }

            span {
                color: #009e69
            }

            .content {
                max-width: 500px;
                margin: auto;
                padding: 20px;
                background-color: #fff;
                border: 1px solid #ddd;
                border-radius: 20px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                margin-top: 50px;
            }

            .text-top {
                align-items: center;
                text-align: center;
            }

            h2 {
                color: #333;
                margin-top: 0;
                text-align: center;
            }

            form {
                display: flex;
                flex-direction: column;
            }

            label {
                margin-bottom: 10px;
                color: #333;
            }

            input {
                padding: 10px;
                margin-bottom: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }




            .success {
                color: green;
                margin-bottom: 20px;
                text-align: center;
            }

            .error {
                color: red;
                margin-bottom: 20px;
                text-align: center;
            }

            .logo img {
                width: 100px;
            }

            @media only screen and (max-width: 600px) {
                .content {
                    max-width: 90%;
                }
            }
            </style>

            <button type="submit">تسجيل الدخول</button>
        </form>
    </div>
</body>

</html>