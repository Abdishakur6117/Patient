<?php
    session_start();
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'display_prescription':
            display_prescription($conn);
            break;
        default:
            throw new Exception('Invalid action');
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function get_patient($conn) {
    $stmt = $conn->query("
        SELECT 
            v.visit_id, 
            p.full_name AS patient_name
        FROM visits v
        JOIN appointments a ON v.appointment_id = a.appointment_id
        JOIN patients p ON a.patient_id = p.patient_id
    ");
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

// function display_prescription($conn) {
//     $query = "
//        SELECT 
//             pr.prescription_id,
//             pr.visit_id,
//             p.full_name as patient_name,
//             pr.medication,
//             pr.dosage,
//             pr.duration
//         FROM prescriptions  pr
//          JOIN visits v ON pr.visit_id = v.visit_id
//          JOIN appointments a ON v.appointment_id = a.appointment_id
//          JOIN patients p ON a.patient_id = p.patient_id
//     ";
    
//     $stmt = $conn->query($query);
//     echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
// }
function display_prescription($conn) {


    // Hubi in patient uu login yahay
    if (!isset($_SESSION['patient_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not logged in as patient'
        ]);
        return;
    }

    $patient_id = $_SESSION['patient_id'];

    $query = "
        SELECT 
            pr.prescription_id,
            pr.visit_id,
            p.full_name AS patient_name,
            pr.medication,
            pr.dosage,
            pr.duration
        FROM prescriptions pr
        JOIN visits v ON pr.visit_id = v.visit_id
        JOIN appointments a ON v.appointment_id = a.appointment_id
        JOIN patients p ON a.patient_id = p.patient_id
        WHERE p.patient_id = :patient_id
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute(['patient_id' => $patient_id]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

?>