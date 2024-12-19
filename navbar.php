<?php
require 'db.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$is_admin = false;
$is_nutritionist = false;


if ($user_id) {
    $stmt = $conn->prepare("
        SELECT r.role_name
        FROM users u
        JOIN roles r ON u.role_id = r.id  
        WHERE u.id = ?
    ");
    $stmt->bind_param("i", $user_id);  
    $stmt->execute();  
    $result = $stmt->get_result(); 
    $user = $result->fetch_assoc();  
     $is_admin = ($user['role_name'] === 'admin');
    $is_nutritionist = ($user['role_name'] === 'nutritionist');
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="sidebar">
  <a href="main.php" class="d-flex align-items-center mb-4 link-body-emphasis text-decoration-none">
    <i class="bi bi-droplet-half me-2" style="font-size: 24px;"></i>
    <span class="fs-4 text">FlexIO</span>
  </a>
  <ul class="nav nav-pills flex-column text-center">

    <?php if ($user_id): ?>
        <?php if ($is_nutritionist): ?>
          <li class="nav-item">
            <a href="manage_subscriptions.php" class="nav-link active">
              <svg class="bi pe-none" width="24" height="24"><use xlink:href="#person-lines-fill"></use></svg>
              <span class="text">Управление пользователями</span>
            </a>
          </li><li class="nav-item">
            <a href="diet_profile.php" class="nav-link active">
              <svg class="bi pe-none" width="24" height="24"><use xlink:href="#person-lines-fill"></use></svg>
              <span class="text">Профиль</span>
            </a>
          </li>
          <li class="nav-item">
          <a href="list_recipe.php" class="nav-link link-body-emphasis">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#grid"></use></svg>
            <span class="text">Рецепты</span>
          </a>
        </li>
          <li class="nav-item">
          <a href="logout.php" class="nav-link link-body-emphasis">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#box-arrow-right"></use></svg>
            <span class="text">Выйти</span>
          </a>
        </li>
        <?php elseif ($is_admin): ?>
          <li class="nav-item">
            <a href="add_food.php" class="nav-link active">
              <svg class="bi pe-none" width="24" height="24"><use xlink:href="#gear"></use></svg>
              <span class="text">Настройки каталога</span>
            </a>
            </li>
            <li class="nav-item">
            <a href="delete_user.php" class="nav-link active">
              <svg class="bi pe-none" width="24" height="24"><use xlink:href="#gear"></use></svg>
              <span class="text">Пользователи</span>
            </a>
          </li>
          <li class="nav-item">
          <a href="logout.php" class="nav-link link-body-emphasis">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#box-arrow-right"></use></svg>
            <span class="text">Выйти</span>
          </a>
        </li>
        <?php else: ?>

        <li class="nav-item">
          <a href="tracker.php" class="nav-link active">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#home"></use></svg>
            <span class="text">Трекер</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="profile.php" class="nav-link active">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#person"></use></svg>
            <span class="text">Профиль</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="food_catalog.php" class="nav-link link-body-emphasis">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#grid"></use></svg>
            <span class="text">Каталог продуктов</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="dietolog_list.php" class="nav-link link-body-emphasis">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#grid"></use></svg>
            <span class="text">Диетологи</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="list_recipe.php" class="nav-link link-body-emphasis">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#grid"></use></svg>
            <span class="text">Рецепты</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="user_settings.php" class="nav-link link-body-emphasis">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#grid"></use></svg>
            <span class="text">Настройки</span>
          </a>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="nav-link link-body-emphasis">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#box-arrow-right"></use></svg>
            <span class="text">Выйти</span>
          </a>
        </li>
        <?php endif; ?>
        <?php else: ?>
        <li class="nav-item">
          <a href="login.php" class="nav-link active">
            <svg class="bi pe-none" width="24" height="24"><use xlink:href="#box-arrow-in-right"></use></svg>
            <span class="text">Войти</span>
          </a>
        </li>
    <?php endif; ?>

  </ul>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    window.addEventListener('load', () => {
        navLinks.forEach(link => link.classList.remove('active'));
    });
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', () => {
            navLinks.forEach(link => link.classList.remove('active'));
        });
    });
   
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navLinks.forEach(link => link.classList.remove('active'));
            link.classList.add('active');
        });
    });
</script>

</body>
</html>
