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
 

$uploads_dir = 'uploads';
if (!is_dir($uploads_dir) || !is_readable($uploads_dir) || !is_writable($uploads_dir)) {
   
    $file_error_message = "Папка 'uploads' недоступна. Для данного рецепта невозможно загрузить фото.";
} else {
   
    $file_error_message = null;
}

function displayMessage($type, $message) {
    echo "<div style='background-color: " . ($type === 'error' ? '#ffdddd' : '#ddffdd') . "; border: 1px solid " . ($type === 'error' ? '#ff8888' : '#88ff88') . "; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
    echo htmlspecialchars($message);
    echo "</div>";
}


if ($role_id != 1) {
    
    if (isset($_SESSION['error_message'])) {
        displayMessage('error', $_SESSION['error_message']);
        unset($_SESSION['error_message']);
    }

if (isset($_SESSION['success_message'])) {
    displayMessage('success', $_SESSION['success_message']);
    unset($_SESSION['success_message']);
}
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <style>
       
        body {
            font-family: Arial, sans-serif;
            background-color: rgba(250, 242, 245, 0.8);
            text-align: center;
           
            margin-top: 30px;
            padding: 0;
        }

        h1, h2, h3 {
            margin-top: 30px;
            color: #333;
        }

        .recipe {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            padding: 20px;
            max-width: 800px;
            background-color: rgba(171, 5, 66, 0.6);
            border: 2px solid  rgba(171, 5, 66,1);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .recipe img {
            max-width: 150px;
            max-height: 150px;
            margin-right: 20px;
            border-radius: 8px;
            border: 2px solid  rgba(171, 5, 66,1);
        }

        .recipe-content {
            text-align: left;
        }

        .add-recipe-form {
            border: 2px solid  rgba(171, 5, 66,1);
            margin: 20px auto;
            padding: 20px;
            max-width: 600px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .add-recipe-form form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .add-recipe-form label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .add-recipe-form input, 
        .add-recipe-form textarea, 
        .add-recipe-form button {
            width: 100%;
            padding: 10px;
            border: 2px solid  rgba(171, 5, 66,1);
            background-color: rgba(250, 242, 245, 0.8);
          
            border-radius: 5px;
            font-size: 14px;
        }

        .add-recipe-form textarea {
            resize: vertical; 
        }

        .add-recipe-form button {
            background-color: rgba(171, 5, 66, 0.6);
            border: 2px solid  rgba(171, 5, 66,1);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .add-recipe-form button:hover {
            background-color:rgba(171, 5, 66, 1);
            
        }
    </style>
</head>
<body>

<h1>Добро пожаловать на главную страницу!</h1>

<?php
if (isset($_SESSION['user_id'])) {
    echo "<p>Привет, пользователь! Добро пожаловать на наш сайт!</p>";
} else {
    echo "<p>Пожалуйста, войдите или зарегистрируйтесь, чтобы получить доступ к дополнительным функциям.</p>";
}
?>

<h2>Простые рецепты с КБЖУ</h2>



<div id="recipes-list">
    <?php
   
    $result = $conn->query("SELECT COUNT(*) AS count FROM recipes");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
      
        $default_title = "Овсянка с бананом и медом";
        $default_description = "Ингредиенты: овсянка (50г), банан (1 шт.), мед (1 ст. л.), молоко (150 мл).";
        $default_calories = 350;
        $default_protein = 10;
        $default_fat = 5;
        $default_carbs = 65;
        $default_image_path = "stat/Stat.jpg";

        $stmt = $conn->prepare("INSERT INTO recipes (title, description, calories, protein, fat, carbs, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiiis", $default_title, $default_description, $default_calories, $default_protein, $default_fat, $default_carbs, $default_image_path);
        $stmt->execute();
        $stmt->close();
    }

    if ($role_id == 3) {
        $stmt = $conn->prepare("SELECT * FROM recipes WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
    } elseif($user_id= 2){
    $stmt = $conn->prepare("SELECT * FROM recipes where user_id=3");
    } else{
        $stmt = $conn->prepare("SELECT * FROM recipes"); 
    }

    $stmt->execute();
    $result = $stmt->get_result();


    while ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'];
        $file_error_message = null;
        $uploads_dir = 'uploads';  

        if (empty($image_path) || strpos($image_path, $uploads_dir) === false) {
            
            echo "
    <div class='recipe'>
        <img src='" . htmlspecialchars($image_path) . "' alt='" . htmlspecialchars($row['title']) . "'>
        <div class='recipe-content'>
            <h3>" . htmlspecialchars($row['title']) . "</h3>
            <ul>
                <li>Калории: " . htmlspecialchars($row['calories']) . " ккал</li>
                <li>Белки: " . htmlspecialchars($row['protein']) . " г</li>
                <li>Жиры: " . htmlspecialchars($row['fat']) . " г</li>
                <li>Углеводы: " . htmlspecialchars($row['carbs']) . " г</li>
            </ul>
            <p>" . htmlspecialchars($row['description']) . "</p>
        </div>";
          
            echo "</div>";
            continue;
        }

        
        if (!is_dir($uploads_dir) || !is_readable($uploads_dir) || !is_writable($uploads_dir)) {
          
            $file_error_message = "Папка 'uploads' недоступна. Для данного рецепта невозможно загрузить фото.";
        } else {
            
            if (!file_exists($image_path)) {
                $file_error_message = "Файл изображения для рецепта '" . htmlspecialchars($row['title']) . "' отсутствует.";
            } else {
               
                if (!is_readable($image_path)) {
                    $file_error_message = "Не удается прочитать файл изображения для рецепта '" . htmlspecialchars($row['title']) . "'.";
                } else {
                   
                    $image_info = @getimagesize($image_path); 
                    if (!$image_info) {
                        $file_error_message = "Файл изображения для рецепта '" . htmlspecialchars($row['title']) . "' поврежден или не является изображением.";
                    } else {
                      
                        $mime_type = mime_content_type($image_path);
                        if (strpos($mime_type, 'image') === false) {
                            $file_error_message = "Файл изображения для рецепта '" . htmlspecialchars($row['title']) . "' не является изображением.";
                        } else {
                           
                            $file_error_message = null;
                        }
                    }
                }
            }
        }


      
       if ($file_error_message ) {
           echo "<div style='background-color: #ffdddd; border: 1px solid #ff8888; padding: 10px; margin: 10px 0; border-radius: 5px;'>";
           echo htmlspecialchars($file_error_message);
           echo "</div>";
           continue;
       }


    
    echo "
    <div class='recipe'>
        <img src='" . htmlspecialchars($image_path) . "' alt='" . htmlspecialchars($row['title']) . "'>
        <div class='recipe-content'>
            <h3>" . htmlspecialchars($row['title']) . "</h3>
            <ul>
                <li>Калории: " . htmlspecialchars($row['calories']) . " ккал</li>
                <li>Белки: " . htmlspecialchars($row['protein']) . " г</li>
                <li>Жиры: " . htmlspecialchars($row['fat']) . " г</li>
                <li>Углеводы: " . htmlspecialchars($row['carbs']) . " г</li>
            </ul>
            <p>" . htmlspecialchars($row['description']) . "</p>
        </div>";

    
    if ($role_id == 3) {
        echo "
        <div class='edit-button'>
            <form action='edit_recipe.php' method='GET'>
                <input type='hidden' name='recipe_id' value='" . htmlspecialchars($row['id']) . "'>
                <button type='submit'>Редактировать</button>
            </form>
        </div>
        ";
    }

    echo "</div>";
}

    ?>
</>

<div id="message-container"></div>
<?php
if ($role_id == 3) {
    ?>
    <div class="add-recipe-form">
        <h3>Добавить новый рецепт</h3>
        <form id="recipe-form" action="add_recipe.php" method="POST" enctype="multipart/form-data">
            <label for="title">Название рецепта:</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Описание:</label>
            <textarea name="description" id="description" required></textarea>

            <label for="calories">Калории:</label>
            <input type="number" min="10" max="1000" step="1" name="calories" id="calories" required>

            <label for="protein">Белки:</label>
            <input type="number" step="1" min="1" max="100" name="protein" id="protein" required>

            <label for="fat">Жиры:</label>
            <input type="number" step="1" min="1" max="100" name="fat" id="fat" required>

            <label for="carbs">Углеводы:</label>
            <input type="number" step="1" min="1" max="100" name="carbs" id="carbs" required>

            <label for="image">Изображение (PNG, JPG, JPEG):</label>
            <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png" required>

            <button type="submit">Добавить рецепт</button>
        </form>
    </div>

    <script>
document.getElementById("recipe-form").addEventListener("submit", function(event) {
    event.preventDefault(); 

    var formData = new FormData(this); 

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_recipe.php", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText); 
            if (response.success) {
                
                document.getElementById("message-container").innerHTML = '<div style="background-color: #ddffdd; padding: 10px; border-radius: 5px;">' + response.message + '</div>';
                
                
                document.getElementById("recipes-list").innerHTML += response.new_recipe_html;
                document.getElementById("recipe-form").reset(); 
            } else {
                document.getElementById("message-container").innerHTML = '<div style="background-color: #ffdddd; padding: 10px; border-radius: 5px;">' + response.message + '</div>';
            }
        } else {
            alert("Ошибка при добавлении рецепта.");
        }
    };
    xhr.send(formData); 
});
</script>

    <?php
}
?>

</body>
</html>
