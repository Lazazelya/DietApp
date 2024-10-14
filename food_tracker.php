<?php
session_start();
include 'navbar.php';
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT daily_calories FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$current_daily_calories = $user['daily_calories'];

$calorie_limit = 20000;

$search_query = '';
$no_results_message = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
}

if ($search_query) {
    $sql_search_query = $search_query . "%";
    $stmt = $conn->prepare("SELECT * FROM food_products WHERE name LIKE ?");
    $stmt->bind_param("s", $sql_search_query);
} else {
    $stmt = $conn->prepare("SELECT * FROM food_products");
}
$stmt->execute();
$products = $stmt->get_result();

if ($search_query && $products->num_rows === 0) {
    $no_results_message = "Продукты не найдены.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add']) && $current_daily_calories <= $calorie_limit) {
    $product_id = $_POST['product_id'];
    $amount = $_POST['amount'];

    // Вызов хранимой процедуры add_calories
    $stmt = $conn->prepare("CALL add_calories(?, ?, ?)");
    $stmt->bind_param("iid", $user_id, $product_id, $amount);
    $stmt->execute();


    header("Location: calories_tracker.php");
    exit();

}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Доступные продукты</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #bdacbb;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1, h2 {
            text-align: center;
        }
        .container {
            margin-top: 50px;
            width: 80%;
            max-width: 1200px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .search-form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .search-form input[type="text"] {
            width: 300px;
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-form button {
            padding: 8px 16px;
            background-color: rgba(139, 83, 179, 0.62);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-form button:hover {
            background-color: #715ac8;
        }
        table {
            width: 100%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        input[type="number"] {
            width: 80px;
            padding: 5px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-appearance: none;
            -moz-appearance: textfield;
            appearance: textfield;
        }
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        button {
            padding: 5px 10px;
            background-color: rgba(139, 83, 179, 0.62);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #715ac8;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Доступные продукты</h1>
    <div class="search-form">
        <form method="POST" action="">
            <input type="text" name="search_query" placeholder="Поиск продуктов" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" name="search">Поиск</button>
        </form>
    </div>

    <?php if ($no_results_message): ?>
        <div style="color: red; text-align: center;"><?php echo htmlspecialchars($no_results_message); ?></div>
    <?php endif; ?>

    <table>
        <tr>
            <th>Название</th>
            <th>Калорий на 100г</th>
            <th>Действия</th>
        </tr>
        <?php while ($product = $products->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['calories_per_100g']); ?></td>
                <td>
                    <?php if ($current_daily_calories < $calorie_limit): ?>
                        <form method="POST" action="" class="form-container">
                            <input type="hidden" name="product_id"  value="<?php echo htmlspecialchars($product['id']); ?>">
                            <input type="number" name="amount" min= "1" max="1000" step="0.01" placeholder="Граммы" required>
                            <button type="submit" name="add">Добавить</button>
                        </form>
                    <?php else: ?>
                        <div style="color: red;">Лимит калорий на день превышен. Добавление продуктов запрещено.</div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
