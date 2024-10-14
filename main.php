<?php
session_start();
require 'db.php';
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #bdacbb;
            margin-top: 30px;
            padding: 0;
        }

        h1, h2, h3 {
            margin-top: 30px;
            color: #333;
        }

        div {
            margin: 20px auto;
            padding: 20px;
            max-width: 600px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .t ul {
            list-style-type: none;
            padding: 0;
            text-align: left;
            display: inline-block;
        }


    </style>
</head>
<body>

<h1>Добро пожаловать на главную страницу!</h1>

<?php

if (isset($_SESSION['user_id'])) {
    echo "<p>Привет, пользователь! Добро пожаловать на наш сайт!</p>";
} else {
    echo "<p>Пожалуйста, войдите или зарегистрируйтесь, чтобы получить доступ к дополнительным функциям.</p>";
}
?>

<h2>Простые рецепты с КБЖУ</h2>

<div>
    <h3>1. Овсянка с бананом и медом</h3>
    <ul class="t">
        <li>Калории: 350 ккал</li>
        <li>Белки: 10 г</li>
        <li>Жиры: 5 г</li>
        <li>Углеводы: 65 г</li>
    </ul>
    <p>Ингредиенты: овсянка (50г), банан (1 шт.), мед (1 ст. л.), молоко (150 мл).</p>
</div>

<div>
    <h3>2. Куриное филе с овощами</h3>
    <ul class="t">
        <li>Калории: 450 ккал</li>
        <li>Белки: 40 г</li>
        <li>Жиры: 10 г</li>
        <li>Углеводы: 40 г</li>
    </ul>
    <p>Ингредиенты: куриное филе (150г), болгарский перец (100г), брокколи (100г), оливковое масло (1 ст. л.).</p>
</div>

<div>
    <h3>3. Гречневая каша с грибами</h3>
    <ul class="t">
        <li>Калории: 320 ккал</li>
        <li>Белки: 12 г</li>
        <li>Жиры: 7 г</li>
        <li>Углеводы: 50 г</li>
    </ul>
    <p>Ингредиенты: гречка (80г), шампиньоны (100г), лук (1 шт.), сливочное масло (1 ст. л.).</p>
</div>

</body>
</html>
