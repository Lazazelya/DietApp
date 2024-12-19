<?php
session_start();
require 'db.php'; 
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';

unset($_SESSION['message']);
unset($_SESSION['error']);

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM user_settings WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();
$stmt->close();

$goal = $settings['goal'] ?? '';
$reminder = $settings['reminder'] ?? 'none';
$water_goal = $settings['water_goal'] ?? 8;
$fruit_goal = $settings['fruit_goal'] ?? 6;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color:rgba(250, 242, 245, 1);
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: rgba(171, 5, 66, 0.6);
            padding: 20px;
            border-radius: 8px;
            border: 2px solid  rgba(171, 5, 66,1);
        
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        section {
            margin-bottom: 20px;
            
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="radio"],
        select,
        input[type="number"] {
            margin-bottom: 10px;
            border-radius: 4px;
            width: 100%;
            background-color: rgba(250, 242, 245, 1);
            border: 1px solid #ccc;
            padding: 8px;
          
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color:rgba(171, 5, 66, 0.7);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color:rgba(171, 5, 66, 1);
        }
        .message, .error {
        padding: 20px;
        margin: 10px 0;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        display: none;
        width: 250px; 
        text-align: center; 
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%); 
        animation: fadeInOut 4s forwards;
    }

    .message {
        background-color:rgba(47, 245, 162, 0.8);
    }

    .error {
        background-color:rgba(171, 5, 66, 0.8);
    }

    @keyframes fadeInOut {
        0% {
            opacity: 1; 
        }
        100% {
            opacity: 0; 
        }
    }

    .message.show, .error.show {
        display: block;
    }
    .message-block {
    background-color:rgba(171, 5, 66, 0.7);
    padding: 20px;
    height: 30px;
    width: 800px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.message-block p {
    margin: 0;
    font-size: 24px;
}

    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const message = "<?php echo addslashes($message); ?>";
            const error = "<?php echo addslashes($error); ?>";

            if (message) {
                const messageDiv = document.createElement("div");
                messageDiv.classList.add("message");
                messageDiv.innerHTML = message;
                document.body.prepend(messageDiv);
                messageDiv.classList.add("show");
            }

            if (error) {
                const errorDiv = document.createElement("div");
                errorDiv.classList.add("error");
                errorDiv.innerHTML = error;
                document.body.prepend(errorDiv);
                errorDiv.classList.add("show");
            }
        });
    </script>
</head>
<body>


<div class="container">

    <h1>Настройки</h1>

    <form action="save_settings.php" method="POST">
        <section>
            <label for="goal">Цель</label>
            <select name="goal" id="goal">
                <option value="maintain" <?php if ($goal === 'maintain') echo 'selected'; ?>>Поддержание веса</option>
                <option value="lose" <?php if ($goal === 'lose') echo 'selected'; ?>>Потеря веса</option>
                <option value="gain" <?php if ($goal === 'gain') echo 'selected'; ?>>Набор веса</option>
            </select>
        </section>

        <section>
            <h2>Напоминания</h2>
            <label for="reminder">Выберите напоминание:</label>
            <select name="reminder">
                <option value="none" <?php if ($reminder == 'none') echo 'selected'; ?>>Без напоминаний</option>
                <option value="drink-water" <?php if ($reminder == 'drink-water') echo 'selected'; ?>>Напоминание пить воду</option>
                <option value="eat-fruits" <?php if ($reminder == 'eat-fruits') echo 'selected'; ?>>Напоминание есть фрукты</option>
                <option value="exercise" <?php if ($reminder == 'exercise') echo 'selected'; ?>>Напоминание заниматься спортом</option>
            </select>
        </section>
        <section>
            <label for="water_goal">Цель по воде (в литрах)</label>
            <input type="number" name="water_goal" id="water_goal" value="<?php echo $water_goal; ?>">
        </section>

        <section>
            <label for="fruit_goal">Цель по фруктам (в штуках)</label>
            <input type="number" name="fruit_goal" id="fruit_goal" value="<?php echo $fruit_goal; ?>">
        </section>

        <button type="submit">Сохранить настройки</button>
    </form>
</div>

</body>
</html>
