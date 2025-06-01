<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'display_doctor':
            display_doctor($conn);
            break;
        case 'create_doctor':
            create_doctor($conn);
            break;
        case 'update_doctor':
            update_doctor($conn);
            break;
        case 'delete_doctor':
            delete_doctor($conn);
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
function display_doctor($conn) {
    $query = "
       SELECT 
            doctor_id,
            full_name as doctor_name,
            gender,
            date_of_birth,
            address,
            phone,
            email,
            specialty,
            created_at
        FROM doctors 
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function create_doctor($conn) {
    $required = ['doctor_name', 'gender','DOB','address','phone','email','specialty'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }

    // Check for duplicate name 
    $stmt = $conn->prepare("
        SELECT doctor_id FROM doctors 
        WHERE full_name = ?  
    ");
    $stmt->execute([$data['doctor_name']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('users record already exists for this doctor name');
    }
    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO doctors 
        (full_name, gender,date_of_birth, address,phone,email,specialty) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['doctor_name'],
        $data['gender'],
        $data['DOB'],
        $data['address'],
        $data['phone'],
        $data['email'],
        $data['specialty']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Doctor recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record Doctor');
    }
}

function update_doctor($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'doctor_name' => $_POST['edit_doctor_name'] ?? null,
        'gender' => $_POST['edit_gender'] ?? null,
        'DOB' => $_POST['edit_DOB'] ?? null,
        'address' => $_POST['edit_address'] ?? null,
        'phone' => $_POST['edit_phone'] ?? null,
        'email' => $_POST['edit_email'] ?? null,
        'specialty' => $_POST['edit_specialty'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    
    // Check for duplicate (excluding current record)
    $stmt = $conn->prepare("
        SELECT doctor_id FROM doctors 
        WHERE full_name = ?  AND doctor_id != ?
    ");
    $stmt->execute([
        $required['doctor_name'],
        $required['id']
    ]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('A user with this doctor name already exists.');
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE doctors SET
            full_name = ?,
            gender = ?,
            date_of_birth = ?,
            address = ?,
            phone = ?,
            email = ?,
            specialty = ?
        WHERE doctor_id = ?
    ");
    
    $success = $stmt->execute([
        $required['doctor_name'],
        $required['gender'],
        $required['DOB'],
        $required['address'],
        $required['phone'],
        $required['email'],
        $required['specialty'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'doctor updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update doctor');
    }
}

function delete_doctor($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Doctor  ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM doctors WHERE doctor_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Doctor  deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete Doctor ');
    }
}
?>