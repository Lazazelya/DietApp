<?php
session_start();
require 'db.php';

$response = ['success' => false, 'message' => '', 'new_recipe_html' => ''];

function respondWithError($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image']; 

    if (!file_exists($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        respondWithError("Файл не найден. Возможно, он был удален или не загружен корректно.");
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        respondWithError("Ошибка загрузки файла.");
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        respondWithError("Ограничение по размеру файла 5 МБ");
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

   
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        respondWithError("Недопустимый тип файла. Допустимы только файлы с расширением JPG, JPEG, PNG.");
    }
    

    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        respondWithError("Изображение повреждено. Попробуйте загрузить другой файл.");
    }

   
  
  
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        respondWithError("Не удалось создать папку для загрузки файлов.");
    }
    if (!is_writable($uploadDir)) {
        respondWithError("Сервер не имеет прав на запись в директорию.");
    }

  
    $fileName = uniqid() . '_' . basename($file['name']);
    $filePath = $uploadDir . $fileName;


    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        respondWithError("Ошибка при перемещении загруженного файла.");
    }

   
    $title = $_POST['title'];
    $description = $_POST['description'];
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $fat = $_POST['fat'];
    $carbs = $_POST['carbs'];

    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $stmt = $conn->prepare("INSERT INTO recipes (title, description, calories, protein, fat, carbs, image_path, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiisi", $title, $description, $calories, $protein, $fat, $carbs, $filePath, $userId);
    $stmt->execute();

    $recipeId = $stmt->insert_id;
    $stmt->close();

    $newRecipeHtml = '
    <div class="recipe">
        <img src="' . htmlspecialchars($filePath) . '" alt="' . htmlspecialchars($title) . '">
        <div class="recipe-content">
            <h3>' . htmlspecialchars($title) . '</h3>
            <ul>
                <li>Калории: ' . htmlspecialchars($calories) . ' ккал</li>
                <li>Белки: ' . htmlspecialchars($protein) . ' г</li>
                <li>Жиры: ' . htmlspecialchars($fat) . ' г</li>
                <li>Углеводы: ' . htmlspecialchars($carbs) . ' г</li>
            </ul>
            <p>' . htmlspecialchars($description) . '</p>
        </div>';

    if ($_SESSION['role_id'] == 3) {
        $newRecipeHtml .= '
        <div class="edit-button">
            <form action="edit_recipe.php" method="GET">
                <input type="hidden" name="recipe_id" value="' . $recipeId . '">
                <button type="submit">Редактировать</button>
            </form>
        </div>';
    }

    $newRecipeHtml .= '</div>';

    $response['success'] = true;
    $response['message'] = 'Рецепт добавлен!';
    $response['new_recipe_html'] = $newRecipeHtml;

} else {
    $response['message'] = 'Недопустимый запрос.';
}

echo json_encode($response);
