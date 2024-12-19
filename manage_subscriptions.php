<?php
session_start();
require 'db.php';
include 'navbar.php';

if (!isset($_SESSION['user_id']) ) {
    echo "Доступ запрещен.";
    exit();
}

$user_id = $_SESSION['user_id'];
$sql_dietologist = "SELECT id FROM dietologists WHERE user_id = ?";
$stmt_dietologist = $conn->prepare($sql_dietologist);
$stmt_dietologist->bind_param("i", $user_id);
$stmt_dietologist->execute();
$result_dietologist = $stmt_dietologist->get_result();

if ($result_dietologist->num_rows === 0) {
    echo "Диетолог не найден.";
    exit();
}

$dietologist = $result_dietologist->fetch_assoc();
$dietologist_id = $dietologist['id'];

$sql = "
    SELECT s.id AS subscription_id, u.username AS user_name, s.status
    FROM subscriptions s
    JOIN users u ON s.user_id = u.id
    WHERE s.dietologist_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dietologist_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление подписками</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
   
body {
    margin-top: 20px;
        padding: 0;
        background-color: rgba(250, 242, 245, 1);
        display: flex;
        align-items: center;
        justify-content: center;
}

.container {
   background-color: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 80%;
        max-width: 900px;
        text-align: center;
        border: 2px solid rgba(171, 5, 66, 1);
}

h2 {
    font-size: 28px;
    margin-bottom: 30px;
    text-align: center;
}


table {
    width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
}
th, td {
        padding: 15px;
        text-align: center;
        border: 1px solid #ddd;
    }

    th {
        background-color: rgba(171, 5, 66, 0.7);
        color: white;
    }

    td {
        background-color: rgba(250, 242, 245, 0.7);
        font-size: 16px;
    }

    tr:nth-child(even) td {
        background-color: rgba(250, 242, 245,1);
    }

    tr:hover td {
        background-color:rgba(171, 5, 66, 0.3);
        cursor: pointer;
    }

    p {
        font-size: 18px;
        color: #666;
    }


.btn {
    padding: 5px 15px;
    margin: 5px;
    border-radius: 5px;
    font-size: 14px;
    text-decoration: none;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}


</style>
<body>
    <div class="container ">
        <h2 class="text-center">Запросы на подписку</h2>
        <?php if ($result->num_rows > 0): ?>
            <table >
                <thead>
                    <tr>
                        <th>Имя пользователя</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <a href="update_subscription.php?id=<?= $row['subscription_id'] ?>&action=approve" class="btn btn-success btn-sm">Одобрить</a>
                                    <a href="update_subscription.php?id=<?= $row['subscription_id'] ?>&action=reject" class="btn btn-danger btn-sm">Отклонить</a>
                                <?php else: ?>
                                    <span><?= htmlspecialchars($row['status'] === 'approved' ? 'Одобрено' : 'Отклонено') ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Нет запросов на подписку.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$stmt->close();
$stmt_dietologist->close();
$conn->close();
?>
