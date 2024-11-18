<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = (int) $_POST['request_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $new_status = 'approved';
    } elseif ($action === 'reject') {
        $new_status = 'rejected';
    } elseif ($action === 'in_process') { // Действие "Редактировать"
        $new_status = 'pending'; // Статус меняется на "Ожидает"
    } else {
        $_SESSION['message'] = "Неверное действие.";
        header("Location: training_requests.php");
        exit();
    }

    // Обновление статуса в таблице
    $stmt = $conn->prepare("UPDATE training_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $request_id);
    $stmt->execute();

    $_SESSION['message'] = "Запрос успешно обновлен.";
    header("Location: training_requests.php");
    exit();
}

    

    // Если запрос одобрен, обновить сожженные калории
    if ($new_status === 'approved') {
        $stmt = $conn->prepare("
            SELECT tr.user_id, tr.training_id, t.calories_burned 
            FROM training_requests tr
            JOIN training_programs t ON tr.training_id = t.id
            WHERE tr.id = ?
        ");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();

        $user_id = $request['user_id'];
        $calories_burned = $request['calories_burned'];

        // Получить текущие калории из calorie_tracker
        $stmt = $conn->prepare("SELECT calories_burned FROM calorie_tracker WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $current_burned_calories = $result ? $result['calories_burned'] : 0;

        // Вычислить новое значение калорий
        $new_burned_calories = $current_burned_calories + $calories_burned;

        // Обновить или вставить данные в calorie_tracker
        if ($result) {
            $stmt = $conn->prepare("UPDATE calorie_tracker SET calories_burned = ? WHERE user_id = ?");
            $stmt->bind_param("ii", $new_burned_calories, $user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO calorie_tracker (user_id, calories_burned) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $new_burned_calories);
        }
        $stmt->execute();
    }

    $_SESSION['message'] = "Запрос успешно обработан.";
    header("Location: training_requests.php");
    exit();

?>
