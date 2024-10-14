<?php
session_start();
require 'db.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Проверка роли администратора
$stmt = $conn->prepare("SELECT role_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$is_admin = $user['role_id'] == 2; // Предположим, что 1 — это роль администратора

if (!$is_admin) {
    echo "У вас нет прав для доступа к этой странице.";
    exit();
}

// Получаем программы со статусом 'pending'
$stmt = $conn->prepare("SELECT id, name, description, created_by, training_type_id, duration, calories_burned FROM training_programs WHERE status = 'pending'");
$stmt->execute();
$programs_result = $stmt->get_result();
$programs = $programs_result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $program_id = $_POST['program_id'];
    $action = $_POST['action'];  // 'approve' или 'reject'

    if ($action == 'approve') {
        $stmt = $conn->prepare("UPDATE training_programs SET status = 'approved' WHERE id = ?");
    } else {
        $stmt = $conn->prepare("UPDATE training_programs SET status = 'rejected' WHERE id = ?");
    }

    $stmt->bind_param("i", $program_id);
    if ($stmt->execute()) {
        $success_message = "Программа успешно " . ($action == 'approve' ? "одобрена" : "отклонена") . ".";
    } else {
        $error_message = "Ошибка при обновлении программы: " . $stmt->error;
    }
    $stmt->close();
    header("Location: pending.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рассмотрение программ тренировок</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #bdacbb;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        button {
            padding: 5px 10px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button.reject {
            background-color: #d9534f;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Рассмотрение программ тренировок</h1>

    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <table>
        <thead>
        <tr>
            <th>Название программы</th>
            <th>Описание</th>
            <th>Продолжительность (мин)</th>
            <th>Калории</th>
            <th>Действие</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($programs) > 0): ?>
            <?php foreach ($programs as $program): ?>
                <tr>
                    <td><?php echo htmlspecialchars($program['name']); ?></td>
                    <td><?php echo htmlspecialchars($program['description']); ?></td>
                    <td><?php echo htmlspecialchars($program['duration']); ?> мин</td>
                    <td><?php echo htmlspecialchars($program['calories_burned']); ?> ккал</td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="program_id" value="<?php echo $program['id']; ?>">
                            <button type="submit" name="action" value="approve">Одобрить</button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="program_id" value="<?php echo $program['id']; ?>">
                            <button type="submit" name="action" value="reject" class="reject">Отклонить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Нет программ на рассмотрении.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
