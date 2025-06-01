<?php
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
        case 'display_visit':
            display_visit($conn);
            break;
        case 'create_visit':
            create_visit($conn);
            break;
        case 'update_visit':
            update_visit($conn);
            break;
        case 'delete_visit':
            delete_visit($conn);
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
            a.appointment_id, 
            p.full_name as patient_name
        FROM appointments a
        join patients p on a.patient_id = p.patient_id 

    ");
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
function display_visit($conn) {
    $query = "
       SELECT 
            v.visit_id,
            v.appointment_id,
            p.full_name as patient_name,
            v.diagnosis,
            v.treatment,
            v.visit_date,
            v.charge
        FROM visits  v
         join patients p on v.appointment_id = p.patient_id 
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function create_visit($conn) {
    $required = ['appointment_id', 'diagnosis','treatment','charge'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }
    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO visits 
        (appointment_id, diagnosis,treatment, charge) 
        VALUES (?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['appointment_id'],
        $data['diagnosis'],
        $data['treatment'],
        $data['charge']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Visits recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record Visits');
    }
}
function update_visit($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'patient_name' => $_POST['edit_appointment_id'] ?? null,
        'diagnosis' => $_POST['edit_diagnosis'] ?? null,
        'treatment' => $_POST['edit_treatment'] ?? null,
        'charge' => $_POST['edit_charge'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE visits SET
            appointment_id = ?,
            diagnosis = ?,
            treatment = ?,
            charge = ?
        WHERE visit_id = ?
    ");
    
    $success = $stmt->execute([
        $required['patient_name'],
        $required['diagnosis'],
        $required['treatment'],
        $required['charge'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Visits updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update Visits');
    }
}

function delete_visit($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Visit  ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM visits WHERE visit_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Visits  deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete Visits ');
    }
}
?>