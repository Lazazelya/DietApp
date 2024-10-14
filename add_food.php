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
    $calories_per_100g = $_POST['calories_per_100g'];

    if (empty($name)) {
        $error_message = "Название продукта не может состоять только из пробелов! Добавление не произошло!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM food_products WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Продукт с таким же названием существует, уточните название добавляемого продукта.";
        } else {
            $stmt = $conn->prepare("INSERT INTO food_products (name, calories_per_100g, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("sd", $name, $calories_per_100g);

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
        $stmt = $conn->prepare("DELETE FROM food_products WHERE id = ?");
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
            margin: 0;
            background-color: #bdacbb;
            font-family: Arial, sans-serif;
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
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: rgba(139, 83, 179, 0.62);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #715ac8;
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
            background-color: rgba(139, 83, 179, 0.62);
            color: #fff;
        }

    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

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

        <label for="calories_per_100g">Калории на 100 грамм:</label>
        <input type="number" id="calories_per_100g" min="1" name="calories_per_100g" step="0.01" required>

        <button type="submit" name="add_product">Добавить продукт</button>
    </form>

    <h2>Удалить продукт</h2>
    <form method="POST" action="">
        <label for="name_to_delete">Начните вводить название продукта для удаления:</label>
        <input type="text" id="name_to_delete" name="name_to_delete" autocomplete="off" required>

        <!-- Скрытое поле для хранения ID выбранного продукта -->
        <input type="hidden" id="product_id_to_delete" name="product_id_to_delete">

        <ul id="product_suggestions" style="list-style-type:none; padding-left: 0;"></ul>

        <button type="submit" name="delete_product">Удалить продукт</button>
    </form>

    <script>

        document.getElementById('name_to_delete').addEventListener('input', function() {
            let query = this.value;

            if (query.length > 0) { // Измените 1 на 0, чтобы искать начиная с 1 символа
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
                                suggestionBox.innerHTML = '';  // Очищаем список предложений
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
