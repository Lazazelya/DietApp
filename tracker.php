<?php
session_start();
require 'navbar.php';
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$meal_types = [
    'breakfast' => 'Завтрак',
    'lunch' => 'Обед',
    'dinner' => 'Ужин',
    'snack' => 'Перекус'
];

$stmt = $conn->prepare("SELECT daily_calories, img_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Ошибка: пользователь не найден.";
    exit();
}

$daily_calories = $user['daily_calories'];
$img_id = $user['img_id'];
$stmt->close();

$user_avatar = null;
if ($img_id) {
    $stmt = $conn->prepare("SELECT image FROM images WHERE img_id = ?");
    $stmt->bind_param("i", $img_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image_data = $result->fetch_assoc();
    if ($image_data) {
        
        $user_avatar = 'data:image/jpeg;base64,' . base64_encode($image_data['image']);
    }
    $stmt->close();
}

$calories_per_meal = [
    'breakfast' => $daily_calories * 0.2,
    'lunch' => $daily_calories * 0.3,
    'dinner' => $daily_calories * 0.4,
    'snack' => $daily_calories * 0.1
];

$meals = ['breakfast', 'lunch', 'dinner', 'snack'];
$meal_foods = [];

foreach ($meals as $meal) {
    $stmt = $conn->prepare("SELECT f.name, fl.gramms, fl.calories, fl.proteins, fl.fats, fl.carbohydrates 
                            FROM food_log fl
                            JOIN food_items f ON fl.food_id = f.food_id
                            WHERE fl.user_id = ? 
                            AND fl.meal_type = ? 
                            AND DATE(fl.created_at) = CURDATE()");
    $stmt->bind_param("is", $user_id, $meal);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $meal_foods[$meal][] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracker</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 

</head>
<style>

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f1f1f1;
}

.container {
    display: flex;
}


.message-block {
    background-color:rgba(171, 5, 66, 0.7);
    padding: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.message-block p {
    margin: 0;
    font-size: 24px;
}

.user-avatar {
    border-radius: 50%;
    width: 50px;
    height: 50px;
}

.calories-chart {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.chart-item {
    background-color: #FFFFFF;
    padding: 20px;
    border-radius: 10px;
    width: 30%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.chart {
    height: 200px;
    background-color: #ddd;
    margin-top: 10px;
}

.meal-blocks {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.meal-block {
    background-color: #FFFFFF;
    padding: 20px;
    border-radius: 10px;
    width: 22%;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.meal-info {
    margin-top: 10px;
}

.add-food {
    background-color: #2ff5a2cc;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    margin-top: 15px;
}

.add-food:hover {
    background-color: #45a049;
}
</style>
<body>
    <div class="container">
        <div class="main-content">
            <div class="message-block">
                <p>Привет,здесь ты можешь следить за питанием.</p>
                <img class="user-avatar" src="<?php echo $user_avatar ? $user_avatar : 'default_avatar.jpg'; ?>" alt="User Avatar">
    
            </div>
            <div class="meal-blocks">
                <?php foreach ($meals as $meal): ?>
                    <div class="meal-block">
                        <h3><?php echo $meal_types[$meal]; ?></h3>
                        <p>Рекомендовано: <?php echo round($calories_per_meal[$meal], 2); ?> ккал</p>
                       
                        <canvas id="chart-<?php echo $meal; ?>" width="200" height="200"></canvas>
                        <div class="meal-info">
                            <?php 
                            if (isset($meal_foods[$meal]) && !empty($meal_foods[$meal])) {
                                foreach ($meal_foods[$meal] as $food) {
                                    
                                    echo "<p>{$food['name']} - {$food['gramms']} г</p>";
                                }
                            } else {
                                echo "<p>Нет продуктов для этого приема пищи за сегодня.</p>";
                            }
                            ?>
                        </div>
                        <button class="add-food" onclick="window.location.href='food_catalog.php'">+</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
  
    const mealData = <?php echo json_encode($meal_foods); ?>;
    const mealTypes = <?php echo json_encode($meal_types); ?>;
    const recommendedCalories = <?php echo json_encode($calories_per_meal); ?>;
    Object.keys(mealData).forEach(mealType => {
        const ctx = document.getElementById(`chart-${mealType}`).getContext('2d');
        const foodData = mealData[mealType] || [];
        const labels = foodData.map(food => food.name);
        const calories = foodData.map(food => food.calories);
        const totalConsumed = calories.reduce((sum, value) => sum + value, 0); 

        const remainingCalories = Math.max(recommendedCalories[mealType] - totalConsumed, 0);
        labels.push("Остаток");
        calories.push(remainingCalories);
        new Chart(ctx, {
            type: 'pie', 
            data: {
                labels: labels,
                datasets: [{
                    label: 'Калории',
                    data: calories,
                    backgroundColor: [
                        ...Array(foodData.length).fill('rgba(47, 245, 162, 0.4)'),
                        'rgba(171, 5, 66, 0.4)' 
                    ],
                    borderColor: [
                        ...Array(foodData.length).fill('rgba(47, 245, 162, 1)'), 
                        'rgba(171, 5, 66, 0.8)' 
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: `Калории - ${mealTypes[mealType]}`
                    }
                }
            }
        });
    });
</script>

</body>
</html>
