<?php
session_start();
require 'db.php';
include 'navbar.php';

// Проверка наличия идентификатора тренировки и сессии пользователя
if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$training_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Получение информации о пользователе
$stmt = $conn->prepare("SELECT role_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_info = $stmt->get_result()->fetch_assoc();
$user_role = $user_info['role_id'];

// Получение информации о тренировке
$stmt = $conn->prepare("
    SELECT
        t.id,  -- Добавляем id тренировки для использования в кнопке лайка
        t.name,
        t.description,
        t.duration,
        t.calories_burned,
        u.username AS trainer_name,
        t.created_by,
        COUNT(tl.id) AS like_count  -- Получаем количество лайков
    FROM training_programs t
    JOIN users u ON t.created_by = u.id
    LEFT JOIN training_likes tl ON t.id = tl.training_id  -- Подключаем лайки
    WHERE t.id = ?
    GROUP BY t.id  -- Группируем по id тренировки
");
$stmt->bind_param("i", $training_id);
$stmt->execute();
$program = $stmt->get_result()->fetch_assoc();

// Проверяем, является ли текущий пользователь тренером или он создал тренировку
$is_trainer = $program['created_by'] == $user_id || $user_role == 2; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complete_training']) && !$is_trainer) {
    $calories_burned = $program['calories_burned'];
   
    // Запись о выполнении тренировки
    $stmt = $conn->prepare("INSERT INTO training_executions (user_id, training_id, execution_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $user_id, $training_id);
    $stmt->execute();

    // Получаем текущие сожженные калории
    $stmt = $conn->prepare("SELECT calories_burned FROM calorie_tracker WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $current_burned_calories = $result ? $result['calories_burned'] : 0;

    $new_burned_calories = $current_burned_calories + $calories_burned;

    // Обновляем или создаем запись о сожженных калориях
    if ($result) {
        $stmt = $conn->prepare("UPDATE calorie_tracker SET calories_burned = ? WHERE user_id = ?");
        $stmt->bind_param("ii", $new_burned_calories, $user_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO calorie_tracker (user_id, calories_burned) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $new_burned_calories);
    }
    $stmt->execute();
    $training_completed = true;  // Успешно выполненная тренировка
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация о тренировке</title>
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

        h1 {
            margin-top: 50px;
            text-align: center;
        }

        .container {
            width: 80%;
            max-width: 800px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            text-align: center;
        }

        p {
            margin: 10px 0;
            font-size: 16px;
        }

        .duration, .calories {
            font-style: italic;
            color: #666;
        }

        button {
            padding: 10px 20px;
            background-color: rgba(139, 83, 179, 0.62);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        button:hover {
            background-color: #715ac8;
        }

        .message {
            margin-top: 20px;
            font-size: 18px;
            color: #333;
        }

        .trainer-info {
            font-size: 0.9em;
            font-style: italic;
            color: #888;
        }
    </style>
</head>
<body>
<div class="container">
    <h1><?php echo htmlspecialchars($program['name']); ?> <span class="trainer-info">(Тренер: <?php echo htmlspecialchars($program['trainer_name']); ?>)</span></h1>
    <p><?php echo htmlspecialchars($program['description']); ?></p>
    <p class="duration">Длительность: <?php echo htmlspecialchars($program['duration']); ?> минут</p>
    <p class="calories">Калории: <?php echo htmlspecialchars($program['calories_burned']); ?> ккал</p>

    <?php if (!$is_trainer): // Только пользователи могут ставить лайки ?>
        <form method="POST" action="like_training.php">
            <input type="hidden" name="training_id" value="<?php echo htmlspecialchars($program['id']); ?>">
            <button type="submit" class="btn">
                Лайк (<?php echo isset($program['like_count']) ? htmlspecialchars($program['like_count']) : 0; ?>)
            </button>
        </form>
    <?php endif; ?>

    <form method="POST" action="">
        <?php if ($is_trainer): ?>
            <button type="button" onclick="window.location.href='edit_training.php?id=<?php echo $training_id; ?>'">Редактировать тренировку</button>
        <?php else: ?>
            <button type="submit" name="complete_training">Выполнить тренировку</button>
        <?php endif; ?>
    </form>

    <?php if (isset($training_completed) && $training_completed): ?>
        <p class="message">Тренировка выполнена! Калории пересчитаны.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['message'])): ?>
        <p class="message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
        <?php unset($_SESSION['message']); // Удаляем сообщение из сессии после отображения ?>
    <?php endif; ?>
</div>

<?php if (isset($training_completed) && $training_completed): ?>
    <script>
        setTimeout(function() {
            window.location.href = 'train_choose.php';
        }, 2000);
    </script>
<?php endif; ?>

</body>
</html>
