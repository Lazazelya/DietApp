<?php
session_start();
require 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$goal = $_POST['goal'];
$reminder = $_POST['reminder'];
$water_goal = (int)$_POST['water_goal'];
$fruit_goal = (int)$_POST['fruit_goal'];

$stmt = $conn->prepare("SELECT * FROM user_settings WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;
$stmt->close();

if ($exists) {
   
    $stmt = $conn->prepare("
        UPDATE user_settings 
        SET goal = ?, reminder = ?, water_goal = ?, fruit_goal = ? 
        WHERE user_id = ?
    ");
    $stmt->bind_param("ssiii", $goal, $reminder, $water_goal, $fruit_goal, $user_id);
} else {
   
    $stmt = $conn->prepare("
        INSERT INTO user_settings (user_id, goal, reminder, water_goal, fruit_goal) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issii", $user_id, $goal, $reminder, $water_goal, $fruit_goal);
}

if ($stmt->execute()) {
    
    $result = update_user_calories($conn, $user_id);

    if (strpos($result, 'Ошибка') !== false) {
        $_SESSION['error'] = $result; 
        header("Location: user_settings.php");
    } else {
        $_SESSION['message'] = "Настройки сохранены и калории обновлены."; 
        header("Location: user_settings.php");
    }
} else {
    echo "Ошибка: " . $stmt->error;
}

$stmt->close();
$conn->close();

function calculate_calories($age, $weight, $height, $gender, $activity_level, $goal) {
    if ($gender === 'male') {
        $bmr = 10 * $weight + 6.25 * $height - 5 * $age + 5;
    } else if ($gender === 'female') {
        $bmr = 10 * $weight + 6.25 * $height - 5 * $age - 161;
    } else {
        return null;
    }

    $activity_multiplier = [
        'sedentary' => 1.2,
        'light' => 1.375,
        'moderate' => 1.55,
        'active' => 1.725,
        'very_active' => 1.9
    ];

    $activity_factor = $activity_multiplier[$activity_level] ?? 1.2;
    $calories = $bmr * $activity_factor;

    if ($goal === 'lose') {
        $calories -= 500;
    } else if ($goal === 'gain') {
        $calories += 500;
    }

    return max($calories, 1200);
}

function update_user_calories($conn, $user_id) {
    
    $stmt = $conn->prepare("SELECT age, weight, height, gender, activity_level FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {
        return "Ошибка: пользователь не найден.";
    }

    $stmt = $conn->prepare("SELECT goal FROM user_settings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $goal_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$goal_data) {
        return "Ошибка: цель пользователя не найдена.";
    }

    $goal = $goal_data['goal'];
    $daily_calories = calculate_calories(
        $user['age'],
        $user['weight'],
        $user['height'],
        $user['gender'],
        $user['activity_level'],
        $goal
    );

    if ($daily_calories === null) {
        return "Ошибка расчета калорий.";
    }

    $stmt = $conn->prepare("UPDATE users SET daily_calories = ? WHERE id = ?");
    $stmt->bind_param("di", $daily_calories, $user_id);

    if ($stmt->execute()) {
        $stmt->close();
        return "Калории успешно обновлены.";
    } else {
        $error = $stmt->error;
        $stmt->close();
        return "Ошибка обновления калорий: " . $error;
    }
}
?>
