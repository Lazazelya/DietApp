<?php
session_start();
require 'db.php';
include 'navbar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];


    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if ($stmt === false) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password_hash'])) {
       
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        header("Location: main.php");
        exit();
    } else {
        echo "Неверный email или пароль.";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма входа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="res\style.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            background-color: rgba(250, 242, 245, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-form {
            max-width: 450px;
            margin: 50px auto;
            padding: 20px;
            background-color: rgba(171, 5, 66, 0.6);
            border: 2px solid  rgba(171, 5, 66,1);
            border-radius: 10px;
            
        }

        .login-form input {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            background-color: rgba(250, 242, 245, 0.8);
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: rgba(171, 5, 66, 0.7);
            border: 2px solid  rgba(171, 5, 66,1);
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-form button:hover {
            background-color:rgba(171, 5, 66, 1);
        }

        .registr-btn {
            background-color: rgba(171, 5, 66, 0.3);
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="login-form">
        <h2>Вход</h2>
        <form method="POST" action="">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Введите email" required>

            <label for="password">Пароль</label>
            <input type="password" name="password" id="password" placeholder="Введите пароль" required>

            <button type="submit">Войти</button>
            <button onclick="window.location.href='registr.php';" class="registr-btn">Зарегистрируйтесь</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>