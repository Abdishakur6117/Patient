<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) {          
        case 'get_doctor':
            get_doctor($conn);
            break;
        case 'display_user':
            display_user($conn);
            break;
            
        case 'create_user':
            create_user($conn);
            break;
            
        case 'update_user':
            update_user($conn);
            break;
            
        case 'delete_user':
            delete_user($conn);
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
function get_doctor($conn) {
    $stmt = $conn->query("
        SELECT 
            doctor_id, 
            full_name as doctor_name
        FROM doctors
    ");
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
function display_user($conn) {
    $query = "
        SELECT 
            user_id,
            username,
            role,
            status,
            created_at
        FROM users 
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
function create_user($conn) {
    $required = ['username', 'password', 'confirmPassword', 'role', 'status'];
    $optional = ['related_doctor_id', 'related_patient_id'];
    $data = [];

    // Hubi required fields
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = trim($_POST[$field]);
    }

    // Qaado optional fields (haddii la soo diro)
    foreach ($optional as $field) {
        // Haddii POST ka yimaado oo madhan yahay, u dhig null si DB u fahmo
        $data[$field] = (isset($_POST[$field]) && $_POST[$field] !== '') ? $_POST[$field] : null;
    }

    // Check for duplicate username
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $stmt->execute([$data['username']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('User record already exists for this username');
    }

    // Check passwords match
    if ($data['password'] !== $data['confirmPassword']) {
        throw new Exception('Passwords do not match');
    }

    // Prepare insert query
    $stmt = $conn->prepare("
        INSERT INTO users 
        (username, password, role, status, related_doctor_id, related_patient_id) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    // Execute query with data, make sure doctor_id or patient_id is NULL if empty
    $success = $stmt->execute([
        $data['username'],
        $data['password'],
        $data['role'],
        $data['status'],
        $data['related_doctor_id'] !== null ? $data['related_doctor_id'] : null,
        $data['related_patient_id'] !== null ? $data['related_patient_id'] : null
    ]);

    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record user');
    }
}

// function update_user($conn) {
//     // Accept both 'edit_id' and 'id' as the identifier
//     $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
//     $required = [
//         'id' => $id,
//         'username' => $_POST['edit_username'] ?? null,
//         'role' => $_POST['edit_role'] ?? null,
//         'status' => $_POST['edit_status'] ?? null
//     ];
    
//     // Validate required fields
//     foreach ($required as $field => $value) {
//         if (empty($value)) {
//             throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
//         }
//     }
    
//     // Check for duplicate (excluding current record)
//     $stmt = $conn->prepare("
//         SELECT user_id FROM users 
//         WHERE username = ?  AND user_id != ?
//     ");
//     $stmt->execute([
//         $required['username'],
//         $required['id']
//     ]);
//     if ($stmt->rowCount() > 0) {
//         throw new Exception('A user with this username already exists.');
//     }
//     // Update record
//     $stmt = $conn->prepare("
//         UPDATE users SET
//             username = ?,
//             role = ?,
//             status = ?
//         WHERE user_id = ?
//     ");
    
//     $success = $stmt->execute([
//         $required['username'],
//         $required['role'],
//         $required['status'],
//         $required['id']
//     ]);
    
//     if ($success) {
//         echo json_encode([
//             'status' => 'success',
//             'message' => 'user updated successfully'
//         ]);
//     } else {
//         throw new Exception('Failed to update user');
//     }
// }
function update_user($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'username' => $_POST['edit_username'] ?? null,
        'role' => $_POST['edit_role'] ?? null,
        'status' => $_POST['edit_status'] ?? null
    ];
    
    $optional = [
        'related_doctor_id' => $_POST['edit_related_doctor_id'] ?? null,
        'related_patient_id' => $_POST['edit_related_patient_id'] ?? null
    ];

    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }

    // Validate related_patient_id exists or null
    if (!empty($optional['related_patient_id'])) {
        $stmtCheckPatient = $conn->prepare("SELECT patient_id FROM patients WHERE patient_id = ?");
        $stmtCheckPatient->execute([$optional['related_patient_id']]);
        if ($stmtCheckPatient->rowCount() === 0) {
            throw new Exception('Invalid related_patient_id: patient does not exist');
        }
    } else {
        $optional['related_patient_id'] = null;
    }

    // Validate related_doctor_id exists or null
    if (!empty($optional['related_doctor_id'])) {
        $stmtCheckDoctor = $conn->prepare("SELECT doctor_id FROM doctors WHERE doctor_id = ?");
        $stmtCheckDoctor->execute([$optional['related_doctor_id']]);
        if ($stmtCheckDoctor->rowCount() === 0) {
            throw new Exception('Invalid related_doctor_id: doctor does not exist');
        }
    } else {
        $optional['related_doctor_id'] = null;
    }
    
    // Check for duplicate username excluding current record
    $stmt = $conn->prepare("
        SELECT user_id FROM users 
        WHERE username = ? AND user_id != ?
    ");
    $stmt->execute([
        $required['username'],
        $required['id']
    ]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('A user with this username already exists.');
    }

    // Update record including related ids
    $stmt = $conn->prepare("
        UPDATE users SET
            username = ?,
            role = ?,
            status = ?,
            related_doctor_id = ?,
            related_patient_id = ?
        WHERE user_id = ?
    ");
    
    $success = $stmt->execute([
        $required['username'],
        $required['role'],
        $required['status'],
        $optional['related_doctor_id'],
        $optional['related_patient_id'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update user');
    }
}


function delete_user($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('User  ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Users  deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete Users ');
    }
}


?>