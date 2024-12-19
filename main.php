<?php
session_start();
require 'db.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM user_settings WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();
$stmt->close();
$water_goal = $settings['water_goal'] ?? 8; 
$fruit_goal = $settings['fruit_goal'] ?? 6;

$stmt = $conn->prepare("SELECT WEmount, VEmount FROM user_activity WHERE user_id = ? AND action_date = CURDATE()");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_water, $current_fruits);
$stmt->fetch();
$stmt->close();

$current_water = $current_water ?? 0;
$current_fruits = $current_fruits ?? 0;

$stmt = $conn->prepare("SELECT reminder FROM user_settings WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($reminder);
$stmt->fetch();
$stmt->close();

switch ($reminder) {
    case 'eat-fruits':
        $reminder_text = "Не забывайте кушать фрукты и овощи, чтобы достичь норму элементов.";
        break;
    case 'drink-water':
        $reminder_text = "Пейте больше воды, чтобы поддерживать гидратацию.";
        break;
    case 'exercise':
        $reminder_text = "Не забывайте выполнять физические упражнения!";
        break;
    default:
        $reminder_text = "Не забудьте выполнить свои цели!";
        break;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
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

        .tracker {
            margin: 20px 0;
        }

        .glasses, .foods {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }

        .glasses img, .foods img {
            width: 60px;
            height: 60px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .glasses img:hover, .foods img:hover {
            transform: scale(1.1);
        }

        .glass.filled {
            content: url('glass-full.png'); 
        }

        p {
            font-size: 18px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="reminder">
        <h2>Напоминание</h2>
        <p><?php echo htmlspecialchars($reminder_text); ?></p>
    </div>

    <div class="tracker water-tracker">
        <h2>Трекер воды</h2>
        <div class="glasses">
            <?php for ($i = 0; $i < $water_goal; $i++): ?>
                <img src="<?php echo ($i < floor($current_water / 300)) ? 'stat/GoWF.png' : 'stat/GoWE.png'; ?>" alt="Стакан" class="glass">
            <?php endfor; ?>
        </div>
        <p>Вы выпили: <span id="water-intake"><?php echo $current_water; ?></span> мл</p>
    </div>

    <div class="tracker food-tracker">
        <h2>Фрукты и овощи</h2>
        <div class="foods">
            <?php for ($i = 0; $i < $fruit_goal; $i++): ?>
                <img src="<?php echo ($i < $current_fruits) ? 'stat/VF.png' : 'stat/VE.png'; ?>" alt="Фрукт/Овощ" class="food">
            <?php endfor; ?>
        </div>
        <p>Вы съели: <span id="food-count"><?php echo $current_fruits; ?></span> из <?php echo $fruit_goal; ?></p>
    </div>
</div>

<script>
const glasses = document.querySelectorAll('.glass');
const waterIntakeDisplay = document.getElementById('water-intake');
let waterIntake = <?php echo $current_water; ?>;

glasses.forEach((glass, index) => {
    glass.addEventListener('click', () => {
        if (glass.src.includes('GoWF.png')) {
            glass.src = 'stat/GoWE.png'; 
            waterIntake -= 300; 
            saveActivity('water', -300); 
        } else {
            
            glass.src = 'stat/GoWF.png'; 
            waterIntake += 300; 
            saveActivity('water', 300); 
        }
        waterIntakeDisplay.textContent = waterIntake;
    });
});

const foods = document.querySelectorAll('.food');
const foodCountDisplay = document.getElementById('food-count');
let foodCount = <?php echo $current_fruits; ?>;

foods.forEach((food, index) => {
    food.addEventListener('click', () => {
        if (food.src.includes('VF.png')) {
            food.src = 'stat/VE.png'; 
            foodCount -= 1;
            saveActivity('fruit', -1); 
        } else {
            food.src = 'stat/VF.png'; 
            foodCount += 1; 
            saveActivity('fruit', 1); 
        }
        foodCountDisplay.textContent = foodCount;
    });
});

function saveActivity(type, change) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_activity.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(`type=${type}&amount=${change}`);
}

</script>

</body>
</html>
