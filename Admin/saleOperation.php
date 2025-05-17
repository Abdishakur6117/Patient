<?php
session_start();
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) {           
        case 'get_product':
            get_product($conn);
            break;
        case 'display_sale':
            display_sale($conn);
            break;
            
        case 'create_sale':
            create_sale($conn);
            break;
            
        case 'update_sale':
            update_sale($conn);
            break;
            
        case 'delete_sale':
            delete_sale($conn);
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

function get_product($conn) {
    $stmt = $conn->query("SELECT product_id, name, price FROM products ORDER BY name");
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
function display_sale($conn) {
    $query = "
        SELECT 
        s.sale_id,
        s.customer_name,
        p.product_id as product_id,
        p.name as product_name,
        s.sale_date,
        s.quantity,
        s.unit_price,
        u.user_id as user_id,
        u.username
        FROM Sales s
        JOIN users u ON s.user_id = u.user_id
        JOIN products p ON s.product_id = p.product_id
        
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}   
function create_sale($conn) {

    // Hubi user login
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in");
    }

    $user_id = $_SESSION['user_id'];

    // 1. Required fields
    $required = ['customer_name', 'product_id', 'sale_date', 'quantity', 'unit_price'];
    $data = [];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }

    $product_id = $data['product_id'];
    $quantity_requested = (int)$data['quantity'];

    // 2. Hubi in stock uu ku filan yahay
    $stmt = $conn->prepare("SELECT quantity_in_stock, name FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception("Product not found");
    }

    $stock_available = (int)$product['quantity_in_stock'];

    if ($quantity_requested > $stock_available) {
        throw new Exception("Insufficient stock for product: " . $product['name'] . ". Only $stock_available in stock.");
    }

    // 3. Haddii stock ku filan yahay, samee sale
    $conn->beginTransaction();

    try {
        // Insert into sales
        $stmt = $conn->prepare("
            INSERT INTO sales (customer_name, product_id, sale_date, quantity, unit_price, user_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['customer_name'],
            $data['product_id'],
            $data['sale_date'],
            $data['quantity'],
            $data['unit_price'],
            $user_id
        ]);

        // 4. Update product stock
        $stmt = $conn->prepare("
            UPDATE products SET quantity_in_stock = quantity_in_stock - ? WHERE product_id = ?
        ");
        $stmt->execute([$quantity_requested, $product_id]);

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Sale created successfully',
            'sale_id' => $conn->lastInsertId()
        ]);
    } catch (Exception $e) {
        $conn->rollBack();
        throw new Exception("Transaction failed: " . $e->getMessage());
    }
}
function update_sale($conn) {
    $purchase_id = $_POST['edit_id'] ?? $_POST['id'] ?? null;

    $required = [
        'id' => $purchase_id,
        'customer_name' => $_POST['edit_customer_name'] ?? null,
        'product_id' => $_POST['edit_product_id'] ?? null,
        'sale_date' => $_POST['edit_sale_date'] ?? null,
        'quantity' => (int)($_POST['edit_quantity'] ?? 0),
        'unit_price' => $_POST['edit_unit_price'] ?? null
    ];

    // 1. Get old quantity from Sales
    $stmt = $conn->prepare("SELECT quantity FROM Sales WHERE sale_id = ?");
    $stmt->execute([$required['id']]);
    $old_sale = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$old_sale) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sale record not found'
        ]);
        return;
    }

    $old_quantity = (int)$old_sale['quantity'];
    $new_quantity = $required['quantity'];
    $quantity_diff = $old_quantity - $new_quantity; // could be positive or negative

    // 2. Get current stock from Products
    $stmt = $conn->prepare("SELECT quantity_in_stock FROM Products WHERE product_id = ?");
    $stmt->execute([$required['product_id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Product not found'
        ]);
        return;
    }

    $current_stock = (int)$product['quantity_in_stock'];
    $adjusted_stock = $current_stock + $quantity_diff;

    if ($adjusted_stock < 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Insufficient stock to update. Only ' . $current_stock . ' available.'
        ]);
        return;
    }

    // 3. Update the Sale
    $stmt = $conn->prepare("
        UPDATE Sales SET
            customer_name = ?,
            product_id = ?,
            sale_date = ?,
            quantity = ?,
            unit_price = ?
        WHERE sale_id = ?
    ");
    $success = $stmt->execute([
        $required['customer_name'],
        $required['product_id'],
        $required['sale_date'],
        $new_quantity,
        $required['unit_price'],
        $required['id']
    ]);

    if ($success) {
        // 4. Update the stock
        $stmt = $conn->prepare("UPDATE Products SET quantity_in_stock = ? WHERE product_id = ?");
        $stmt->execute([$adjusted_stock, $required['product_id']]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Sale updated and stock adjusted successfully'
        ]);
    } else {
        throw new Exception('Failed to update Sale');
    }
}
function delete_sale($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Sale ID is required');
    }

    $sale_id = $_POST['id'];

    try {
        $conn->beginTransaction();

        // Hel product_id iyo quantity sale-ga laga tirayo
        $stmt = $conn->prepare("SELECT product_id, quantity FROM Sales WHERE sale_id = ?");
        $stmt->execute([$sale_id]);
        $sale = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sale) {
            throw new Exception('Sale not found');
        }

        $product_id = $sale['product_id'];
        $quantity = $sale['quantity'];

        // Dib ugu dar stock
        $stmt = $conn->prepare("UPDATE Products SET quantity_in_stock = quantity_in_stock + ? WHERE product_id = ?");
        $stmt->execute([$quantity, $product_id]);

        // Tirtir sale-ga
        $stmt = $conn->prepare("DELETE FROM Sales WHERE sale_id = ?");
        $stmt->execute([$sale_id]);

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Sale deleted and stock updated successfully.'
        ]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to delete sale: ' . $e->getMessage()
        ]);
    }
}
?>