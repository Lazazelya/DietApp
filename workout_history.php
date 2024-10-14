<?php
session_start();
require 'db.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT te.execution_date, tp.name, tp.description, tp.duration, tp.calories_burned 
    FROM training_executions te
    JOIN training_programs tp ON te.training_id = tp.id
    WHERE te.user_id = ?
    ORDER BY te.execution_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history_result = $stmt->get_result();
$workout_history = $history_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История выполненных тренировок</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #bdacbb;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>История выполненных тренировок</h1>

    <table>
        <tr>
            <th>Дата выполнения</th>
            <th>Название тренировки</th>

            <th>Длительность (мин)</th>
            <th>Сожженные калории</th>
        </tr>
        <?php if (count($workout_history) > 0): ?>
            <?php foreach ($workout_history as $workout): ?>
                <tr>
                    <td><?php echo htmlspecialchars($workout['execution_date']); ?></td>
                    <td><?php echo htmlspecialchars($workout['name']); ?></td>

                    <td><?php echo htmlspecialchars($workout['duration']); ?></td>
                    <td><?php echo htmlspecialchars($workout['calories_burned']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">История тренировок пуста.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
