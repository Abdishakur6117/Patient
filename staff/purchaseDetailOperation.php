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
        case 'get_product':
            get_product($conn);
            break;           
        case 'display_purchaseDetail':
            display_purchaseDetail($conn);
            break;
            
        case 'create_purchaseDetail':
            create_purchaseDetail($conn);
            break;
            
        case 'update_purchaseDetail':
            update_purchaseDetail($conn);
            break;
            
        case 'delete_purchaseDetail':
            delete_purchaseDetail($conn);
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
    $stmt = $conn->query("
        SELECT 
            p.purchase_id, 
            s.name AS supplier_name 
        FROM purchases p
        JOIN Suppliers  s ON p.supplier_id = s.supplier_id
        ORDER BY s.name
    ");
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
function get_product($conn) {
    $stmt = $conn->query("SELECT product_id, name as product_name , price,quantity_in_stock FROM products ORDER BY name");
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}


function display_purchaseDetail($conn) {
    $query = "
        SELECT 
            pd.detail_id,
            p.purchase_id,
            s.name AS supplier_name,
            pr.product_id,
            pr.name AS product_name,
            pd.quantity,
            pd.unit_price
        FROM Purchasedetails pd
        JOIN purchases p ON pd.purchase_id = p.purchase_id
        JOIN suppliers s ON p.supplier_id = s.supplier_id
        JOIN products pr ON pd.product_id = pr.product_id
    ";
    
    $stmt = $conn->query($query);
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

function create_purchaseDetail($conn) {
    $required = ['purchase_id', 'product_id','quantity','unit_price'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }

    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO Purchasedetails  
        (purchase_id, product_id, quantity, unit_price) 
        VALUES (?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['purchase_id'],
        $data['product_id'],
        $data['quantity'],
        $data['unit_price'],
    ]);

    if ($success) {
        $stmtStock = $conn->prepare("
        UPDATE products 
        SET quantity_in_stock = quantity_in_stock + ? 
        WHERE product_id = ?
        ");
        $stmtStock->execute([
            $data['quantity'],
            $data['product_id']
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Purchase Details recorded & stock updated successfully'
        ]);
    } else {
        throw new Exception('Failed to record Purchase Details ');
    }
}
function update_purchaseDetail($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'purchase_id' => $_POST['edit_purchase_id'] ?? null,
        'product_id' => $_POST['edit_product_id'] ?? null,
        'quantity' => $_POST['edit_quantity'] ?? null,
        'unit_price' => $_POST['edit_unit_price'] ?? null
    ];
    
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    
    try {
        $conn->beginTransaction();
    
        // 1. Hel quantity-kii hore
        $stmt = $conn->prepare("SELECT quantity, product_id FROM purchaseDetails WHERE detail_id = ?");
        $stmt->execute([$required['id']]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$old) {
            throw new Exception('Old purchase detail not found');
        }
    
        $old_quantity = (int)$old['quantity'];
        $old_product_id = (int)$old['product_id'];
        $new_quantity = (int)$required['quantity'];
        $new_product_id = (int)$required['product_id'];
    
        // 2. Haddii product ID is beddelay, update laba product
        if ($old_product_id !== $new_product_id) {
            // Ka jaro stock-kii hore product-kii hore
            $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock - ? WHERE product_id = ?");
            $stmt->execute([$old_quantity, $old_product_id]);
    
            // Ku dar stock cusub product cusub
            $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE product_id = ?");
            $stmt->execute([$new_quantity, $new_product_id]);
        } else {
            // 3. Haddii product-ka la mid yahay, xisaabi farqiga (diff)
            $diff = $new_quantity - $old_quantity;
            $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE product_id = ?");
            $stmt->execute([$diff, $new_product_id]);
        }
    
        // 4. Update purchaseDetails table
        $stmt = $conn->prepare("
            UPDATE purchaseDetails SET
                purchase_id = ?,
                product_id = ?,
                quantity = ?,
                unit_price = ?
            WHERE detail_id = ?
        ");
        $success = $stmt->execute([
            $required['purchase_id'],
            $required['product_id'],
            $required['quantity'],
            $required['unit_price'],
            $required['id']
        ]);
    
        if ($success) {
            $conn->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Purchase detail updated & stock adjusted successfully'
            ]);
        } else {
            $conn->rollBack();
            throw new Exception('Failed to update purchase detail');
        }
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}    
function delete_purchaseDetail($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Purchase Detail ID is required');
    }

    $detail_id = $_POST['id'];

    try {
        $conn->beginTransaction();

        // 1. Hel xogta detail ka hor inta aanad tirtirin (si aad stock uga jarto)
        $stmt = $conn->prepare("SELECT product_id, quantity FROM purchaseDetails WHERE detail_id = ?");
        $stmt->execute([$detail_id]);
        $detail = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$detail) {
            throw new Exception('Purchase detail not found');
        }

        $product_id = $detail['product_id'];
        $quantity = $detail['quantity'];

        // 2. Tirtir purchase detail
        $stmt = $conn->prepare("DELETE FROM purchaseDetails WHERE detail_id = ?");
        $success = $stmt->execute([$detail_id]);

        if ($success) {
            // 3. Ka jaro stock-ka alaabta
            $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock - ? WHERE product_id = ?");
            $stmt->execute([$quantity, $product_id]);

            $conn->commit();

            echo json_encode([
                'status' => 'success',
                'message' => 'Purchase detail deleted and stock updated successfully.'
            ]);
        } else {
            $conn->rollBack();
            throw new Exception('Failed to delete purchase detail');
        }

    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}




?>