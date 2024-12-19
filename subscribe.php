<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "Вы должны быть авторизованы для оформления подписки.";
    exit();
}

if (!isset($_GET['dietologist_id'])) {
    echo "Не указан идентификатор диетолога.";
    exit();
}

$dietologist_id = intval($_GET['dietologist_id']);
$user_id = $_SESSION['user_id'];

$sql_check_dietologist = "SELECT id FROM dietologists WHERE id = ?";
$stmt_check_dietologist = $conn->prepare($sql_check_dietologist);
$stmt_check_dietologist->bind_param("i", $dietologist_id);
$stmt_check_dietologist->execute();
$result_check_dietologist = $stmt_check_dietologist->get_result();

if ($result_check_dietologist->num_rows === 0) {
    echo "Диетолог с таким ID не найден.";
    exit();
}

$stmt_check_dietologist->close();

$sql_check = "SELECT * FROM subscriptions WHERE user_id = ? AND dietologist_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $dietologist_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "Вы уже отправили запрос на подписку этому диетологу.";
    exit();
}

$sql_insert = "INSERT INTO subscriptions (user_id, dietologist_id, status) VALUES (?, ?, 'pending')";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("ii", $user_id, $dietologist_id);

if ($stmt_insert->execute()) {
    echo "Запрос на подписку успешно отправлен.";
} else {
    echo "Произошла ошибка при отправке запроса.";
}

$stmt_check->close();
$stmt_insert->close();
$conn->close();
?>
