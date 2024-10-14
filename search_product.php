<?php
require 'db.php';

if (isset($_GET['query'])) {
    $search_query = $_GET['query'] . '%';

    $stmt = $conn->prepare("SELECT id, name FROM food_products WHERE name LIKE ?");
    $stmt->bind_param("s", $search_query);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($products);
}
?>
