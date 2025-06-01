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
        case 'get_doctor':
            get_doctor($conn);
            break;
        case 'display_appointment':
            display_appointment($conn);
            break;
        case 'create_appointment':
            create_appointment($conn);
            break;
        case 'update_appointment':
            update_appointment($conn);
            break;
        case 'delete_appointment':
            delete_appointment($conn);
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
            patient_id, 
            full_name as patient_name
        FROM patients 
    ");
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
function get_doctor($conn) {
    $stmt = $conn->query("
        SELECT 
            doctor_id, 
            full_name as doctor_name
        FROM doctors
    ");
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
function display_appointment($conn) {
    $query = "
       SELECT 
            a.appointment_id,
            p.patient_id,
            p.full_name as patient_name,
            d.doctor_id,
            d.full_name as doctor_name,
            a.appointment_date,
            a.reason,
            a.status
        FROM appointments a
         join patients p on a.patient_id = p.patient_id 
         join doctors d on a.doctor_id = d.doctor_id 
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}


function create_appointment($conn) {
    $required = ['patient_id', 'doctor_id','appointment_date','reason','status'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }
    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO appointments 
        (patient_id, doctor_id,appointment_date, reason,status) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['patient_id'],
        $data['doctor_id'],
        $data['appointment_date'],
        $data['reason'],
        $data['status']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Appointment recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record Appointment');
    }
}

function update_appointment($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'patient_name' => $_POST['edit_patient_id'] ?? null,
        'doctor_name' => $_POST['edit_doctor_id'] ?? null,
        'appointment_date' => $_POST['edit_appointment_date'] ?? null,
        'reason' => $_POST['edit_reason'] ?? null,
        'status' => $_POST['edit_status'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE appointments SET
            patient_id = ?,
            doctor_id = ?,
            appointment_date = ?,
            reason = ?,
            status = ?
        WHERE appointment_id = ?
    ");
    
    $success = $stmt->execute([
        $required['patient_name'],
        $required['doctor_name'],
        $required['appointment_date'],
        $required['reason'],
        $required['status'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Appointment updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update Appointment');
    }
}

function delete_appointment($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Appointment  ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Appointment  deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete Appointment ');
    }
}
?>