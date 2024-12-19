<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) ) {
    echo "Доступ запрещен.";
    exit();
}

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    echo "Некорректный запрос.";
    exit();
}

$subscription_id = intval($_GET['id']);
$action = $_GET['action'];


if ($action !== 'approve' && $action !== 'reject') {
    echo "Некорректное действие.";
    exit();
}

$status = $action === 'approve' ? 'approved' : 'rejected';
$sql = "UPDATE subscriptions SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $subscription_id);

if ($stmt->execute()) {
    echo "Статус подписки успешно обновлен.";
} else {
    echo "Произошла ошибка при обновлении статуса.";
}

$stmt->close();
$conn->close();
?>
