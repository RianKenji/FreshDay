<?php
include '../config.php';

// Get the offset and limit from the URL
$offset = $_GET['offset'] ?? 0;
$limit = $_GET['limit'] ?? 6;

// Query to fetch products based on offset and limit
$sql = "SELECT * FROM menu LIMIT $offset, $limit";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Prepare product data
        $imagePath = "../img/" . $row['gambar'];
        if (!file_exists($imagePath)) {
            $imagePath = "../img/default.jpg"; // Default image if not found
        }
        
        $products[] = [
            'menuID' => $row['menuID'],
            'namaMenu' => $row['namaMenu'],
            'harga' => $row['harga'],
            'gambar' => $imagePath
        ];
    }
}

echo json_encode([
    'success' => count($products) > 0,
    'products' => $products
]);

$conn->close();
?>
