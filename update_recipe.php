<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Нет доступа']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['recipe_id'])) {
        $recipe_id = intval($_POST['recipe_id']);
    } else {
        echo "ID рецепта не передан.";
        exit();
    }


    $title = $_POST['title'];
    $description = $_POST['description'];
    $calories = $_POST['calories'];
    $protein = $_POST['protein'];
    $fat = $_POST['fat'];
    $carbs = $_POST['carbs'];

    $stmt = $conn->prepare("UPDATE recipes SET title = ?, description = ?, calories = ?, protein = ?, fat = ?, carbs = ? WHERE id = ?");
    $stmt->bind_param("ssiiiii", $title, $description, $calories, $protein, $fat, $carbs, $recipe_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Рецепт успешно обновлен!";
    } else {
        $_SESSION['message'] = "Ошибка обновления рецепта.";
    }

    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == UPLOAD_ERR_OK) {
    
        $upload_dir = 'uploads/';
        if (!is_writable($upload_dir)) {
            $_SESSION['message'] = 'Нет доступа к папке для сохранения изображений. Пожалуйста, проверьте права доступа.';
            header("Location: edit_recipe.php?recipe_id=" . $recipe_id);
            exit();
        }

        $file = $_FILES['new_image'];
        $allowed_types = ['image/jpeg', 'image/png'];

        
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['message'] = 'Неверный тип файла. Разрешены только JPEG и PNG.';
            header("Location: edit_recipe.php?recipe_id=" . $recipe_id);
            exit();
        }

       
        $new_image_path = $upload_dir . basename($file['name']);
        move_uploaded_file($file['tmp_name'], $new_image_path);
        $image_info = @getimagesize($new_image_path);
        if ($image_info === false) {
            $_SESSION['message'] = 'Файл поврежден.';
            header("Location: edit_recipe.php?recipe_id=" . $recipe_id);
            exit();
        }

    
        $stmt = $conn->prepare("SELECT image_path FROM recipes WHERE id = ?");
        $stmt->bind_param("i", $recipe_id);
        $stmt->execute();
        $stmt->bind_result($image_path);
        $stmt->fetch();
        $stmt->close();

        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path); 
        }

        $stmt = $conn->prepare("UPDATE recipes SET image_path = ? WHERE id = ?");
        $stmt->bind_param("si", $new_image_path, $recipe_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Изображение успешно обновлено!";
        } else {
            $_SESSION['message'] = "Ошибка обновления изображения.";
        }
    }

    
    header("Location: edit_recipe.php?recipe_id=" . $recipe_id);
    exit();
}


if (isset($_POST['delete_image'])) {
    $stmt = $conn->prepare("SELECT image_path FROM recipes WHERE id = ?");
    $stmt->bind_param("i", $_POST['recipe_id']);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    if (!empty($image_path) && file_exists($image_path)) {
        unlink($image_path); 
    }

    
    $stmt = $conn->prepare("UPDATE recipes SET image_path = NULL WHERE id = ?");
    $stmt->bind_param("i", $_POST['recipe_id']);
    $stmt->execute();

    echo json_encode(['success' => true]);
    exit();
}
