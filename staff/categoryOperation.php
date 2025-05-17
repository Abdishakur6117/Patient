<?php
session_start(); 
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) {          
        case 'display_category':
            display_category($conn);
            break;
            
        case 'create_category':
            create_category($conn);
            break;
            
        case 'update_category':
            update_category($conn);
            break;
            
        case 'delete_category':
            delete_category($conn);
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

function display_category($conn) {

    if (empty($_SESSION['user_id'])) {
        throw new Exception('User is not logged in');
    }

    $user_id = $_SESSION['user_id'];

    $query = "
        SELECT 
            category_id,
            name,
            description
        FROM categories 
        WHERE user_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
function create_category($conn) {


    $required = ['Category_name', 'description'];
    $data = [];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = trim($_POST[$field]);
    }

    // Hubi in user login yahay
    if (empty($_SESSION['user_id'])) {
        throw new Exception('User is not logged in');
    }
    $user_id = $_SESSION['user_id'];

    // Check for duplicate category name
    $stmt = $conn->prepare("
        SELECT category_id FROM categories 
        WHERE LOWER(name) = LOWER(?)
    ");
    $stmt->execute([$data['Category_name']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('Category already exists with this name');
    }

    // Insert category with user_id
    $stmt = $conn->prepare("
        INSERT INTO categories (name, description, user_id) 
        VALUES (?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['Category_name'],
        $data['description'],
        $user_id
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Category recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record category');
    }
}


function update_category($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'category Name' => $_POST['edit_Category_name'] ?? null,
        'description' => $_POST['edit_description'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    
    // Check for duplicate (excluding current record)
    $stmt = $conn->prepare("
        SELECT category_id FROM categories 
        WHERE name = ?  AND category_id != ?
    ");
    $stmt->execute([
        $required['category Name'],
        $required['id']
    ]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('A Category with this Category Name already exists.');
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE categories SET
            name = ?,
            description = ?
        WHERE category_id = ?
    ");
    
    $success = $stmt->execute([
        $required['category Name'],
        $required['description'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'category updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update category');
    }
}

function delete_category($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Category ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'category deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete category');
    }
}
?>