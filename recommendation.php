<?php
session_start();
require 'db.php';
include 'navbar.php';
// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch training recommendations based on trainer rating and user's execution count
$stmt = $conn->prepare("
    SELECT 
        u.username AS trainer_username,
        t.id AS training_id,
        t.name AS training_name,
        t.duration,
        t.calories_burned,
        COUNT(te.id) AS execution_count,
        (SELECT 
            ROUND((SUM(CASE WHEN t1.status = 'approved' THEN 1 ELSE 0 END) / 
            (COUNT(t1.id) + COUNT(t1.id))) * 4 + 
            (COUNT(tl.id) / (COUNT(t1.id) + COUNT(t1.id))) * 5 + 
            (IF(COUNT(te.id) > 0, 1, 0) * 1), 1)
        FROM training_programs t1
        LEFT JOIN training_likes tl ON t1.id = tl.training_id
        WHERE t1.created_by = u.id) AS trainer_rating
    FROM 
        training_programs t
    LEFT JOIN 
        training_executions te ON t.id = te.training_id AND te.user_id = ?
    LEFT JOIN 
        users u ON t.created_by = u.id
    GROUP BY 
        trainer_username, training_id
    ORDER BY 
        trainer_rating DESC, execution_count DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$recommendations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рекомендации тренировок</title>
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
        .container {
            width: 80%;
            max-width: 1200px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: rgba(139, 83, 179, 0.62);
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Рекомендации тренировок</h1>

    <?php if (!empty($recommendations)): ?>
        <table>
            <thead>
                <tr>
                    <th>Тренер</th>
                    <th>Тренировка</th>
                    <th>Длительность (мин)</th>
                    <th>Калории (ккал)</th>
                    <th>Выполнено раз</th>
                    <th>Рейтинг тренера</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recommendations as $recommendation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($recommendation['trainer_username']); ?></td>
                        <td>
                            <a href="training_details.php?id=<?php echo $recommendation['training_id']; ?>">
                                <?php echo htmlspecialchars($recommendation['training_name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($recommendation['duration']); ?></td>
                        <td><?php echo htmlspecialchars($recommendation['calories_burned']); ?></td>
                        <td><?php echo htmlspecialchars($recommendation['execution_count']); ?></td>
                        <td><?php echo htmlspecialchars($recommendation['trainer_rating']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Нет доступных рекомендаций для вас.</p>
    <?php endif; ?>
</div>

</body>
</html>
