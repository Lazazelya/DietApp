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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id_to_delete = $_POST['user_id_to_delete'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id_to_delete);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $success_message = "Пользователь успешно удален!";
    } else {
        $error_message = "Ошибка при удалении пользователя: " . $stmt->error;
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_role'])) {
    $user_id_to_change = $_POST['user_id_to_change'];
    $new_role_id = $_POST['role_id'];

    $stmt = $conn->prepare("UPDATE users SET role_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_role_id, $user_id_to_change);

    if ($stmt->execute()) {
        $success_message = "Роль пользователя успешно обновлена!";
    } else {
        $error_message = "Ошибка при обновлении роли: " . $stmt->error;
    }

    $stmt->close();
}

$stmt = $conn->prepare("SELECT id, username, email, role_id FROM users");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$roles = [
    1 => 'Пользователь',
    2 => 'Администратор',
    3 => 'Тренер'
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
    <style>
        body {
        margin-top: 20px;
        padding: 0;
        background-color: rgba(250, 242, 245, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        }
        .container {
            margin-top: 20px;
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid rgba(171, 5, 66, 1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        td{
            border: 2px solid rgba(171, 5, 66, 1) !important;
        }
        th, td {
            padding: 10px!important;
            border: 2px solid rgba(171, 5, 66, 1)!important;
        }
        th {
            background-color: rgba(171, 5, 66, 0.7);
            color: white;
        }
        button {
            padding: 10px 15px;
            background-color: rgba(171, 5, 66, 0.7);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: rgba(171, 5, 66, 1);
            
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Управление пользователями</h1>

    <?php if (isset($success_message)): ?>
        <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <h2>Список пользователей</h2>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Имя пользователя</th>
            <th>Email</th>
            <th>Роль</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="user_id_to_change" value="<?php echo $user['id']; ?>">
                        <select name="role_id">
                            <?php foreach ($roles as $role_id => $role_name): ?>
                                <option value="<?php echo $role_id; ?>" <?php if ($user['role_id'] == $role_id) echo 'selected'; ?>>
                                    <?php echo $role_name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="change_role">Изменить роль</button>
                    </form>
                </td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="user_id_to_delete" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="delete_user">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
