<?php
session_start();
require 'db.php';
require 'navbar.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$message = isset($_SESSION['message']) ? $_SESSION['message'] : null;
unset($_SESSION['message']); 

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM dietologists WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$profile = $result->fetch_assoc();

$stmt->close();

$stmt = $conn->prepare("SELECT img_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($img_id);
$stmt->fetch();
$stmt->close();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль Диетолога</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
          body {
            font-family: Arial, sans-serif;
            background-color: rgba(250, 242, 245, 0.8);
            padding: 20px;}

        .profile-card {
           max-width: 600px;
            margin: 0 auto;
            margin-bottom: 20px;
            padding: 20px;
            background-color: rgba(171, 5, 66, 0.6);
            border: 2px solid  rgba(171, 5, 66,1);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .profile-image img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid  rgba(171, 5, 66,1);
            margin-right: 20px;
        }
        .profile-item {
            margin-top: 5px;
        }
        .profile-item label {
          
            font-weight: bold;
            display: block;}
        .profile-skills {
            background-color: rgba(250, 242, 245, 0.8);
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            background-color: rgba(250, 242, 245, 0.8);
            margin-bottom: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: rgba(171, 5, 66, 0.7);
            border: 2px solid  rgba(171, 5, 66,1);
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color:rgba(171, 5, 66, 1);
            
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <?php if ($message): ?>
            <div class="alert alert-info text-center">
                <?= $message ?>
            </div>
        <?php endif; ?>
        <form action="save_diet_profile.php" method="POST" enctype="multipart/form-data">
        <div class="profile-card">
        <div class="profile-header">
            <div class="profile-image">
            <?php if ($img_id) : ?>
        <img src="display_avatar.php?img_id=<?php echo htmlspecialchars($img_id); ?>" alt="Аватар" style="width:150px;height:150px;">
    <?php else: ?>
        <p>Аватар не установлен</p>
    <?php endif; ?>
</div>
                
                <div>
                    <input type="file" name="profile_image" accept="image/*" class="form-control">
                </div>
</div>
                
                <div class="profile-item">
                    <label>Имя:</label>
                    <input type="text" name="name" placeholder="Введите полное имя" value="<?= $profile['name'] ?? '' ?>" required>
                    </div>
                    <div class="profile-item">
                    <label>Квалификация:</label>
                    <input type="text" name="title"  placeholder="Введите квалификацию" value="<?= $profile['title'] ?? '' ?>" required>
                </div>
                
                <div class="profile-item">
                    <label>О себе:</label>
                    <textarea name="description" rows="5" placeholder="Напишите о себе..."><?= $profile['description'] ?? '' ?></textarea>
                </div>

                <div class="profile-item">
                    <label>Образование:</label>
                    <input type="text" name="education"  placeholder="Образование" value="<?= $profile['education'] ?? '' ?>" required>
                </div>
                <div class="profile-item">
                    <label>Сферы:</label>
                    <input type="text" name="specialization" placeholder="Сферы диетологии" value="<?= $profile['specialization'] ?? '' ?>" required>
                </div>
                <div class="profile-item">
                    <label>Сколько лет в диетологии:</label>
                    <input type="number" name="years_experience"  placeholder="Сколько лет в диетологии" value="<?= $profile['years_experience'] ?? '' ?>" required>
                </div>

                <div class="profile-skills">
                    <h5 class="text-center">Выберите навыки</h5>
                    <div class="form-check">
                        <input type="checkbox" name="SportPit" class="form-check-input" checked>
                        <label class="form-check-label">Спортивное питание для выступлений</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="ClinicalDiet" class="form-check-input" checked>
                        <label class="form-check-label">Клинические диетологические случаи </label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="Diet" class="form-check-input" checked>
                        <label class="form-check-label">Ведение питание</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="RPP" class="form-check-input" checked>
                        <label class="form-check-label">Работа с РПП</label>
                    </div>
                </div>
                <div class="profile-footer text-center py-3">
        <button type="submit">Сохранить профиль</button>
    </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
