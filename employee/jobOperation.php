<?php
session_start(); 
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) {          
        case 'display_job':
            display_job($conn);
            break;
            
        case 'create_job':
            create_job($conn);
            break;
            
        case 'update_job':
            update_job($conn);
            break;
            
        case 'delete_job':
            delete_job($conn);
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
// function display_job($conn) {

//     $query = "
//         SELECT 
//             j.job_id,
//             u.user_id,
//             u.name as employee_name,
//             j.title,
//             j.description,
//             j.category,
//             j.location,
//             j.salary_range,
//             j.type,
//             j.created_at
//         FROM jobs   j
//         join users u on j.employer_id = u.user_id
//     ";
    
//     $stmt = $conn->query($query);
//     echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
// }

function display_job($conn) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not logged in'
        ]);
        return;
    }

    $userId = $_SESSION['user_id']; // ID-ga user-ka loginka ah

    $query = "
        SELECT 
            j.job_id,
            u.user_id,
            u.name AS employee_name,
            j.title,
            j.description,
            j.category,
            j.location,
            j.salary_range,
            j.type,
            j.created_at
        FROM jobs j
        JOIN users u ON j.employer_id = u.user_id
        WHERE j.employer_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([$userId]);
    
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// function create_job($conn) {


//     $required = ['employee_name', 'title','description','category','location','salary','type'];
//     $data = [];

//     foreach ($required as $field) {
//         if (empty($_POST[$field])) {
//             throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
//         }
//         $data[$field] = trim($_POST[$field]);
//     }

//     // Insert category with user_id
//     $stmt = $conn->prepare("
//         INSERT INTO jobs (employer_id,title, description,category, location,salary_range,type) 
//         VALUES (?, ?, ?, ?, ?, ?, ?)
//     ");
    
//     $success = $stmt->execute([
//         $data['employee_name'],
//         $data['title'],
//         $data['description'],
//         $data['category'],
//         $data['location'],
//         $data['salary'],
//         $data['type']
//     ]);
    
//     if ($success) {
//         echo json_encode([
//             'status' => 'success',
//             'message' => 'Job recorded successfully'
//         ]);
//     } else {
//         throw new Exception('Failed to record Job');
//     }
// }

function create_job($conn) {

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    $employer_id = $_SESSION['user_id'];

    $required = ['title', 'description', 'category', 'location', 'salary', 'type'];
    $data = [];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = trim($_POST[$field]);
    }

    // Insert job with user_id as employer_id
    $stmt = $conn->prepare("
        INSERT INTO jobs (employer_id, title, description, category, location, salary_range, type) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $success = $stmt->execute([
        $employer_id,
        $data['title'],
        $data['description'],
        $data['category'],
        $data['location'],
        $data['salary'],
        $data['type']
    ]);

    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Job recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record Job');
    }
}

function update_job($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'employee_name' => $_POST['edit_employee_name'] ?? null,
        'title' => $_POST['edit_title'] ?? null,
        'description' => $_POST['edit_description'] ?? null,
        'category' => $_POST['edit_category'] ?? null,
        'location' => $_POST['edit_location'] ?? null,
        'salary_range' => $_POST['edit_salary'] ?? null,
        'type' => $_POST['edit_type'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE jobs SET
            employer_id = ?,
            title = ?,
            description = ?,
            category = ?,
            location = ?,
            salary_range = ?,
            type = ?
        WHERE job_id = ?
    ");
    
    $success = $stmt->execute([
        $required['employee_name'],
        $required['title'],
        $required['description'],
        $required['category'],
        $required['location'],
        $required['salary_range'],
        $required['type'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'job updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update job');
    }
}

function delete_job($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Job ID is required');
    }

    $jobId = $_POST['id'];

    try {
        // Start transaction
        $conn->beginTransaction();

        // 1. Delete related applications first
        $appStmt = $conn->prepare("DELETE FROM applications WHERE job_id = ?");
        $appStmt->execute([$jobId]);

        // 2. Delete the job itself
        $jobStmt = $conn->prepare("DELETE FROM jobs WHERE job_id = ?");
        $success = $jobStmt->execute([$jobId]);

        if ($success) {
            // Commit transaction if both deletes succeed
            $conn->commit();
            echo json_encode([
                'status' => 'success',
                'message' => 'Job and related applications deleted successfully'
            ]);
        } else {
            // Rollback if job deletion fails
            $conn->rollBack();
            throw new Exception('Failed to delete job');
        }

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollBack();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
?>