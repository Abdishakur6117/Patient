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
        up.profile_id,
        u.user_id,
        u.name AS jobSeeker_name,
        u.email,
        up.phone,
        up.address,
        up.education,
        up.experience,
        up.skills
    FROM user_profiles up
    JOIN users u ON up.user_id = u.user_id
    WHERE 
        u.name LIKE :search 
        OR up.phone LIKE :search 
        OR up.skills LIKE :search
        OR up.address LIKE :search
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
