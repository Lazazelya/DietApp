<?php
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
$allowedMimeTypes = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
];
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['file'];

    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileExtension = strtolower($fileExtension); // все маленькии
    if (!in_array($fileExtension, $allowedExtensions)) {
        die('Ошибка: Недопустимое расширение файла!');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    $uploadDir = 'uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $fileName = basename($file['name']);
    $destination = $uploadDir . $fileName;
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        echo 'Файл успешно загружен: ' . $fileName;
    } else {
        echo 'Ошибка при сохранении файла.';
    }
} else {
    echo 'Ошибка загрузки файла.';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка файла</title>
</head>
<body>
<h1>Допустимые типы:jpg,jpeg,png,gif,pdf,doc,docx.</h1>
<form action="upload.php" method="POST" enctype="multipart/form-data">
    <label for="file">Выберите файл для загрузки:</label>
    <input type="file" name="file" id="file" required>
    <button type="submit">Загрузить</button>
</form>
</body>
</html>
