<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'get_category':
            get_category($conn);
            break;
        case 'display_product':
            display_product($conn);
            break;
            
        case 'create_product':
            create_product($conn);
            break;
            
        case 'update_product':
            update_product($conn);
            break;
            
        case 'delete_product':
            delete_product($conn);
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

function get_category($conn) {
    $stmt = $conn->query("SELECT category_id, name FROM categories ORDER BY name");
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

function display_product($conn) {
    $query = "
        SELECT 
            p.product_id,
            p.name as product_name,
            p.description,
            c.category_id as category_id,
            c.name as category_name,
            p.price,
            p.quantity_in_stock,
            p.created_at
        FROM products p 
        JOIN categories c  ON p.category_id = c.category_id
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function create_product($conn) {
    $required = ['product_name', 'description','category_id','price','quantity'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }

    // Check for duplicate username 
    $stmt = $conn->prepare("
        SELECT product_id FROM products 
        WHERE category_id = ?  
    ");
    $stmt->execute([$data['category_id']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('product record already exists for this category Name');
    }
    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO products 
        (name, description,category_id,price,quantity_in_stock) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['product_name'],
        $data['description'],
        $data['category_id'],
        $data['price'],
        $data['quantity']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'product recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record product');
    }
}

function update_product($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'product_name' => $_POST['edit_product_name'] ?? null,
        'description' => $_POST['edit_description'] ?? null,
        'category_id' => $_POST['edit_category_id'] ?? null,
        'price' => $_POST['edit_price'] ?? null,
        'quantity' => $_POST['edit_quantity'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    
    // Check for duplicate (excluding current record)
    $stmt = $conn->prepare("
        SELECT product_id FROM products 
        WHERE category_id = ?  AND product_id != ?
    ");
    $stmt->execute([
        $required['category_id'],
        $required['id']
    ]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('A product with this category name already exists.');
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE products SET
            name = ?,
            description = ?,
            category_id = ?,
            price = ?,
            quantity_in_stock = ?
        WHERE product_id = ?
    ");
    
    $success = $stmt->execute([
        $required['product_name'],
        $required['description'],
        $required['category_id'],
        $required['price'],
        $required['quantity'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'product updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update product');
    }
}

function delete_product($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('product ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'product deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete product');
    }
}
?>