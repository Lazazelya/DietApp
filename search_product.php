<?php
require 'db.php';

$query = $_GET['query'] ?? '';

if (!empty($query)) {
    $stmt = $conn->prepare("SELECT food_id, name FROM food_items WHERE name LIKE ?");
    $search_term = "%" . $query . "%";
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = ['id' => $row['food_id'], 'name' => $row['name']];
    }

    echo json_encode($products);
}
?>
