<?php
session_start();
require 'db.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_date = date('Y-m-d'); // Получаем текущую дату

// Удаление продукта за текущую дату
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    // Удаление продукта только за текущий день
    $stmt = $conn->prepare("DELETE FROM calorie_tracker WHERE product_id = ? AND user_id = ? AND entry_date = ?");
    $stmt->bind_param("iis", $product_id, $user_id, $current_date);
    $stmt->execute();

    // Пересчет калорий за текущий день
    $stmt = $conn->prepare("SELECT SUM(calories) AS total_calories FROM calorie_tracker WHERE user_id = ? AND entry_date = ?");
    $stmt->bind_param("is", $user_id, $current_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $new_total_calories = $row['total_calories'] ?? 0;

    $stmt = $conn->prepare("UPDATE users SET daily_calories = ? WHERE id = ?");
    $stmt->bind_param("di", $new_total_calories, $user_id);
    $stmt->execute();

    $_SESSION['current_daily_calories'] = $new_total_calories;

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Получаем информацию о пользователе и сожженных калориях за текущий день
$stmt = $conn->prepare("SELECT u.daily_calories, u.age, u.weight, u.height, u.gender, u.activity_level
                        FROM users u
                        WHERE u.id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$current_daily_calories = $user['daily_calories'];

// Запрос для получения сожженных калорий за тренировки, выполненные сегодня
$stmt = $conn->prepare("SELECT SUM(tp.calories_burned) AS calories_burned_today
                        FROM training_executions te
                        JOIN training_programs tp ON te.training_id = tp.id
                        WHERE te.user_id = ? AND DATE(te.execution_date) = ?");
$stmt->bind_param("is", $user_id, $current_date);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$calories_burned = $row['calories_burned_today'] ?? 0;  

// Вычисляем BMR и суточную норму калорий
if (isset($user['age'], $user['weight'], $user['height'], $user['gender'], $user['activity_level'])) {
    if ($user['gender'] == 'male') {
        $bmr = 88.36 + (13.4 * $user['weight']) + (4.8 * $user['height']) - (5.7 * $user['age']);
    } else {
        $bmr = 447.6 + (9.2 * $user['weight']) + (3.1 * $user['height']) - (4.3 * $user['age']);
    }

    // Уровни активности
    switch ($user['activity_level']) {
        case 'sedentary':
            $calorie_needs = $bmr * 1.2;
            break;
        case 'light':
            $calorie_needs = $bmr * 1.375;
            break;
        case 'moderate':
            $calorie_needs = $bmr * 1.55;
            break;
        case 'active':
            $calorie_needs = $bmr * 1.725;
            break;
        case 'very_active':
            $calorie_needs = $bmr * 1.9;
            break;
        default:
            $calorie_needs = null;
            break;
    }
} else {
    $calorie_needs = null;
}

// Получаем продукты, добавленные сегодня, и их калории
$stmt = $conn->prepare("SELECT calorie_tracker.product_id, food_products.name, calorie_tracker.calories
                        FROM calorie_tracker
                        JOIN food_products ON calorie_tracker.product_id = food_products.id
                        WHERE calorie_tracker.user_id = ? AND calorie_tracker.entry_date = ?");
$stmt->bind_param("is", $user_id, $current_date);
$stmt->execute();
$calorie_results = $stmt->get_result();

$total_calories_consumed = 0;
$added_products = [];
while ($row = $calorie_results->fetch_assoc()) {
    $added_products[] = $row;
    $total_calories_consumed += $row['calories'];
}

// Чистые калории (потребленные минус сожженные)
$net_calories = $total_calories_consumed - $calories_burned;

$calorie_limit = 20000; // Лимит калорий для предупреждения
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дневник калорий</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #bdacbb;
        }

        h1, h2 {
            text-align: center;
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #f9f9f9;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        form {
            display: inline;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>

<h1>Ваш дневник калорий</h1>

<h2>Сумма всех потребленных калорий за сегодня: <?php echo htmlspecialchars($total_calories_consumed); ?> ккал</h2>

<h2>Сожжено калорий тренировками: <?php echo htmlspecialchars($calories_burned); ?> ккал</h2>

<?php if ($calorie_needs !== null): ?>
    <h2>Ваша суточная норма калорий: <?php echo htmlspecialchars(round($calorie_needs, 2)); ?> ккал</h2>
    <h2>Остаток калорий до достижения нормы: <?php echo htmlspecialchars(round($calorie_needs - $net_calories, 2)); ?> ккал</h2>
<?php else: ?>
    <h2>Информация для расчета суточной нормы калорий неполная.</h2>
<?php endif; ?>

<?php if ($net_calories > $calorie_limit): ?>
    <h2 style="color: red;">Внимание: вы превысили лимит калорий на <?php echo htmlspecialchars($net_calories - $calorie_limit); ?> ккал!</h2>
<?php endif; ?>

<h3 class="center">Добавленные продукты за сегодня:</h3>
<table>
    <tr>
        <th>Название продукта</th>
        <th>Калории</th>
        <th>Действие</th>
    </tr>
    <?php foreach ($added_products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['calories']); ?></td>
            <td>
                <form method="POST" action="">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                    <button type="submit" name="delete_product">Удалить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
