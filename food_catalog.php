<?php
session_start();
require 'navbar.php';
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT food_id, name, calorie_per_100, proteins, fats, carbohydrates FROM food_items ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();

$foods = [];
while ($row = $result->fetch_assoc()) {
    $foods[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выбор продукта</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
   body {
      
        margin: 0;
        padding: 0;
        background-color: rgba(250, 242, 245, 1);
        display: flex;
        align-items: center;
        justify-content: center;
       
    }


    .container {
        background-color: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 80%;
        max-width: 900px;
        text-align: center;
        border: 2px solid rgba(171, 5, 66, 1);
    }

    h1 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 15px;
        text-align: center;
        border: 1px solid #ddd;
    }

    th {
        background-color: rgba(171, 5, 66, 0.7);
        color: white;
    }

    td {
        background-color: rgba(250, 242, 245, 0.7);
        font-size: 16px;
    }

    tr:nth-child(even) td {
        background-color: rgba(250, 242, 245,1);
    }

    tr:hover td {
        background-color:rgba(171, 5, 66, 0.3);
        cursor: pointer;
    }

    p {
        font-size: 18px;
        color: #666;
    }


    .add-button {
        text-decoration: none;
        color: #ffffff;
        background-color: rgba(171, 5, 66, 0.7);
        padding: 8px 15px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .add-button:hover {
        background-color: rgba(171, 5, 66, 1);
    }

    .back-link {
        display: block;
        margin-top: 20px;
        color: rgba(171, 5, 66, 0.8);
        text-decoration: none;
        font-size: 16px;
    }

    .back-link:hover {
        color: rgba(171, 5, 66, 1);
    }

    .search-box {
        position: relative;
        margin-bottom: 20px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }

    #searchInput {
        padding: 8px 12px;
        border: 2px solid  rgba(171, 5, 66,1);
        font-size: 16px;
        margin-left: 10px;
        width: 80%;
        border-radius: 5px;

    }

    #suggestions {
        left: 0;
        right: 0;
        background-color: #fff;
        top: 100%;
        max-height: 200px;
        overflow-y: auto;
        display: none;
        position: absolute;
       
        width: 100%;
        z-index: 10;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border-top: none;
    }

    .suggestion-item {
        padding: 8px;
        cursor: pointer;
    }

    .suggestion-item:hover {
        background-color: #f1f1f1;
    }

    .clear-btn {
        padding: 8px 16px;
        background-color: rgba(171, 5, 66, 0.7);;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 25px;
    }

    .clear-btn:hover {
        background-color: rgba(171, 5, 66, 1);;
    }
</style>

<body>

<div class="container">
    <h1>Выберите продукт</h1>

    <div class="search-box">
       
        <input type="text" id="searchInput" placeholder="Поиск по названию..." onkeyup="searchProducts()">
        <button class="clear-btn" onclick="clearSearch()">Очистить</button>
        <div id="suggestions" class="suggestions"></div>
    </div>

    <?php if (empty($foods)): ?>
        <p>Продукты не добавлены в базу данных.</p>
    <?php else: ?>
        <table id="foodTable">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Калории</th>
                    <th>Белки</th>
                    <th>Жиры</th>
                    <th>Углеводы</th>
                    <th>Добавить</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($foods as $food): ?>
                    <tr data-name="<?php echo htmlspecialchars(strtolower($food['name'])); ?>">
                        <td><?php echo htmlspecialchars($food['name']); ?></td>
                        <td><?php echo htmlspecialchars($food['calorie_per_100']); ?> ккал</td>
                        <td><?php echo htmlspecialchars($food['proteins']); ?> г</td>
                        <td><?php echo htmlspecialchars($food['fats']); ?> г</td>
                        <td><?php echo htmlspecialchars($food['carbohydrates']); ?> г</td>
                        <td>
                            <a href="add_food_to_meal.php?food_id=<?php echo $food['food_id']; ?>" class="add-button">Добавить</a>
                            </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="tracker.php" class="back-link">Назад</a>
</div>

<script>

let isSearchCompleted = false;


function searchProducts() {
    if (isSearchCompleted) return;  
    let input = document.getElementById('searchInput').value.toLowerCase();  
    let suggestionsBox = document.getElementById('suggestions');
    let rows = document.querySelectorAll('#foodTable tbody tr');
    let suggestions = [];
    let matches = 0;

    suggestionsBox.innerHTML = '';

    rows.forEach(row => {
        let productName = row.cells[0].textContent.toLowerCase();  
        if (productName.includes(input)) {  
            row.style.display = '';
            matches++;

            if (!suggestions.includes(row.cells[0].textContent)) {
                suggestions.push(row.cells[0].textContent);
            }
        } else {
            row.style.display = 'none';
        }
    });

    if (input && matches > 0) {
        suggestionsBox.style.display = 'block';
        suggestions.forEach(suggestion => {
            let div = document.createElement('div');
            div.classList.add('suggestion-item');
            div.textContent = suggestion;
            div.onclick = function() {
                document.getElementById('searchInput').value = suggestion;
                suggestionsBox.innerHTML = '';  
                filterTableBySearch(suggestion);  
            };
            suggestionsBox.appendChild(div);
        });
    } else {
        suggestionsBox.style.display = 'none';
    }
}

function filterTableBySearch(query) {
    let rows = document.querySelectorAll('#foodTable tbody tr');
    rows.forEach(row => {
        let productName = row.cells[0].textContent.toLowerCase();  
        if (productName.includes(query.toLowerCase())) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

document.getElementById('searchInput').addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        let inputValue = this.value.toLowerCase();
        filterTableBySearch(inputValue);  
        document.getElementById('suggestions').style.display = 'none';  
        isSearchCompleted = true;  
    }
});


function clearSearch() {
    document.getElementById('searchInput').value = '';  
    document.getElementById('suggestions').style.display = 'none';
    let rows = document.querySelectorAll('#foodTable tbody tr');
    rows.forEach(row => {
        row.style.display = ''; 
    });
    isSearchCompleted = false; 
}
</script>

</body>
</html>
