<?php
session_start();

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
function display_application($conn) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not logged in'
        ]);
        return;
    }

    $user_id = $_SESSION['user_id'];

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
        WHERE a.user_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id]);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
function create_application($conn) {

    if (!isset($_SESSION['user_id'])) {
        return [
            'status' => 'error',
            'message' => 'User not logged in'
        ];
    }

    $userId = $_SESSION['user_id'];

    try {
        // Fields laga rabo in la helo
        $required = ['job_name'];
        $data = [];

        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
            $data[$field] = $_POST[$field];
        }

        // Hubi in codsiga hore loo diiwaan geliyey (duplicate)
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM applications WHERE job_id = ? AND user_id = ?");
        $checkStmt->execute([$data['job_name'], $userId]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            throw new Exception('You have already applied for this job.');
        }

        // Hubi in faylka resume la soo diray
        if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Resume file is required');
        }

        $uploadDir = '../uploads/resumes/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }

        $allowedTypes = [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword'
        ];

        $fileType = $_FILES['resume']['type'];
        $fileSize = $_FILES['resume']['size'];
        $maxSize = 5 * 1024 * 1024;

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Only PDF and Word documents are allowed');
        }

        if ($fileSize > $maxSize) {
            throw new Exception('File size exceeds 5MB limit');
        }

        $fileInfo = pathinfo($_FILES['resume']['name']);
        $extension = strtolower($fileInfo['extension'] ?? '');
        $fileName = 'resume_user_' . $userId . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['resume']['tmp_name'], $targetPath)) {
            throw new Exception('Failed to save resume file');
        }

        // Geli codsiga database-ka - status default ah ayaa loo isticmaalayaa database-ka
        $stmt = $conn->prepare("
            INSERT INTO applications (job_id, user_id, resume, applied_at)
            VALUES (?, ?, ?, NOW())
        ");

        $success = $stmt->execute([
            $data['job_name'],
            $userId,
            $targetPath
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
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'User not logged in'
        ]);
        return;
    }

    $userId = $_SESSION['user_id'];

    try {
        // Hel xogaha POST
        $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
        $job_name = $_POST['edit_job_name'] ?? null;
        $current_resume = $_POST['current_resume'] ?? '';

        // Validation
        if (empty($id) || empty($job_name)) {
            throw new Exception('Job ID and Application ID are required');
        }

        // Hubi in userka uusan duplicate codsi dirin
        $checkStmt = $conn->prepare("
            SELECT COUNT(*) FROM applications 
            WHERE job_id = ? AND user_id = ? AND application_id != ?
        ");
        $checkStmt->execute([$job_name, $userId, $id]);
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            throw new Exception("You have already applied for this job.");
        }

        // Resume file update
        if (!empty($_FILES['edit_resume']['name'])) {
            $upload_dir = "../uploads/resumes/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $fileInfo = pathinfo($_FILES['edit_resume']['name']);
            $extension = strtolower($fileInfo['extension'] ?? '');
            $newResumeName = 'resume_user_' . $userId . '_' . time() . '.' . $extension;
            $target_path = $upload_dir . $newResumeName;

            if (!move_uploaded_file($_FILES['edit_resume']['tmp_name'], $target_path)) {
                throw new Exception("Failed to upload resume file");
            }

            // Delete old file if it's not empty and exists
            if (!empty($current_resume) && file_exists($current_resume)) {
                @unlink($current_resume);
            }

        } else {
            $target_path = $current_resume;
        }

        // Update database
        $stmt = $conn->prepare("
            UPDATE applications SET 
                job_id = ?, 
                user_id = ?, 
                resume = ?
            WHERE application_id = ?
        ");

        $success = $stmt->execute([
            $job_name,
            $userId,
            $target_path,
            $id
        ]);

        if ($success) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Application updated successfully'
            ]);
        } else {
            throw new Exception('Failed to update application');
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