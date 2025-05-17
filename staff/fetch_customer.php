<?php
session_start();
require_once '../Connection/connection.php'; 

$db = new DatabaseConnection();
$pdo = $db->getConnection();

header('Content-Type: application/json');

// Get search term from POST request
$searchTerm = trim($_POST['searchTerm'] ?? '');  // Clean the search term
$searchWildcard = "%$searchTerm%";  // For LIKE query
$isGenderSearch = in_array(strtolower($searchTerm), ['male', 'female']);

// Start the query
$query = "
    SELECT 
        s.sale_id,
        s.customer_name,
        pr.product_id as product_id,
        pr.name as product_name,
        s.sale_date,
        s.quantity,
        s.unit_price
    FROM sales s
    JOIN products pr ON s.product_id = pr.product_id
    WHERE 
        customer_name LIKE :search
";

// If the gender is part of the search term, apply gender filter
if ($isGenderSearch) {
    $query .= " OR gender = :gender";
}

try {
    $stmt = $pdo->prepare($query);
    
    // Define the parameters for the query
    $params = ['search' => $searchWildcard];
    
    if ($isGenderSearch) {
        // Add the gender filter
        $params['gender'] = strtolower($searchTerm);
    }

    // Execute query
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response with the data
    echo json_encode(['data' => $results]);

} catch (Exception $e) {
    // If any error happens, return it in JSON
    echo json_encode(['data' => [], 'error' => $e->getMessage()]);
}
?>
