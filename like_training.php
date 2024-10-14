<?php
session_start();
require 'db.php';

// Проверяем, авторизован ли пользователь и передан ли идентификатор тренировки
if (!isset($_SESSION['user_id']) || !isset($_POST['training_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$training_id = $_POST['training_id'];

// Проверяем, ставил ли пользователь уже лайк
$stmt = $conn->prepare("SELECT * FROM training_likes WHERE user_id = ? AND training_id = ?");
$stmt->bind_param("ii", $user_id, $training_id);
$stmt->execute();
$like = $stmt->get_result()->fetch_assoc();

if ($like) {
    // Если лайк уже существует, удалим его
    $stmt = $conn->prepare("DELETE FROM training_likes WHERE user_id = ? AND training_id = ?");
    $stmt->bind_param("ii", $user_id, $training_id);
    $stmt->execute();

    // Устанавливаем сообщение об удалении лайка
    $_SESSION['message'] = "Лайк удален.";
} else {
    // Если лайка нет, добавим его
    $stmt = $conn->prepare("INSERT INTO training_likes (user_id, training_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $training_id);
    $stmt->execute();

    // Устанавливаем сообщение о добавлении лайка
    $_SESSION['message'] = "Лайк добавлен.";
}

// Перенаправляем пользователя обратно на страницу деталей тренировки
header("Location: training_details.php?id=" . $training_id);
exit();
?>
