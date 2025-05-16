<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'display_supplier':
            display_supplier($conn);
            break;
            
        case 'create_supplier':
            create_supplier($conn);
            break;
            
        case 'update_supplier':
            update_supplier($conn);
            break;
            
        case 'delete_supplier':
            delete_supplier($conn);
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
function display_supplier($conn) {
    $query = "
        SELECT 
            s.supplier_id,
            s.name as supplier_name,
            s.contact_person,
            s.phone,
            s.email,
            s.gender,
            s.address
        FROM Suppliers  s 
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}


function create_supplier($conn) {
    $required = ['supplier_name', 'contact_person','phone','email','gender','address'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }

    // Check for duplicate supplier name 
    $stmt = $conn->prepare("
        SELECT supplier_id FROM suppliers 
        WHERE name = ?  
    ");
    $stmt->execute([$data['supplier_name']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('supplier record already exists for this supplier Name');
    }
    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO suppliers 
        (name, contact_person,phone,email,gender,address ) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['supplier_name'],
        $data['contact_person'],
        $data['phone'],
        $data['email'],
        $data['gender'],
        $data['address']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'supplier recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record supplier');
    }
}

function update_supplier($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'supplier_name' => $_POST['edit_supplier_name'] ?? null,
        'contact_person' => $_POST['edit_contact_person'] ?? null,
        'phone' => $_POST['edit_phone'] ?? null,
        'email' => $_POST['edit_email'] ?? null,
        'gender' => $_POST['edit_gender'] ?? null,
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
        SELECT supplier_id FROM suppliers 
        WHERE name = ?  AND supplier_id != ?
    ");
    $stmt->execute([
        $required['supplier_name'],
        $required['id']
    ]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('A supplier with this supplier name already exists.');
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE suppliers SET
            name = ?,
            contact_person = ?,
            phone = ?,
            email = ?,
            gender = ?,
            address = ?
        WHERE supplier_id = ?
    ");
    
    $success = $stmt->execute([
        $required['supplier_name'],
        $required['contact_person'],
        $required['phone'],
        $required['email'],
        $required['gender'],
        $required['address'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'supplier updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update supplier');
    }
}

function delete_supplier($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('supplier ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'supplier deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete supplier');
    }
}
?>