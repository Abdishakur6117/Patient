<?php
session_start();
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'get_patient':
            get_patient($conn);
            break;
        case 'display_prescription':
            display_prescription($conn);
            break;
        case 'create_prescription':
            create_prescription($conn);
            break;
        case 'update_prescription':
            update_prescription($conn);
            break;
        case 'delete_prescription':
            delete_prescription($conn);
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

    if (!isset($_SESSION['doctor_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Doctor not logged in'
        ]);
        return;
    }

    $doctor_id = $_SESSION['doctor_id'];

    $stmt = $conn->prepare("
        SELECT 
            v.visit_id, 
            p.full_name AS patient_name
        FROM visits v
        JOIN appointments a ON v.appointment_id = a.appointment_id
        JOIN patients p ON a.patient_id = p.patient_id
        WHERE a.doctor_id = :doctor_id
    ");

    $stmt->bindParam(':doctor_id', $doctor_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

function display_prescription($conn) {
    if (!isset($_SESSION['doctor_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Doctor not logged in'
        ]);
        return;
    }

    $doctor_id = $_SESSION['doctor_id'];

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
        WHERE a.doctor_id = :doctor_id
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':doctor_id', $doctor_id, PDO::PARAM_INT);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);
}


function create_prescription($conn) {
    $required = ['visit_id', 'medication','dosage','duration'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }
    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO prescriptions 
        (visit_id, medication,dosage, duration) 
        VALUES (?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['visit_id'],
        $data['medication'],
        $data['dosage'],
        $data['duration']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'prescriptions recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record prescriptions');
    }
}
function update_prescription($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'patient_name' => $_POST['edit_visit_id'] ?? null,
        'medication' => $_POST['edit_medication'] ?? null,
        'dosage' => $_POST['edit_dosage'] ?? null,
        'duration' => $_POST['edit_duration'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE prescriptions SET
            visit_id = ?,
            medication = ?,
            dosage = ?,
            duration = ?
        WHERE prescription_id = ?
    ");
    
    $success = $stmt->execute([
        $required['patient_name'],
        $required['medication'],
        $required['dosage'],
        $required['duration'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'prescription updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update prescription');
    }
}

function delete_prescription($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Prescription  ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM prescriptions WHERE prescription_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'prescription  deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete prescription ');
    }
}
?>