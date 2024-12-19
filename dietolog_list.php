<?php 
session_start();  
require 'db.php';  
include 'navbar.php';  

$sql = "SELECT d.name, d.description, u.img_id, d.user_id 
        FROM dietologists d
        JOIN users u ON d.user_id = u.id";
$result = $conn->query($sql);

$doctors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}


$chunks = array_chunk($doctors, 3);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dietologists</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    body {
            font-family: Arial, sans-serif;
            background-color: rgba(250, 242, 245, 0.8);
            padding: 20px;}
 
    .featurette-divider {
        margin: 5rem 0;
    }
    .carousel-inner .carousel-item {
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    .carousel-inner .carousel-item img {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border: 2px solid  rgba(171, 5, 66,1);
        border-radius: 50%;
    }
    .btn-secondary {
        background-color: rgba(171, 5, 66, 0.7);
            border: 2px solid  rgba(171, 5, 66,1);
            color: white;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
    }
    .btn-secondary:hover {
        background-color:rgba(171, 5, 66, 1);
    }
</style>
<body>
    <main class="container py-5">
    <div>
            <h2 class="featurette-heading fw-normal lh-1">Наши Диетологи</h2>
            <p class="lead">Посмотрите лучших специалистов в области диетологии!</p>
        </div>
       
        <div id="dietologistsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($chunks as $index => $chunk): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <?php foreach ($chunk as $doctor): ?>
                            <div class="text-center">
                            <?php if ($doctor['img_id']): ?>
                                    <img src="display_avatar.php?img_id=<?= htmlspecialchars($doctor['img_id']) ?>" alt="<?= htmlspecialchars($doctor['name']) ?>">
                                <?php else: ?>
                                    <img src="path/to/default-image.jpg" alt="Default Avatar">
                                <?php endif; ?> <h2 class="fw-normal"><?= htmlspecialchars($doctor['name']) ?></h2>
                                <p><?= htmlspecialchars($doctor['description']) ?></p>
                                <p><a class="btn btn-secondary" href="view_profile.php?user_id=<?= $doctor['user_id'] ?>">Подробнее &raquo;</a></p>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button class="carousel-control-prev" type="button" data-bs-target="#dietologistsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#dietologistsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <hr class="featurette-divider">

    
        <div class="row featurette">
            <div class="col-md-7">
                <h2 class="featurette-heading fw-normal lh-1">Наши диетологи спасут ваши жизни <span class="text-body-secondary">Переходите на сторону зеленого сельдерея.</span></h2>
                <p class="lead">Укрепите здоровье и наслаждайтесь жизнью.</p>
            </div>
            <div class="col-md-5">
    <img class="featurette-image img-fluid mx-auto" src="stat/1.jpg" alt="Диетологи" width="500" height="500">
        </div>

        </div>
        <hr class="featurette-divider">
        <div class="row featurette">
      <div class="col-md-7 order-md-2">
        <h2 class="featurette-heading fw-normal lh-1">Вы хотите жить дольше?. <span class="text-body-secondary">Мечтаете похудеть но не доверяете себе?</span></h2>
        <p class="lead">Обратите внимание на наших крутых и квалифицированных диетологов.</p>
      </div>
      <div class="col-md-5">
    <img class="featurette-image img-fluid mx-auto" src="stat/2.jpg" alt="Диетологи" width="500" height="500">
        </div>
    </div>  </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
