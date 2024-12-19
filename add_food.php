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
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$is_admin = $user['role_id'] == 2;

if (!$is_admin) {
    echo "У вас нет прав для доступа к этой странице.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $calorie_per_100 = $_POST['calorie_per_100'];
    $proteins = $_POST['proteins'];
    $fats = $_POST['fats'];
    $carbohydrates = $_POST['carbohydrates'];

    if (empty($name)) {
        $error_message = "Название продукта не может быть пустым!";
    } else {
        $stmt = $conn->prepare("SELECT food_id FROM food_items WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Продукт с таким названием уже существует!";
        } else {
            $stmt = $conn->prepare("INSERT INTO food_items (name, calorie_per_100, proteins, fats, carbohydrates) VALUES (?, ?, ?, ?, ?)");

            $stmt->bind_param("sdddd", $name, $calorie_per_100, $proteins, $fats, $carbohydrates);

            if ($stmt->execute()) {
                $success_message = "Продукт успешно добавлен!";
            } else {
                $error_message = "Ошибка при добавлении продукта: " . $stmt->error;
            }
        }

        $stmt->close();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $product_id_to_delete = $_POST['product_id_to_delete'];

    if (!empty($product_id_to_delete)) {
        $stmt = $conn->prepare("DELETE FROM food_items WHERE food_id = ?");
        $stmt->bind_param("i", $product_id_to_delete);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $success_message = "Продукт успешно удален!";
        } else {
            $error_message = "Ошибка при удалении продукта: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = "Ошибка: Не выбран продукт для удаления.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление продуктами</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color:rgba(250, 242, 245, 1);
        }

        .container {
            width: 80%;
            margin: 0 auto;
           
            padding: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: rgba(171, 5, 66, 0.6);
            border: 2px solid  rgba(171, 5, 66,1);
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 0;
            background-color: rgba(250, 242, 245, 1);
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: rgba(171, 5, 66, 0.8);
            border: 2px solid  rgba(171, 5, 66,1);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color:rgba(171, 5, 66,1)
        }

        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        #product_suggestions {
            border: 1px solid #ddd;
            max-height: 150px;
            background-color: #fff;
            position: absolute;
            width: 97%;
            overflow-y: auto;
            margin-top: 0;
            padding: 0;
        }

        #product_suggestions li {
            padding: 10px;
            cursor: pointer;
            list-style: none;
        }

        #product_suggestions li:hover {
            background-color:rgba(171, 5, 66, 1);
            color: #fff;
        }

    </style>
</head>
<body>

<div class="container">
    <h1>Управление продуктами</h1>

    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <h2>Добавить новый продукт</h2>
    <form method="POST" action="">
        <label for="name">Название продукта:</label>
        <input type="text" id="name" name="name" required>

        <label for="calorie_per_100">Калории на 100 грамм:</label>
        <input type="number" id="calorie_per_100" name="calorie_per_100" step="0.01" required>

        <label for="proteins">Белки на 100 грамм:</label>
        <input type="number" id="proteins" name="proteins" step="0.01" required>

        <label for="fats">Жиры на 100 грамм:</label>
        <input type="number" id="fats" name="fats" step="0.01" required>

        <label for="carbohydrates">Углеводы на 100 грамм:</label>
        <input type="number" id="carbohydrates" name="carbohydrates" step="0.01" required>

        <button type="submit" name="add_product">Добавить продукт</button>
    </form>

    <h2>Удалить продукт</h2>
    <form method="POST" action="">
        <label for="name_to_delete">Начните вводить название продукта для удаления:</label>
        <input type="text" id="name_to_delete" name="name_to_delete" autocomplete="off" required>

        <input type="hidden" id="product_id_to_delete" name="product_id_to_delete">

        <ul id="product_suggestions"></ul>

        <button type="submit" name="delete_product">Удалить продукт</button>
    </form>

    <script>
        document.getElementById('name_to_delete').addEventListener('input', function() {
            let query = this.value;

            if (query.length > 0) {
                fetch(`search_product.php?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        let suggestionBox = document.getElementById('product_suggestions');
                        suggestionBox.innerHTML = '';

                        data.forEach(product => {
                            let listItem = document.createElement('li');
                            listItem.textContent = product.name;
                            listItem.setAttribute('data-id', product.id);

                            listItem.addEventListener('click', function() {
                                document.getElementById('name_to_delete').value = this.textContent;
                                document.getElementById('product_id_to_delete').value = this.getAttribute('data-id');
                                suggestionBox.innerHTML = '';
                            });

                            suggestionBox.appendChild(listItem);
                        });
                    });
            } else {
                document.getElementById('product_suggestions').innerHTML = '';
            }
        });
    </script>
</div>
</body>
</html>
