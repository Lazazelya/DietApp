<?php

include 'db.php';
include 'navbar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = 1;
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($stmt === false) {
        die("Ошибка подготовки запроса: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        echo '<script>alert("Ошибка: Этот адрес электронной почты уже зарегистрирован.");</script>';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (email, password_hash, role_id) VALUES (?, ?, ?)");
        if ($stmt === false) {
            die("Ошибка подготовки запроса: " . $conn->error);
        }
        $stmt->bind_param("ssi", $email, $password, $role_id);

        if ($stmt->execute()) {
            echo '<script>
                alert("Регистрация прошла успешно!");
                setTimeout(function() {
                    window.location.href = "profile.php";
                }, 1000);
            </script>';
        } else {
            echo '<script>alert("Ошибка при регистрации: ' . $stmt->error . '");</script>';
        }
        $stmt->close();
    }


}

$conn->close();
?>

<div class="form-container">
    <div class="form-box">
        <h2>Регистрация</h2>
        <p>Присоединяйтесь к нашему сообществу для управления питанием и контроля калорий!</p>
        <form method="POST" action="">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Введите ваш Email" required>
            
            <label for="password">Пароль</label>
            <input type="password" name="password" placeholder="Введите ваш пароль" required>
            
            <button type="submit">Зарегистрироваться</button>
        </form>
    </div>
</div>

<style>
 
    body {
       
        margin: 0;
        padding: 0;
        background-color: rgba(250, 242, 245, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        color: #333;
    }


    .form-container {
        max-width: 450px;
        background-color: rgba(171, 5, 66, 0.6);
        border: 2px solid  rgba(171, 5, 66,1);
        padding: 20px;
        margin: 50px auto;
        border-radius: 10px;
    }

    .form-box input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: rgba(250, 242, 245, 0.8);
        
    }

    .form-box button {
        width: 100%;
        padding: 10px;
        background-color: rgba(171, 5, 66, 0.7);
        border: 2px solid  rgba(171, 5, 66,1);
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
      
        color: white;
    }

    .form-box button:hover {
        background-color:rgba(171, 5, 66, 1);
    }

    .form-box input:focus {
        outline: none;
        border-color: #66bb6a;
    }
</style>

