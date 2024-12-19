<?php
session_start();
ob_start(); 
require 'navbar.php';
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['food_id'])) {
    $food_id = intval($_GET['food_id']);

    $stmt = $conn->prepare("SELECT food_id, name, calorie_per_100, proteins, fats, carbohydrates FROM food_items WHERE food_id = ?");
    $stmt->bind_param("i", $food_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $food = $result->fetch_assoc();

    if (!$food) {
        echo "Продукт не найден.";
        exit();
    }
    $stmt->close();
} else {
    echo "Ошибка: продукт не выбран.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $meal_type = $_POST['meal_type'];
    $quantity = intval($_POST['quantity']);
    $total_calories = $_POST['total_calories'];
    $total_proteins = $_POST['total_proteins'];
    $total_fats = $_POST['total_fats'];
    $total_carbohydrates = $_POST['total_carbohydrates'];

    $valid_meal_types = ['breakfast', 'lunch', 'dinner', 'snack'];
    if (!in_array($meal_type, $valid_meal_types)) {
        echo "Ошибка: выбран неправильный тип приёма пищи.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO food_log (user_id, food_id, meal_type, calorie_per_100, gramms, calories, proteins, fats, carbohydrates) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisisdiii", $user_id, $food_id, $meal_type, $food['calorie_per_100'], $quantity, $total_calories, $total_proteins, $total_fats, $total_carbohydrates);

    if ($stmt->execute()) {
        header("Location: tracker.php");
        exit();
    } else {
        echo "Ошибка при добавлении продукта: " . $stmt->error;
    }

    $stmt->close();
}
ob_end_flush(); 
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить продукт</title>
    <link rel="stylesheet" href="styles.css">
    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgba(250, 242, 245, 1);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            border: 2px solid rgba(171, 5, 66, 0.8); 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 600px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        button{
            background-color: rgba(171, 5, 66, 0.7);
            border: 2px solid  rgba(171, 5, 66,1);
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer
        }
        button:hover{
            background-color: rgba(171, 5, 66, 1);
        }
    </style>
    <script>
     
function calculateNutrients() {
    var quantity = document.getElementById('quantity').value;
    var calorie_per_100 = <?php echo $food['calorie_per_100']; ?>;
    var proteins_per_100 = <?php echo $food['proteins']; ?>;
    var fats_per_100 = <?php echo $food['fats']; ?>;
    var carbohydrates_per_100 = <?php echo $food['carbohydrates']; ?>;

    if (quantity > 0) {
        var total_calories = (calorie_per_100 * quantity) / 100;
        var total_proteins = (proteins_per_100 * quantity) / 100;
        var total_fats = (fats_per_100 * quantity) / 100;
        var total_carbohydrates = (carbohydrates_per_100 * quantity) / 100;

        
        total_calories = total_calories.toFixed(2);
        total_proteins = total_proteins.toFixed(2);
        total_fats = total_fats.toFixed(2);
        total_carbohydrates = total_carbohydrates.toFixed(2);

        
        document.getElementById('total_calories').textContent = total_calories + ' ккал';
        document.getElementById('total_proteins').textContent = total_proteins + ' г';
        document.getElementById('total_fats').textContent = total_fats + ' г';
        document.getElementById('total_carbohydrates').textContent = total_carbohydrates + ' г';

        
        document.getElementById('total_calories_input').value = total_calories;
        document.getElementById('total_proteins_input').value = total_proteins;
        document.getElementById('total_fats_input').value = total_fats;
        document.getElementById('total_carbohydrates_input').value = total_carbohydrates;
    }
}


function validateForm() {
    var mealType = document.getElementById('meal_type').value;
    console.log("Тип приёма пищи, отправляемый на сервер: " + mealType);  
    if (mealType == "") {
        alert("Ошибка: пожалуйста, выберите тип приёма пищи.");
        return false;
    }
    return true;
}


    </script>
</head>
<body>

<div class="container">
    <h1>Добавить продукт: <?php echo htmlspecialchars($food['name']); ?></h1>

    <form method="POST" onsubmit="return validateForm()">
    <div>
    <label for="meal_type">Тип приёма пищи:</label>
    <select name="meal_type" id="meal_type" required>
        <option value="breakfast">Завтрак</option>
        <option value="lunch">Обед</option>
        <option value="dinner">Ужин</option>
        <option value="snack">Перекус</option>
    </select>
</div>


        <div>
            <label for="quantity">Количество (г):</label>
            <input type="number" name="quantity" id="quantity" min="1" value="1" required onchange="calculateNutrients()">
        </div>

        <div>
            <p><strong>Пищевая ценность (на 100 г):</strong></p>
            <p>Калории: <?php echo htmlspecialchars($food['calorie_per_100']); ?> ккал</p>
            <p>Белки: <?php echo htmlspecialchars($food['proteins']); ?> г</p>
            <p>Жиры: <?php echo htmlspecialchars($food['fats']); ?> г</p>
            <p>Углеводы: <?php echo htmlspecialchars($food['carbohydrates']); ?> г</p>
        </div>

        <div>
            <p><strong>Пищевая ценность на выбранное количество:</strong></p>
            <p>Калории: <span id="total_calories">0 ккал</span></p>
            <p>Белки: <span id="total_proteins">0 г</span></p>
            <p>Жиры: <span id="total_fats">0 г</span></p>
            <p>Углеводы: <span id="total_carbohydrates">0 г</span></p>
        </div>


        <input type="hidden" name="total_calories" id="total_calories_input">
        <input type="hidden" name="total_proteins" id="total_proteins_input">
        <input type="hidden" name="total_fats" id="total_fats_input">
        <input type="hidden" name="total_carbohydrates" id="total_carbohydrates_input">

        <button type="submit">Добавить</button>
    </form>

    <a href="food_catalog.php">Назад</a>
</div>

</body>
</html>
