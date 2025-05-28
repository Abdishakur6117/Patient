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
        c.company_id,
        u.user_id,
        u.name AS employee_name,
        c.company_name,
        c.description,
        c.location
    FROM companies c
    JOIN users u ON c.employer_id = u.user_id
    WHERE 
        u.name LIKE :search 
        OR c.company_name LIKE :search 
        OR c.location LIKE :search
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
