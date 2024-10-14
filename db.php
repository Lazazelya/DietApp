<?php

$servername = "localhost";
$username = "Health";
$password = "LNOM";
$dbname = "HealthWeb2";

// Функция для обработки ошибок базы данных
if (!function_exists('handleDbError')) {
    function handleDbError($message) {
        error_log($message, 3, 'db_errors.log'); // Запись ошибки в файл
        die("Произошла ошибка при подключении к базе данных. Попробуйте позже.");
    }
}

try {
    // Включение отчета об ошибках
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    // Создание подключения
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Установка кодировки
    $conn->set_charset("utf8");

} catch (mysqli_sql_exception $e) {
    handleDbError($e->getMessage());
}

// Ваша дальнейшая логика работы с базой данных
?>
