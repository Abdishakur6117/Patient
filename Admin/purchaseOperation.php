<?php
session_start();
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'get_supplier':
            get_supplier($conn); 
            break;                  
        case 'display_purchase':
            display_purchase($conn);
            break;
            
        case 'create_purchase':
            create_purchase($conn);
            break;
            
        case 'update_purchase':
            update_purchase($conn);
            break;
            
        case 'delete_purchase':
            delete_purchase($conn);
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

function get_supplier($conn) {
    $stmt = $conn->query("SELECT supplier_id, name FROM suppliers ORDER BY name");
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

function display_purchase($conn) {

    if (empty($_SESSION['user_id'])) {
        throw new Exception('User is not logged in');
    }

    $user_id = $_SESSION['user_id'];

    $query = "
        SELECT 
            p.purchase_id,
            s.supplier_id,
            s.name AS supplier_name,
            pr.product_id,
            pr.name AS product_name,
            p.purchase_date,
            p.quantity,
            p.unit_price,
            u.user_id,
            u.username AS user_name
        FROM purchases p
        JOIN suppliers s ON p.supplier_id = s.supplier_id
        JOIN products pr ON p.product_id = pr.product_id
        JOIN users u ON p.user_id = u.user_id
        WHERE p.user_id = ?
        ORDER BY p.purchase_date DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function create_purchase($conn) {
    // 1. Required fields
    $required = ['supplier_id', 'product_name', 'purchase_date', 'quantity', 'unit_price'];
    $data = [];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = trim($_POST[$field]);
    }

    // 2. Optional fields
    $description = !empty($_POST['description']) ? trim($_POST['description']) : '';
    $category_name = !empty($_POST['category']) ? trim($_POST['category']) : null;

    // 3. Get user_id from session
    if (empty($_SESSION['user_id'])) {
        throw new Exception('User is not logged in');
    }
    $user_id = $_SESSION['user_id'];

    $quantity = (int)$data['quantity'];
    $unit_price = (float)$data['unit_price'];
    $product_name = strtolower(trim($data['product_name'])); // normalize name

    // 4. Check and/or insert category
    $category_id = null;
    if ($category_name) {
        $stmt = $conn->prepare("SELECT category_id FROM categories WHERE LOWER(name) = ?");
        $stmt->execute([strtolower($category_name)]);
        $existing_category = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_category) {
            $category_id = $existing_category['category_id'];
        } else {
            // Insert new category using the same description
            $insertCat = $conn->prepare("
                INSERT INTO categories (name, description) 
                VALUES (?, ?)
            ");
            $insertCat->execute([$category_name, $description]);
            $category_id = $conn->lastInsertId();
        }
    }

    // 5. Check if product already exists
    $stmt = $conn->prepare("SELECT * FROM products WHERE LOWER(name) = ?");
    $stmt->execute([$product_name]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Product exists → update quantity only
        $product_id = $product['product_id'];

        $update = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE product_id = ?");
        $update->execute([$quantity, $product_id]);
    } else {
        // Product does not exist → insert new product with description & category
        $insertProduct = $conn->prepare("
            INSERT INTO products (name, quantity_in_stock, price, description, category_id, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $insertProduct->execute([
            $product_name,
            $quantity,
            $unit_price,
            $description,    // Same description used here
            $category_id
        ]);

        $product_id = $conn->lastInsertId();
    }

    // 6. Insert purchase record
    $insertPurchase = $conn->prepare("
        INSERT INTO purchases (supplier_id, product_id, purchase_date, quantity, unit_price, user_id) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $success = $insertPurchase->execute([
        $data['supplier_id'],
        $product_id,
        $data['purchase_date'],
        $quantity,
        $unit_price,
        $user_id
    ]);

    if ($success) {
        $purchase_id = $conn->lastInsertId();

        echo json_encode([
            'status' => 'success',
            'message' => 'Purchase recorded successfully',
            'purchase_id' => $purchase_id,
        ]);
    } else {
        throw new Exception('Failed to record purchase');
    }
}

function update_purchase($conn) {
    // 1. Accept both 'edit_id' and 'id'
    $purchase_id = $_POST['edit_id'] ?? $_POST['id'] ?? null;

    // 2. Define required fields
    $supplier_id = $_POST['edit_supplier_id'] ?? null;
    $product_name = $_POST['edit_product_name'] ?? null;
    $purchase_date = $_POST['edit_purchase_date'] ?? null;
    $new_quantity = isset($_POST['edit_quantity']) ? (int)$_POST['edit_quantity'] : null;
    $unit_price = isset($_POST['edit_unit_price']) ? (float)$_POST['edit_unit_price'] : null;

    if (!$purchase_id || !$supplier_id || !$product_name || !$purchase_date || !$new_quantity || !$unit_price) {
        throw new Exception('All fields are required');
    }

    // 3. Normalize product name
    $product_name = strtolower(trim($product_name));

    // 4. Get existing purchase record
    $stmt = $conn->prepare("SELECT * FROM purchases WHERE purchase_id = ?");
    $stmt->execute([$purchase_id]);
    $existing_purchase = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing_purchase) {
        throw new Exception("Purchase record not found");
    }

    $old_quantity = (int)$existing_purchase['quantity'];
    $old_product_id = (int)$existing_purchase['product_id'];

    // 5. Get product ID from name
    $stmt = $conn->prepare("SELECT * FROM products WHERE LOWER(name) = ?");
    $stmt->execute([$product_name]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception("Product not found");
    }

    $product_id = $product['product_id'];

    // 6. Calculate quantity difference
    $quantity_difference = $new_quantity - $old_quantity;

    // 7. Update quantity_in_stock for product
    $update_stock = $conn->prepare("
        UPDATE products 
        SET quantity_in_stock = quantity_in_stock + ? 
        WHERE product_id = ?
    ");
    $update_stock->execute([$quantity_difference, $product_id]);

    // 8. Update purchase table
    $stmt = $conn->prepare("
        UPDATE purchases SET
            supplier_id = ?,
            product_id = ?,
            purchase_date = ?,
            quantity = ?,
            unit_price = ?
        WHERE purchase_id = ?
    ");

    $success = $stmt->execute([
        $supplier_id,
        $product_id,
        $purchase_date,
        $new_quantity,
        $unit_price,
        $purchase_id
    ]);

    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Purchase updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update purchase');
    }
}



function delete_purchase($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Purchase ID is required');
    }

    $purchase_id = $_POST['id'];

    try {
        $conn->beginTransaction();

        // 1. Hel product_id & quantity from purchases table
        $stmt = $conn->prepare("SELECT product_id, quantity FROM purchases WHERE purchase_id = ?");
        $stmt->execute([$purchase_id]);
        $purchase = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$purchase) {
            throw new Exception('Purchase not found');
        }

        $product_id = $purchase['product_id'];
        $quantity = (int)$purchase['quantity'];

        // 2. Update products.quantity_in_stock → subtract quantity
        $stmtStock = $conn->prepare("
            UPDATE products 
            SET quantity_in_stock = quantity_in_stock - ? 
            WHERE product_id = ?
        ");
        $stmtStock->execute([$quantity, $product_id]);

        // 3. Delete the purchase record
        $stmt = $conn->prepare("DELETE FROM purchases WHERE purchase_id = ?");
        $stmt->execute([$purchase_id]);

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Purchase deleted successfully and stock updated.'
        ]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}


?>