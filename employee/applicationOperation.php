<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'get_job':
            get_job($conn);
            break;
        case 'get_job_seeker':
            get_job_seeker($conn);
            break;
        case 'display_application':
            display_application($conn);
            break;
        case 'create_application':
            $result = create_application($conn);
            echo json_encode($result);
            break;
        case 'update_application':
            update_application($conn);
            break;
        case 'delete_application':
            delete_application($conn);
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

function get_job($conn) {
    $stmt = $conn->query("
        SELECT 
            job_id, 
            title as job_name
        FROM jobs 
    ");
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
function get_job_seeker($conn) {
    $stmt = $conn->query("
        SELECT 
            user_id, 
            name as job_seeker_name
        FROM users where role='job_seeker' 
    ");
    
    echo json_encode([
        'status' => 'success',
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
}
// function display_application($conn) {
//     $query = "
//        SELECT 
//             a.application_id,
//             j.job_id,
//             j.title as job_name,
//             u.user_id,
//             u.name as job_seeker_name,
//             a.resume,
//             a.applied_at,
//             a.status
//         FROM applications a
//         JOIN jobs j ON a.job_id = j.job_id
//         JOIN users u ON a.user_id = u.user_id
//     ";
    
//     $stmt = $conn->query($query);
//     echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
// }
function display_application($conn) {
    session_start();

    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not logged in'
        ]);
        return;
    }

    $employer_id = $_SESSION['user_id'];

    $query = "
        SELECT 
            a.application_id,
            j.job_id,
            j.title AS job_name,
            u.user_id,
            u.name AS job_seeker_name,
            a.resume,
            a.applied_at,
            a.status
        FROM applications a
        JOIN jobs j ON a.job_id = j.job_id
        JOIN users u ON a.user_id = u.user_id
        WHERE j.employer_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([$employer_id]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function create_application($conn) {
    try {
        // Fields laga rabo in la helo
        $required = ['job_name', 'job_seeker_name', 'status'];
        $data = [];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
            $data[$field] = $_POST[$field];
        }

        // Hubi in codsiga hore loo diiwaan geliyey (duplicate)
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM applications WHERE job_id = ? AND user_id = ?");
        $checkStmt->execute([$data['job_name'], $data['job_seeker_name']]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            throw new Exception('You have already applied for this job.');
        }

        // Hubi in faylka resume la soo diray
        if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Resume file is required');
        }

        // Directory-ga upload-ka
        $uploadDir = '../uploads/resumes/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }

        // Noocyada file-ka la oggol yahay
        $allowedTypes = [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword'
        ];

        $fileType = $_FILES['resume']['type'];
        $fileSize = $_FILES['resume']['size'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Only PDF and Word documents are allowed');
        }

        if ($fileSize > $maxSize) {
            throw new Exception('File size exceeds 5MB limit');
        }

        // Magaca faylka oo ka kooban magaca job seeker nadiif ah + waqtiga
        $fileInfo = pathinfo($_FILES['resume']['name']);
        $extension = strtolower($fileInfo['extension'] ?? '');
        $safeName = preg_replace('/[^a-zA-Z0-9-_]/', '_', $data['job_seeker_name']);
        $fileName = 'resume_' . $safeName . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['resume']['tmp_name'], $targetPath)) {
            throw new Exception('Failed to save resume file');
        }

        // Geli codsiga database-ka
        $stmt = $conn->prepare("
            INSERT INTO applications (job_id, user_id, resume, status, applied_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $success = $stmt->execute([
            $data['job_name'],
            $data['job_seeker_name'],
            $targetPath,
            $data['status']
        ]);

        if ($success) {
            return [
                'status' => 'success',
                'message' => 'Application submitted successfully',
                'resume_path' => $targetPath
            ];
        } else {
            if (file_exists($targetPath)) {
                unlink($targetPath);
            }
            throw new Exception('Failed to save application to database');
        }

    } catch (Exception $e) {
        if (isset($targetPath) && file_exists($targetPath)) {
            @unlink($targetPath);
        }

        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

function update_application($conn) {
    try {
        // Hel xogaha POST
        $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
        $status = $_POST['edit_status'] ?? null;

        // Validation
        if (empty($id)) {
            throw new Exception('Application ID is required');
        }
        if (empty($status)) {
            throw new Exception('Status is required');
        }

        // Update kaliya status-ka
        $stmt = $conn->prepare("
            UPDATE applications SET 
                status = ?
            WHERE application_id = ?
        ");

        $success = $stmt->execute([$status, $id]);

        if ($success) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Application status updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update application status');
        }

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function delete_application($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Application  ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM applications WHERE application_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Application  deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete Application ');
    }
}
?>