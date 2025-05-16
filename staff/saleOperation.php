<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) {           
        case 'get_user':
            get_user($conn);
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

function get_user($conn) {
    $stmt = $conn->query("SELECT user_id, username FROM users ORDER BY username");
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
        u.user_id as user_id,
        u.username,
        s.sale_date,
        s.total_amount
        FROM Sales s
        JOIN users u ON s.user_id = u.user_id
        
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}


function create_sale($conn) {
    // 1. Required fields (except total_amount)
    $required = ['customer_name','user_id','sale_date', 'total_amount'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }
    
    $stmt = $conn->prepare("
        INSERT INTO sales (customer_name,user_id, sale_date, total_amount)
        VALUES (?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['customer_name'],
        $data['user_id'],
        $data['sale_date'],
        $data['total_amount']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Sale created successfully',
            'sale_id' => $conn->lastInsertId() // useful for creating saleDetails after this
        ]);
    } else {
        throw new Exception('Failed to create sale');
    }
}    

function update_sale($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $purchase_id = $_POST['edit_id'] ?? $_POST['id'] ?? null;

    // Define required fields
    $required = [
        'id' => $purchase_id,
        'customer_name' => $_POST['edit_customer_name'] ?? null,
        'user_id' => $_POST['edit_user_id'] ?? null,
        'sale_date' => $_POST['edit_sale_date'] ?? null,
        'total_amount' => $_POST['edit_total_amount'] ?? null
    ];

        // Update purchase main table
        $stmt = $conn->prepare("
            UPDATE Sales SET
                customer_name = ?,
                user_id = ?,
                sale_date = ?,
                total_amount = ?
            WHERE sale_id = ?
        ");
        $success= $stmt->execute([
            $required['customer_name'],
            $required['user_id'],
            $required['sale_date'],
            $required['total_amount'],
            $required['id']
        ]);

        if ($success) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Sales  updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update Sales ');
        }
}


function delete_sale($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Sale ID is required');
    }

    $sale_id = $_POST['id'];

    try {
        $conn->beginTransaction();

        // 1. Hel saleDetails
        $stmt = $conn->prepare("SELECT product_id, quantity FROM saleDetails WHERE sale_id = ?");
        $stmt->execute([$sale_id]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Dib ugu dar stock
        $stmtUpdate = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE product_id = ?");
        foreach ($details as $row) {
            $stmtUpdate->execute([$row['quantity'], $row['product_id']]);
        }

        // 3. Delete saleDetails
        $stmt = $conn->prepare("DELETE FROM saleDetails WHERE sale_id = ?");
        $stmt->execute([$sale_id]);

        // 4. Delete sale
        $stmt = $conn->prepare("DELETE FROM sales WHERE sale_id = ?");
        $stmt->execute([$sale_id]);

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Sale and its details deleted. Stock restored.'
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