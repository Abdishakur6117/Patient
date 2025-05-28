<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) {          
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

function display_user($conn) {
    $query = "
        SELECT 
            user_id,
            name,
            email,
            role,
            created_at
        FROM users 
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function create_user($conn) {
    $required = ['name','email', 'password','confirmPassword','role'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }

    // Check for duplicate name 
    $stmt = $conn->prepare("
        SELECT user_id FROM users 
        WHERE email = ?  
    ");
    $stmt->execute([$data['email']]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('users record already exists for this Email');
    }
     if ($data['password'] !== $data['confirmPassword']) {
    throw new Exception('Passwords do not match');
    }
    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO users 
        (name, email, password,role) 
        VALUES (?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['name'],
        $data['email'],
        $data['password'],
        $data['role']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'users recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record users');
    }
}

function update_user($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'name' => $_POST['edit_name'] ?? null,
        'email' => $_POST['edit_email'] ?? null,
        'role' => $_POST['edit_role'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    
    // Check for duplicate (excluding current record)
    $stmt = $conn->prepare("
        SELECT user_id FROM users 
        WHERE email = ?  AND user_id != ?
    ");
    $stmt->execute([
        $required['email'],
        $required['id']
    ]);
    if ($stmt->rowCount() > 0) {
        throw new Exception('A user with this Email already exists.');
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE users SET
            name = ?,
            email = ?,
            role = ?
        WHERE user_id = ?
    ");
    
    $success = $stmt->execute([
        $required['name'],
        $required['email'],
        $required['role'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'user updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update user');
    }
}

// function delete_user($conn) {
//     if (empty($_POST['id'])) {
//         throw new Exception('User ID is required');
//     }

//     $userId = $_POST['id'];

//     try {
//         // Start transaction
//         $conn->beginTransaction();

//         // 1. Hel job IDs uu leeyahay userkan (employer_id)
//         $jobsStmt = $conn->prepare("SELECT job_id FROM jobs WHERE employer_id = ?");
//         $jobsStmt->execute([$userId]);
//         $jobIds = $jobsStmt->fetchAll(PDO::FETCH_COLUMN);

//         // 2. Haddii uu jiro wax job ah, tirtiro applications-ka la xiriira
//         if (!empty($jobIds)) {
//             $inQuery = implode(',', array_fill(0, count($jobIds), '?'));
//             $deleteAppsStmt = $conn->prepare("DELETE FROM applications WHERE job_id IN ($inQuery)");
//             $deleteAppsStmt->execute($jobIds);
//         }

//         // 3. Tirtir jobs uu leeyahay user-ka
//         $deleteJobsStmt = $conn->prepare("DELETE FROM jobs WHERE employer_id = ?");
//         $deleteJobsStmt->execute([$userId]);

//         // 4. Tirtir companies uu leeyahay user-ka
//         $deleteCompaniesStmt = $conn->prepare("DELETE FROM companies WHERE employer_id = ?");
//         $deleteCompaniesStmt->execute([$userId]);

//         // 5. Ugu dambayn tirtir user-ka
//         $deleteUserStmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
//         $success = $deleteUserStmt->execute([$userId]);

//         if ($success) {
//             $conn->commit();
//             echo json_encode([
//                 'status' => 'success',
//                 'message' => 'User, their jobs, related applications, and companies deleted successfully'
//             ]);
//         } else {
//             $conn->rollBack();
//             throw new Exception('Failed to delete user');
//         }

//     } catch (Exception $e) {
//         $conn->rollBack();
//         echo json_encode([
//             'status' => 'error',
//             'message' => $e->getMessage()
//         ]);
//     }
// }
function delete_user($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('User ID is required');
    }

    $userId = $_POST['id'];

    try {
        // Start transaction
        $conn->beginTransaction();

        // 1. Delete applications uu sameeyay user-ka (job seeker)
        $deleteOwnApplications = $conn->prepare("DELETE FROM applications WHERE user_id = ?");
        $deleteOwnApplications->execute([$userId]);

        // 2. Delete user profile uu leeyahay (job seeker)
        $deleteProfile = $conn->prepare("DELETE FROM user_profiles WHERE user_id = ?");
        $deleteProfile->execute([$userId]);

        // 3. Hel job_ids uu user-ku leeyahay (haddii uu employer yahay)
        $jobsStmt = $conn->prepare("SELECT job_id FROM jobs WHERE employer_id = ?");
        $jobsStmt->execute([$userId]);
        $jobIds = $jobsStmt->fetchAll(PDO::FETCH_COLUMN);

        // 4. Delete applications-ka jobs-ka uu sameeyay (employer)
        if (!empty($jobIds)) {
            $inQuery = implode(',', array_fill(0, count($jobIds), '?'));
            $deleteJobApplications = $conn->prepare("DELETE FROM applications WHERE job_id IN ($inQuery)");
            $deleteJobApplications->execute($jobIds);
        }

        // 5. Delete jobs uu leeyahay
        $deleteJobs = $conn->prepare("DELETE FROM jobs WHERE employer_id = ?");
        $deleteJobs->execute([$userId]);

        // 6. Delete companies uu leeyahay
        $deleteCompanies = $conn->prepare("DELETE FROM companies WHERE employer_id = ?");
        $deleteCompanies->execute([$userId]);

        // 7. Ugu dambayn delete user-ka
        $deleteUser = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $success = $deleteUser->execute([$userId]);

        if ($success) {
            $conn->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'User and all related data deleted successfully (applications, jobs, companies, profile)'
            ]);
        } else {
            $conn->rollBack();
            throw new Exception('Failed to delete user');
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