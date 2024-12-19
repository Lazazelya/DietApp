<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo 'User not logged in';
    exit();
}

$user_id = $_SESSION['user_id'];
$type = $_POST['type'];
$amount = (int)$_POST['amount']; 
$today = date('Y-m-d'); 

$stmt = $conn->prepare("SELECT WEmount, VEmount FROM user_activity WHERE user_id = ? AND action_date = ?");
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if ($row) {
    if ($type == 'water') {
        $stmt = $conn->prepare("UPDATE user_activity SET WEmount = WEmount + ? WHERE user_id = ? AND action_date = ?");
    } elseif ($type == 'fruit') {
        $stmt = $conn->prepare("UPDATE user_activity SET VEmount = VEmount + ? WHERE user_id = ? AND action_date = ?");
    }
    $stmt->bind_param("iis", $amount, $user_id, $today);
} else {
    
    if ($type == 'water') {
        $stmt = $conn->prepare("INSERT INTO user_activity (user_id, action_date, WEmount) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $today, $amount);
    } elseif ($type == 'fruit') {
        $stmt = $conn->prepare("INSERT INTO user_activity (user_id, action_date, VEmount) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $today, $amount);
    }
}

if (!$stmt->execute()) {
    echo "Ошибка: " . $stmt->error;
}
$stmt->close();
?>
