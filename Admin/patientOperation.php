<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'display_patient':
            display_patient($conn);
            break;
        case 'create_patient':
            create_patient($conn);
            break;
        case 'update_patient':
            update_patient($conn);
            break;
        case 'delete_patient':
            delete_patient($conn);
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

function display_patient($conn) {
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
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function create_patient($conn) {
    $required = ['patient_name', 'gender','DOB','phone','email','address'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }

    // Check for duplicate name 
    $stmt = $conn->prepare("
        SELECT patient_id FROM patients 
        WHERE full_name = ?  
    ");
    $stmt->execute([$data['patient_name']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('users record already exists for this patient name');
    }
    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO patients 
        (full_name, gender,date_of_birth,phone,email,address) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['patient_name'],
        $data['gender'],
        $data['DOB'],
        $data['phone'],
        $data['email'],
        $data['address']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'patient recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record patient');
    }
}

function update_patient($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'patient_name' => $_POST['edit_patient_name'] ?? null,
        'gender' => $_POST['edit_gender'] ?? null,
        'DOB' => $_POST['edit_DOB'] ?? null,
        'phone' => $_POST['edit_phone'] ?? null,
        'email' => $_POST['edit_email'] ?? null,
        'address' => $_POST['edit_address'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    
    // Check for duplicate (excluding current record)
    $stmt = $conn->prepare("
        SELECT patient_id FROM patients 
        WHERE full_name = ?  AND patient_id != ?
    ");
    $stmt->execute([
        $required['patient_name'],
        $required['id']
    ]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('A user with this patient name already exists.');
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE patients SET
            full_name = ?,
            gender = ?,
            date_of_birth = ?,
            phone = ?,
            email = ?,
            address = ?
        WHERE patient_id = ?
    ");
    
    $success = $stmt->execute([
        $required['patient_name'],
        $required['gender'],
        $required['DOB'],
        $required['phone'],
        $required['email'],
        $required['address'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'patient updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update patient');
    }
}

function delete_patient($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Patient  ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM patients WHERE patient_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'patient  deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete patient ');
    }
}
?>