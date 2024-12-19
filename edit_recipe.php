<?php
session_start();
require 'db.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role_id);
$stmt->fetch();
$stmt->close();

if ($role_id != 3) {
    echo "Доступ запрещен!";
    exit();
}

if (!isset($_GET['recipe_id'])) {
    echo "ID рецепта не передан.";
    exit();
}

$recipe_id = intval($_GET['recipe_id']);

$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
$recipe = $result->fetch_assoc();
$stmt->close();

if (!$recipe) {
    echo "Рецепт не найден.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать рецепт</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: rgba(250, 242, 245, 1);
            margin-top: 5px;
        }

        .edit-form {
            border: 2px solid  rgba(171, 5, 66,1);
            margin: 20px auto;
            padding: 20px;
            max-width: 600px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .edit-form label {
            display: block;
            margin: 10px 0 5px;
            text-align: left;
        }

        .edit-form input,
        .edit-form textarea,
        .edit-form button {
            width: 100%;
            padding: 10px;
            background-color: rgba(250, 242, 245, 0.8);
            border: 2px solid  rgba(171, 5, 66,1);
            margin-bottom: 15px;
          
            border-radius: 4px;
        }

        .edit-form button {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: rgba(171, 5, 66, 0.6);
            border: 2px solid  rgba(171, 5, 66,1);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-form button:hover {
            background-color:rgba(171, 5, 66, 1);
            
        } 
        
        .message {
            background-color: #f4f4f4;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
            text-align: center;
        }

        .image-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
        }

        .image-controls button {
            padding: 5px 10px;
            font-size: 14px;
            background-color: rgba(171, 5, 66, 0.6);
            border: 2px solid  rgba(171, 5, 66,1);
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
        }

        .image-controls button:hover {
            background-color: #ddd;
        }

        .image-controls .delete-button {
            background-color: #ff6b6b;
            color: white;
            border: none;
        }

        .image-controls .delete-button:hover {
            background-color: #ff4c4c;
        }

        .image-controls .replace-button {
            background-color: rgba(171, 5, 66, 0.6);
            border: 2px solid  rgba(171, 5, 66,1);
            color: white;
            border: none;
        }

        .image-controls .replace-button:hover {
            background-color: #4caf50;
        }
    </style>
</head>
<body>

<h2>Редактировать рецепт</h2>

<?php

if (isset($_SESSION['message'])): ?>
    <div class="message">
        <?= htmlspecialchars($_SESSION['message']); ?>
    </div>
    <?php unset($_SESSION['message']);  ?>
<?php endif; ?>

<div class="edit-form">
    <form action="update_recipe.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="recipe_id" value="<?= htmlspecialchars($recipe['id']) ?>">

        <label for="title">Название рецепта:</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($recipe['title']) ?>" required>

        <label for="description">Описание:</label>
        <textarea name="description" id="description" required><?= htmlspecialchars($recipe['description']) ?></textarea>

        <label for="calories">Калории:</label>
        <input type="number" name="calories" min="10" max="1000" step="1" id="calories" value="<?= htmlspecialchars($recipe['calories']) ?>" required>

        <label for="protein">Белки:</label>
        <input type="number" name="protein" min="1" max="100" step="1" id="protein" value="<?= htmlspecialchars($recipe['protein']) ?>" required>

        <label for="fat">Жиры:</label>
        <input type="number" min="1" max="100" step="1" name="fat" id="fat" value="<?= htmlspecialchars($recipe['fat']) ?>" required>

        <label for="carbs">Углеводы:</label>
        <input type="number" min="1" max="100" step="1" name="carbs" id="carbs" value="<?= htmlspecialchars($recipe['carbs']) ?>" required>

        <label>Текущее изображение:</label>
        <?php if (!empty($recipe['image_path'])): ?>
            <div>
                <img src="<?= htmlspecialchars($recipe['image_path']); ?>" alt="Изображение рецепта" style="max-width: 200px;">
                <div class="image-controls">
                      <button type="button" id="replace-image" class="replace-button">Заменить изображение</button>
                </div>
            </div>
        <?php else: ?>
            <p>Изображение отсутствует</p>
            <label for="image">Добавить изображение:</label>
            <input type="file" name="new_image" id="image"><br>
        <?php endif; ?>

        <div id="new-image-field" style="display: none;">
            <label for="new-image">Новое изображение:</label>
            <input type="file" name="new_image" id="new-image">
        </div>

        <button type="submit" name="update">Сохранить изменения</button>
        <button type="button" id="delete-recipe" class="delete-button">Удалить рецепт</button>
    </form>
</div>

<script>
   
const replaceButton = document.getElementById('replace-image');
const newImageField = document.getElementById('new-image-field');


replaceButton?.addEventListener('click', function() {
    newImageField.style.display = 'block';
});

</script>
<script>
    document.getElementById('delete-recipe')?.addEventListener('click', function() {
        if (confirm('Вы уверены, что хотите удалить этот рецепт? Это действие нельзя отменить.')) {
            fetch('delete_recipe.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    recipe_id: <?= $recipe['id']; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = 'main.php'; 
                } else {
                    alert('Ошибка при удалении рецепта: ' + data.message);
                }
            })
            .catch(error => {
                alert('Произошла ошибка при отправке запроса.');
            });
        }
    });
</script>

</body>
</html>