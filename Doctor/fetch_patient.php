<?php
session_start();
require_once '../Connection/connection.php'; 

$db = new DatabaseConnection();
$pdo = $db->getConnection();

header('Content-Type: application/json');

$searchTerm = trim($_POST['searchTerm'] ?? '');
$searchWildcard = "%$searchTerm%";

$query = "
    SELECT 
        patient_id,
        full_name as patient_name,
        gender,
        date_of_birth,
        phone,
        email,
        address,
        created_at
    FROM patients 
    WHERE 
        full_name LIKE :search 
        OR email LIKE :search 
        OR gender LIKE :search
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute(['search' => $searchWildcard]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $results]);

} catch (Exception $e) {
    echo json_encode(['data' => [], 'error' => $e->getMessage()]);
}
?>
