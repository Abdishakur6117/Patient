<?php
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
        case 'get_user':
            get_user($conn);
            break;           
        case 'get_product':
            get_product($conn);
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
function get_user($conn) {
    $stmt = $conn->query("SELECT user_id, username FROM users ORDER BY username");
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
function get_product($conn) {
    $stmt = $conn->query("SELECT product_id, name FROM products ORDER BY name");
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

function display_purchase($conn) {
    $query = "
        SELECT 
            p.purchase_id,
            s.supplier_id as supplier_id,
            s.name as supplier_name,
            u.user_id as user_id,
            u.username as user_name,
            p.purchase_date,
            p.total_amount
        FROM purchases p
        JOIN suppliers s ON p.supplier_id = s.supplier_id
        JOIN users u ON p.user_id = u.user_id
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}


function create_purchase($conn) {
    // 1. Required fields (except total_amount)
    $required = ['supplier_id','user_id', 'purchase_date'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }

    // 2. Xisaabi total_amount ka yimid purchase details
    $total_amount = 0;
    if (isset($_POST['product_id'], $_POST['quantity'], $_POST['unit_price'])) {
        foreach ($_POST['product_id'] as $i => $product_id) {
            $qty = isset($_POST['quantity'][$i]) ? (int) $_POST['quantity'][$i] : 0;
            $price = isset($_POST['unit_price'][$i]) ? (float) $_POST['unit_price'][$i] : 0;
            if ($qty > 0 && $price > 0) {
                $total_amount += $qty * $price;
            }
        }
    } else {
        throw new Exception("Purchase items are required.");
    }

    // 3. Insert into purchases
    $stmt = $conn->prepare("
        INSERT INTO purchases 
        (supplier_id, user_id, purchase_date, total_amount) 
        VALUES (?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['supplier_id'],
        $data['user_id'],
        $data['purchase_date'],
        $total_amount
    ]);

    if ($success) {
        $purchase_id = $conn->lastInsertId();

        echo json_encode([
            'status' => 'success',
            'message' => 'Purchase recorded successfully',
            'purchase_id' => $purchase_id,
            'total_amount' => $total_amount
        ]);
    } else {
        throw new Exception('Failed to record purchase');
    }
}


function update_purchase($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $purchase_id = $_POST['edit_id'] ?? $_POST['id'] ?? null;

    // Define required fields
    $required = [
        'id' => $purchase_id,
        'supplier_id' => $_POST['edit_supplier_id'] ?? null,
        'user_id' => $_POST['edit_user_id'] ?? null,
        'purchase_date' => $_POST['edit_purchase_date'] ?? null
    ];

    // 2. Xisaabi total_amount ka yimid purchase details
    $total_amount = 0;
    if (isset($_POST['edit_product_id'], $_POST['edit_quantity'], $_POST['edit_unit_price'])) {
        foreach ($_POST['edit_product_id'] as $i => $product_id) {
            $qty = isset($_POST['edit_quantity'][$i]) ? (int) $_POST['edit_quantity'][$i] : 0;
            $price = isset($_POST['edit_unit_price'][$i]) ? (float) $_POST['edit_unit_price'][$i] : 0;
            if ($qty > 0 && $price > 0) {
                $total_amount += $qty * $price;
            }
        }
    } else {
        throw new Exception("Purchase items are required.");
    }

        // Update purchase main table
        $stmt = $conn->prepare("
            UPDATE purchases SET
                supplier_id = ?,
                user_id = ?,
                purchase_date = ?,
                total_amount =?
            WHERE purchase_id = ?
        ");
        $success= $stmt->execute([
            $required['supplier_id'],
            $required['user_id'],
            $required['purchase_date'],
            $total_amount,
            $required['id']
        ]);

        if ($success) {
            echo json_encode([
                'status' => 'success',
                'message' => 'purchase  updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update purchase ');
        }
}


function delete_purchase($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Purchase ID is required');
    }

    $purchase_id = $_POST['id'];

    try {
        $conn->beginTransaction();

        // 1. Soo hel purchaseDetails si aan u helno product_id & quantity (stock u baahan in laga jaro)
        $stmt = $conn->prepare("SELECT product_id, quantity FROM purchaseDetails WHERE purchase_id = ?");
        $stmt->execute([$purchase_id]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Dib uga jari stock-ka
        $stmtStock = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock - :quantity WHERE product_id = :product_id");
        foreach ($details as $item) {
            $stmtStock->execute([
                ':quantity' => $item['quantity'],
                ':product_id' => $item['product_id']
            ]);
        }

        // 3. Delete purchaseDetails
        $stmt = $conn->prepare("DELETE FROM purchaseDetails WHERE purchase_id = ?");
        $stmt->execute([$purchase_id]);

        // 4. Delete purchase
        $stmt = $conn->prepare("DELETE FROM purchases WHERE purchase_id = ?");
        $stmt->execute([$purchase_id]);

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Purchase and related details deleted successfully. Stock updated.'
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