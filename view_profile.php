<?php
session_start();
require 'db.php';
require 'navbar.php';

if (!isset($_GET['user_id'])) {
    echo "Не указан идентификатор диетолога.";
    exit();
}


$user_id = intval($_GET['user_id']);

$sql = "SELECT d.name, d.title, d.description, d.education, d.specialization, d.years_experience, u.img_id, d.user_id, d.id
 FROM dietologists d
 join users u ON d.user_id = u.id
 where d.user_id= ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows === 0) {
    echo "Диетолог не найден.";
    exit();
}

$profile = $result->fetch_assoc();
$stmt->close();


$skill_translations = [
    'SportPit' => 'Спортивное питание для выступлений',
    'ClinicalDiet' => 'Клинические диетологические случаи',
    'Diet' => 'Ведение питания',
    'RPP' => 'Работа с РПП',
];

$sql_skills = "
    SELECT ds.skill 
    FROM dietologist_skills ds
    JOIN dietologists d ON ds.dietologist_id = d.id
    WHERE d.user_id = ?";
$stmt_skills = $conn->prepare($sql_skills);
$stmt_skills->bind_param("i", $user_id);
$stmt_skills->execute();
$result_skills = $stmt_skills->get_result();

$skills = [];
while ($row = $result_skills->fetch_assoc()) {
    $skills[] = $row['skill'];
}

$stmt_skills->close();
$conn->close();
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
            margin: auto;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: rgba(171, 5, 66, 0.6);
            border: 2px solid  rgba(171, 5, 66,1);
            overflow: hidden;
            background-color: #fff;
        }
        .profile-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid  rgba(171, 5, 66,1);
            margin-right: 20px;
        }
        .profile-body {
            padding: 20px;
        }
        .profile-skills {
            background-color: rgba(250, 242, 245, 0.8);
            
            padding: 15px;
            margin-top: 20px;
        }
        .action-buttons {
            margin-top: 20px;
            text-align: center;
        }
        .btn-subscribe {
            background-color: rgba(171, 5, 66, 0.7);
            border: 2px solid  rgba(171, 5, 66,1);
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .btn-subscribe:hover {
            background-color:rgba(171, 5, 66, 1);
            
        }
        .list-group{
            border: 2px solid  rgba(171, 5, 66,1);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="profile-card">
            <div class="profile-header">
            <?php if ($profile['img_id']): ?>
                                    <img src="display_avatar.php?img_id=<?= htmlspecialchars($profile['img_id']) ?>" alt="<?= htmlspecialchars($doctor['name']) ?>">
                                <?php else: ?>
                                    <img src="path/to/default-image.jpg" alt="Default Avatar">
                                <?php endif; ?> <h3><?= htmlspecialchars($profile['name']) ?></h3>
            </div>
            
            <div class="profile-body">
                <p><strong>Квалификация:</strong> <?= htmlspecialchars($profile['title']) ?></p>
                <p><strong>Описание:</strong> <?= htmlspecialchars($profile['description']) ?></p>
                <p><strong>Образование:</strong> <?= htmlspecialchars($profile['education']) ?></p>
                <p><strong>Сферы:</strong> <?= htmlspecialchars($profile['specialization']) ?></p>
                <p><strong>Опыт:</strong> <?= htmlspecialchars($profile['years_experience']) ?> лет</p>
            </div>
            
            <div class="profile-skills">
                <h5 class="text-center">Навыки</h5>
                <?php if (!empty($skills)): ?>
                    <ul class="list-group">
                        <?php foreach ($skills as $skill): ?>
                            <?php if (isset($skill_translations[$skill])): ?>
                                <li class="list-group-item"><?= htmlspecialchars($skill_translations[$skill]) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-center">Навыки не указаны.</p>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons">
                <a href="dietolog_list.php" class="btn btn-subscribe">Выбрать диетолога</a>
                <button class="btn btn-subscribe" data-bs-toggle="modal" data-bs-target="#confirmModal">Оформить подписку</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Подтверждение подписки</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    Вы точно хотите оформить подписку на диетолога <?= htmlspecialchars($profile['name']) ?>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Нет</button>
                    <a href="subscribe.php?dietologist_id=<?= htmlspecialchars($profile['id']) ?>" class="btn btn-success">Да, оформить</a>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
