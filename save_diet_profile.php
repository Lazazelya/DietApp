<?php
require 'db.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $education = $_POST['education'];
    $specialization = $_POST['specialization'];
    $years_experience = $_POST['years_experience'];

    $user_id = $_SESSION['user_id'];

    $skills = [
        'SportPit' => isset($_POST['SportPit']) ? 1 : 0,
        'ClinicalDiet' => isset($_POST['ClinicalDiet']) ? 1 : 0,
        'Diet' => isset($_POST['Diet']) ? 1 : 0,
        'RPP' => isset($_POST['RPP']) ? 1 : 0
    ];


    $stmt = $conn->prepare("SELECT id FROM dietologists WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dietologist = $result->fetch_assoc();

    if ($dietologist) {

        $dietologist_id = $dietologist['id'];
        $stmt = $conn->prepare("UPDATE dietologists SET name = ?, title = ?, description = ?, education = ?, specialization = ?, years_experience = ?, img_id = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $name, $title, $description, $education, $specialization, $years_experience, $img_id, $dietologist_id);
        if ($stmt->execute()) {
            $stmt_skill = $conn->prepare("INSERT INTO dietologist_skills (dietologist_id, skill) VALUES (?, ?)");
            foreach ($skills as $skill => $value) {
                if ($value) {
                    $stmt_skill->bind_param("is", $dietologist_id, $skill);
                    if (!$stmt_skill->execute()) {
                        echo "Ошибка при добавлении навыка: " . $stmt_skill->error;
                        exit();
                    }
                }
            }
            $_SESSION['message'] = "Профиль успешно обновлен!";
        } else {
            $_SESSION['message'] = "Ошибка при обновлении профиля: " . $stmt->error;
        }
    } else {

        $stmt = $conn->prepare("INSERT INTO dietologists (user_id, name, title, description, education, specialization, years_experience, img_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssis", $user_id, $name, $title, $description, $education, $specialization, $years_experience, $img_id);

        if ($stmt->execute()) {
            $dietologist_id = $stmt->insert_id;

        
            $stmt_skill = $conn->prepare("INSERT INTO dietologist_skills (dietologist_id, skill) VALUES (?, ?)");
            foreach ($skills as $skill => $value) {
                if ($value) {
                    $stmt_skill->bind_param("is", $dietologist_id, $skill);
                    if (!$stmt_skill->execute()) {
                        echo "Ошибка при добавлении навыка: " . $stmt_skill->error;
                        exit();
                    }
                }
            }

            $_SESSION['message'] = "Профиль успешно сохранен!";
        } else {
            $_SESSION['message'] = "Ошибка: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();

    header("Location: diet_profile.php");
    exit();
}
?>
