<?php
session_start();
require 'navbar.php';
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : null;
    $age = isset($_POST['age']) ? $_POST['age'] : null;
    $weight = isset($_POST['weight']) ? $_POST['weight'] : null;
    $height = isset($_POST['height']) ? $_POST['height'] : null;
    $gender = isset($_POST['gender']) ? $_POST['gender'] : null;
    $activity_level = isset($_POST['activity_level']) ? $_POST['activity_level'] : null;
    $errors = [];
    if ($username !== null && $username === '') {
        $errors[] = "Имя пользователя не может быть пустым или состоять только из пробелов.";
    }

    if (!empty($errors)) {
        $error_message = implode("<br>", $errors);
    } else {

        $update_query = "UPDATE users SET ";
        $update_params = [];
        $param_types = '';
        $fields = [];

        if ($username !== null) {
            $fields[] = "username = ?";
            $update_params[] = $username;
            $param_types .= "s";
        }

        if ($age !== null) {
            $fields[] = "age = ?";
            $update_params[] = $age;
            $param_types .= "i";
        }

        if ($weight !== null) {
            $fields[] = "weight = ?";
            $update_params[] = $weight;
            $param_types .= "d";
        }

        if ($height !== null) {
            $fields[] = "height = ?";
            $update_params[] = $height;
            $param_types .= "i";
        }

        if ($gender !== null) {
            $fields[] = "gender = ?";
            $update_params[] = $gender;
            $param_types .= "s";
        }

        if ($activity_level !== null) {
            $fields[] = "activity_level = ?";
            $update_params[] = $activity_level;
            $param_types .= "s";
        }


        if (empty($fields)) {
            die("Нет данных для обновления.");
        }

        $update_query .= implode(", ", $fields);
        $update_query .= " WHERE id = ?";
        $update_params[] = $user_id;
        $param_types .= "i";

        $stmt = $conn->prepare($update_query);
        if ($stmt === false) {
            die("Ошибка подготовки запроса: " . $conn->error);
        }

        $stmt->bind_param($param_types, ...$update_params);

        if ($stmt->execute()) {
            $success_message = "Данные успешно обновлены!";
        } else {
            $error_message = "Ошибка обновления данных: " . $stmt->error;
        }

        $stmt->close();
    }
}


$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #bdacbb;
            padding: 20px;
        }
        .profile-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .profile-item {
            margin-bottom: 15px;
        }
        .profile-item label {
            font-weight: bold;
            display: block;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: rgba(139, 83, 179, 0.62);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #715ac8;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>Профиль пользователя</h2>

    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="profile-item">
            <label>Имя пользователя:</label>
            <input type="text" name="username" value="<?php echo isset($user['username']) ? htmlspecialchars($user['username']) : ''; ?>">
        </div>

        <div class="profile-item">
            <label>Возраст:</label>
            <input type="number" name="age" min="1"  max = "130" value="<?php echo isset($user['age']) ? htmlspecialchars($user['age']) : ''; ?>" required>
        </div>

        <div class="profile-item">
            <label>Вес (кг):</label>
            <input type="number" step="0.01" name="weight" min="1" max ="150" value="<?php echo isset($user['weight']) ? htmlspecialchars($user['weight']) : ''; ?>" required>
        </div>

        <div class="profile-item">
            <label>Рост (см):</label>
            <input type="number" name="height" min="1"  max = "300" value="<?php echo isset($user['height']) ? htmlspecialchars($user['height']) : ''; ?>" required>
        </div>

        <div class="profile-item">
            <label>Пол:</label>
            <select name="gender">

                <option value="male" <?php echo isset($user['gender']) && $user['gender'] === 'male' ? 'selected' : ''; ?>>Мужской</option>
                <option value="female" <?php echo isset($user['gender']) && $user['gender'] === 'female' ? 'selected' : ''; ?>>Женский</option>
            </select>
        </div>

        <div class="profile-item">
            <label>Уровень активности:</label>
            <select name="activity_level">

                <option value="sedentary" <?php echo isset($user['activity_level']) && $user['activity_level'] === 'sedentary' ? 'selected' : ''; ?>>Сидячий</option>
                <option value="light" <?php echo isset($user['activity_level']) && $user['activity_level'] === 'light' ? 'selected' : ''; ?>>Легкая активность</option>
                <option value="moderate" <?php echo isset($user['activity_level']) && $user['activity_level'] === 'moderate' ? 'selected' : ''; ?>>Умеренная активность</option>
                <option value="active" <?php echo isset($user['activity_level']) && $user['activity_level'] === 'active' ? 'selected' : ''; ?>>Активный</option>
                <option value="very_active" <?php echo isset($user['activity_level']) && $user['activity_level'] === 'very_active' ? 'selected' : ''; ?>>Очень активный</option>
            </select>
        </div>

        <button type="submit">Редактировать профиль</button>
    </form>

</div>

</body>
</html>
