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
    if ($age !== null && ($age < 1 || $age > 130)) {
        $errors[] = "Возраст должен быть от 1 до 130 лет.";
    }
    if ($weight !== null && ($weight < 1 || $weight > 150)) {
        $errors[] = "Вес должен быть от 1 до 150 кг.";
    }
    if ($height !== null && ($height < 50 || $height > 300)) {
        $errors[] = "Рост должен быть от 50 до 300 см.";
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
           
            $needs_calorie_update = false;
            if ($age !== null || $weight !== null || $height !== null || $gender !== null || $activity_level !== null) {
                $needs_calorie_update = true;
            }

            if ($needs_calorie_update) {
                include 'update_calories.php';
            }

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

$avatar = null;
$img_id = $user['img_id'];
if ($img_id) {
    $stmt = $conn->prepare("SELECT image FROM images WHERE img_id = ?");
    $stmt->bind_param("i", $img_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $avatar = "display_avatar.php?img_id=" . $img_id;
    } else {
        $avatar = "display_avatar.php?img_id=1";
    }

    $stmt->close();
} else {
    $avatar = "display_avatar.php?img_id=1";
}
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
            background-color: rgba(250, 242, 245, 0.8);
            padding: 20px;
        }
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgba(171, 5, 66, 0.6);
            border: 2px solid  rgba(171, 5, 66,1);
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
            background-color: rgba(250, 242, 245, 0.8);
            
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: rgba(171, 5, 66, 0.7);
            border: 2px solid  rgba(171, 5, 66,1);
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color:rgba(171, 5, 66, 1);
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
        .profile-avatar img {
            display: block;
            margin: 0 auto 20px;
            border-radius: 50%;
            border: 2px solid rgba(171, 5, 66, 1);
        }
        .avatar-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .avatar-gallery label {
            display: inline-block;
            cursor: pointer;
        }
        .avatar-gallery img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px solid rgba(171, 5, 66, 1);
            margin-bottom: 10px;
            transition: border-color 0.3s ease;
        }
        .avatar-gallery input[type="radio"] {
            display: none; 
        }
        .avatar-gallery input[type="radio"]:checked + img {
            border-color: #715ac8; 
        }
        .avatar-gallery label:hover img {
            border-color: #715ac8; 
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>Профиль пользователя</h2>
    <?php
if (isset($_SESSION['error_message'])) {
    echo "<div class='message error'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    echo "<div class='message success'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
    unset($_SESSION['success_message']);
}
?>

    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <div class="profile-avatar">
    <?php if ($avatar): ?>
        
        <img src="display_avatar.php?img_id=<?php echo htmlspecialchars($img_id); ?>" alt="Аватар" style="width:150px;height:150px;">
    <?php else: ?>
        <p>Аватар не установлен</p>
    <?php endif; ?>
</div>
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

        <button type="submit">Сохранить изменения</button>
    </form>

    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label>Загрузить новый аватар:</label>
        <input type="file" name="avatar" accept="image/*" required>
        <button type="submit">Загрузить</button>
    </form>

    <form action="select_avatar.php" method="post">
    <label>Выбрать существующий аватар:</label>
    <div class="avatar-gallery">
    <?php
    $stmt = $conn->prepare("SELECT img_id, image FROM images WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<label>";
            echo "<input type='radio' name='existing_avatar' value='" . $row['img_id'] . "'>";
            echo "<img src='display_avatar.php?img_id=" . $row['img_id'] . "' alt='Аватар' class='avatar-img'>";
            echo "</label>";
        }
    } else {
        echo "<p>Нет доступных аватаров</p>";
    }

    $stmt->close();
    ?>
</div>

    <button type="submit">Выбрать аватар</button>
</form>

</div>
</body>
</html>
