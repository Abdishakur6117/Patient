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
        supplier_id,
        name,
        contact_person,
        phone,
        email,
        gender,
        address
    FROM suppliers
    WHERE 
        name LIKE :search
        OR email LIKE :search
        OR phone LIKE :search
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
