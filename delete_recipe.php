<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Не авторизован']);
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role_id);
$stmt->fetch();
$stmt->close();

if ($role_id != 2) {
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit();
}


$data = json_decode(file_get_contents('php://input'), true);
$recipe_id = $data['recipe_id'];


$stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Рецепт успешно удален']);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка при удалении рецепта']);
}
$stmt->close();
?>
