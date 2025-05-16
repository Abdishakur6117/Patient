<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'get_customer':
            get_customer($conn);
            break;           
        case 'get_product':
            get_product($conn);
            break;           
        case 'display_saleDetail':
            display_saleDetail($conn);
            break;
            
        case 'create_saleDetail':
            create_saleDetail($conn);
            break;
            
        case 'update_saleDetail':
            update_saleDetail($conn);
            break;
            
        case 'delete_saleDetail':
            delete_saleDetail($conn);
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

function get_customer($conn) {
    $stmt = $conn->query("
        SELECT 
            sale_id, 
            customer_name 
        FROM Sales 
        ORDER BY customer_name
    ");
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
function get_product($conn) {
    $stmt = $conn->query("SELECT product_id, name as product_name FROM products ORDER BY name");
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}


function display_saleDetail($conn) {
    $query = "
        SELECT 
            sd.detail_id,
            s.sale_id,
            s.customer_name,
            pr.product_id,
            pr.name AS product_name,
            sd.quantity,
            sd.unit_price
        FROM SaleDetails sd
        JOIN Sales s ON sd.sale_id = s.sale_id
        JOIN products pr ON sd.product_id = pr.product_id
    ";
    
    $stmt = $conn->query($query);
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}

// function create_saleDetail($conn) {
//     $required = ['sale_id', 'product_id','quantity','unit_price'];
//     $data = [];
    
//     foreach ($required as $field) {
//         if (empty($_POST[$field])) {
//             throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
//         }
//         $data[$field] = $_POST[$field];
//     }
    
//     try {
//         $conn->beginTransaction();
    
//         // 1. Insert sale detail
//         $stmt = $conn->prepare("
//             INSERT INTO saleDetails (sale_id, product_id, quantity, unit_price)
//             VALUES (?, ?, ?, ?)
//         ");
//         $stmt->execute([
//             $data['sale_id'],
//             $data['product_id'],
//             $data['quantity'],
//             $data['unit_price']
//         ]);
    
//         // 2. Reduce stock
//         $stmt = $conn->prepare("
//             UPDATE products 
//             SET quantity_in_stock = quantity_in_stock - ? 
//             WHERE product_id = ?
//         ");
//         $stmt->execute([
//             $data['quantity'],
//             $data['product_id']
//         ]);
    
//         $conn->commit();
    
//         echo json_encode([
//             'status' => 'success',
//             'message' => 'Sale detail created and stock updated'
//         ]);
//     } catch (Exception $e) {
//         $conn->rollBack();
//         echo json_encode([
//             'status' => 'error',
//             'message' => $e->getMessage()
//         ]);
//     }
    
// }

function create_saleDetail($conn) {
    $required = ['sale_id', 'product_id','quantity','unit_price'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }
    
    try {
        $conn->beginTransaction();

        // Hubi in kaydka ku filan yahay
        $stmt = $conn->prepare("SELECT quantity_in_stock FROM products WHERE product_id = ?");
        $stmt->execute([$data['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Product not found.");
        }

        if ($product['quantity_in_stock'] < $data['quantity']) {
            throw new Exception("Insufficient stock for the selected product.");
        }

        // 1. Insert sale detail
        $stmt = $conn->prepare("
            INSERT INTO saleDetails (sale_id, product_id, quantity, unit_price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['sale_id'],
            $data['product_id'],
            $data['quantity'],
            $data['unit_price']
        ]);
    
        // 2. Reduce stock
        $stmt = $conn->prepare("
            UPDATE products 
            SET quantity_in_stock = quantity_in_stock - ? 
            WHERE product_id = ?
        ");
        $stmt->execute([
            $data['quantity'],
            $data['product_id']
        ]);
    
        $conn->commit();
    
        echo json_encode([
            'status' => 'success',
            'message' => 'Sale detail created and stock updated'
        ]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

// function update_saleDetail($conn) {
//     // Accept both 'edit_id' and 'id' as the identifier
//     $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
//     $required = [
//         'id'         => $id,
//         'sale_id'    => $_POST['edit_sale_id'] ?? null,
//         'product_id' => $_POST['edit_product_id'] ?? null,
//         'quantity'   => $_POST['edit_quantity'] ?? null,
//         'unit_price' => $_POST['edit_unit_price'] ?? null
//     ];
    
//     foreach ($required as $field => $value) {
//         if (empty($value)) {
//             throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
//         }
//     }
    
//     try {
//         $conn->beginTransaction();
    
//         // 1. Hel xogtii hore
//         $stmt = $conn->prepare("SELECT quantity, product_id FROM saleDetails WHERE detail_id = ?");
//         $stmt->execute([$required['id']]);
//         $old = $stmt->fetch(PDO::FETCH_ASSOC);
    
//         if (!$old) {
//             throw new Exception('Old sale detail not found');
//         }
    
//         $old_quantity   = (int)$old['quantity'];
//         $old_product_id = (int)$old['product_id'];
//         $new_quantity   = (int)$required['quantity'];
//         $new_product_id = (int)$required['product_id'];
    
//         // 2. Stock update
//         if ($old_product_id !== $new_product_id) {
//             // a) Ka dar product-kii hore
//             $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE product_id = ?");
//             $stmt->execute([$old_quantity, $old_product_id]);
    
//             // b) Ka jari product-ka cusub
//             $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock - ? WHERE product_id = ?");
//             $stmt->execute([$new_quantity, $new_product_id]);
//         } else {
//             // c) Haddii product uu isku mid yahay, farqiga xisaabi
//             $diff = $old_quantity - $new_quantity; // because stock was decreased on sale
//             $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE product_id = ?");
//             $stmt->execute([$diff, $new_product_id]);
//         }
    
//         // 3. Update sale detail
//         $stmt = $conn->prepare("
//             UPDATE saleDetails SET
//                 sale_id = ?,
//                 product_id = ?,
//                 quantity = ?,
//                 unit_price = ?
//             WHERE detail_id = ?
//         ");
//         $success = $stmt->execute([
//             $required['sale_id'],
//             $required['product_id'],
//             $required['quantity'],
//             $required['unit_price'],
//             $required['id']
//         ]);
    
//         if ($success) {
//             $conn->commit();
//             echo json_encode([
//                 'status' => 'success',
//                 'message' => 'Sale detail updated & stock adjusted successfully'
//             ]);
//         } else {
//             $conn->rollBack();
//             throw new Exception('Failed to update sale detail');
//         }
    
//     } catch (Exception $e) {
//         $conn->rollBack();
//         echo json_encode([
//             'status' => 'error',
//             'message' => $e->getMessage()
//         ]);
//     }
    
// }    
function update_saleDetail($conn) {
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;

    $required = [
        'id'         => $id,
        'sale_id'    => $_POST['edit_sale_id'] ?? null,
        'product_id' => $_POST['edit_product_id'] ?? null,
        'quantity'   => $_POST['edit_quantity'] ?? null,
        'unit_price' => $_POST['edit_unit_price'] ?? null
    ];

    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }

    try {
        $conn->beginTransaction();

        // 1. Hel xogtii hore
        $stmt = $conn->prepare("SELECT quantity, product_id FROM saleDetails WHERE detail_id = ?");
        $stmt->execute([$required['id']]);
        $old = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$old) {
            throw new Exception('Old sale detail not found');
        }

        $old_quantity   = (int)$old['quantity'];
        $old_product_id = (int)$old['product_id'];
        $new_quantity   = (int)$required['quantity'];
        $new_product_id = (int)$required['product_id'];

        // 2. Stock update
        if ($old_product_id !== $new_product_id) {
            // a) Ka dar product-kii hore
            $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE product_id = ?");
            $stmt->execute([$old_quantity, $old_product_id]);

            // b) Hel kaydka cusub
            $stmt = $conn->prepare("SELECT quantity_in_stock FROM products WHERE product_id = ?");
            $stmt->execute([$new_product_id]);
            $new_product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$new_product) {
                throw new Exception("New product not found.");
            }

            if ($new_product['quantity_in_stock'] < $new_quantity) {
                throw new Exception("Insufficient stock for the new product.");
            }

            // c) Ka jari product-ka cusub
            $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock - ? WHERE product_id = ?");
            $stmt->execute([$new_quantity, $new_product_id]);

        } else {
            // d) Haddii product uu isku mid yahay, xisaabi farqiga
            $diff = $new_quantity - $old_quantity;

            if ($diff > 0) {
                // Wax dheeraad ah baa la rabaa — hubi haddii uu stock ku filan yahay
                $stmt = $conn->prepare("SELECT quantity_in_stock FROM products WHERE product_id = ?");
                $stmt->execute([$new_product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product['quantity_in_stock'] < $diff) {
                    throw new Exception("Insufficient stock to increase quantity.");
                }

                // Ka jari farqiga
                $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock - ? WHERE product_id = ?");
                $stmt->execute([$diff, $new_product_id]);
            } else {
                // Haddii la dhimo ama la simo, ku dar farqiga
                $stmt = $conn->prepare("UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE product_id = ?");
                $stmt->execute([abs($diff), $new_product_id]);
            }
        }

        // 3. Update sale detail
        $stmt = $conn->prepare("
            UPDATE saleDetails SET
                sale_id = ?,
                product_id = ?,
                quantity = ?,
                unit_price = ?
            WHERE detail_id = ?
        ");
        $success = $stmt->execute([
            $required['sale_id'],
            $required['product_id'],
            $required['quantity'],
            $required['unit_price'],
            $required['id']
        ]);

        if ($success) {
            $conn->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Sale detail updated & stock adjusted successfully'
            ]);
        } else {
            $conn->rollBack();
            throw new Exception('Failed to update sale detail');
        }

    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function delete_saleDetail($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('sale Detail ID is required');
    }

    $detail_id = $_POST['id'];

    try {
        $conn->beginTransaction();

        // 1. Get old quantity & product_id
        $stmt = $conn->prepare("SELECT product_id, quantity FROM saleDetails WHERE detail_id = ?");
        $stmt->execute([$detail_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception('Sale detail not found');
        }

        $product_id = $row['product_id'];
        $quantity   = $row['quantity'];

        // 2. Delete sale detail
        $stmt = $conn->prepare("DELETE FROM saleDetails WHERE detail_id = ?");
        $stmt->execute([$detail_id]);

        // 3. Restore stock
        $stmt = $conn->prepare("
            UPDATE products 
            SET quantity_in_stock = quantity_in_stock + ? 
            WHERE product_id = ?
        ");
        $stmt->execute([$quantity, $product_id]);

        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Sale detail deleted and stock restored'
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