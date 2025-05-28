<?php
session_start(); 
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) {          
        case 'get_employee':
            get_employee($conn);
            break;
        case 'display_company':
            display_company($conn);
            break;
            
        case 'create_company':
            create_company($conn);
            break;
            
        case 'update_company':
            update_company($conn);
            break;
            
        case 'delete_company':
            delete_company($conn);
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
function get_employee($conn) {
    $stmt = $conn->query("
        SELECT 
            user_id, 
            name as employee_name
        FROM users where role='employee' 
    ");
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
function display_company($conn) {

    $query = "
        SELECT 
            c.company_id,
            u.user_id,
            u.name as employee_name,
            c.company_name,
            c.description,
            c.location
        FROM companies   c
        join users u on c.employer_id = u.user_id
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
function create_company($conn) {


    $required = ['employee_name', 'company_name','description','location'];
    $data = [];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = trim($_POST[$field]);
    }

    // Check for duplicate category name
    $stmt = $conn->prepare("
        SELECT company_id FROM companies 
        WHERE company_name =?
    ");
    $stmt->execute([$data['company_name']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('company already exists with this company name');
    }

    // Insert category with user_id
    $stmt = $conn->prepare("
        INSERT INTO companies (employer_id,company_name, description, location) 
        VALUES (?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['employee_name'],
        $data['company_name'],
        $data['description'],
        $data['location']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'company recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record company');
    }
}


function update_company($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'employee Name' => $_POST['edit_employee_name'] ?? null,
        'company Name' => $_POST['edit_company_name'] ?? null,
        'description' => $_POST['edit_description'] ?? null,
        'location' => $_POST['edit_location'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    
    // Check for duplicate (excluding current record)
    $stmt = $conn->prepare("
        SELECT company_id FROM companies 
        WHERE company_name = ?  AND company_id != ?
    ");
    $stmt->execute([
        $required['company Name'],
        $required['id']
    ]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('A company with this company Name already exists.');
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE companies SET
            employer_id = ?,
            company_name = ?,
            description = ?,
            location = ?
        WHERE company_id = ?
    ");
    
    $success = $stmt->execute([
        $required['employee Name'],
        $required['company Name'],
        $required['description'],
        $required['location'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'company updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update company');
    }
}

function delete_company($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('company ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM companies WHERE company_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'company deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete company');
    }
}
?>