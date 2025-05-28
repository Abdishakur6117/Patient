<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'get_job_seeker':
            get_job_seeker($conn);
            break;
        case 'display_userProfile':
            display_userProfile($conn);
            break;
            
        case 'create_userProfile':
            create_userProfile($conn);
            break;
            
        case 'update_userProfile':
            update_userProfile($conn);
            break;
            
        case 'delete_userProfile':
            delete_userProfile($conn);
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
function display_userProfile($conn) {
    $query = "
       SELECT 
            up.profile_id,
            u.user_id,
            u.name as job_seeker_name,
            up.phone,
            up.address,
            up.education,
            up.experience,
            up.skills
        FROM user_profiles   up
        join users u on up.user_id = u.user_id
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}


function create_userProfile($conn) {
    $required = ['jobSeeker_name', 'phone','address','education','experience','skills'];
    $data = [];
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }
    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO user_profiles 
        (user_id, phone,address,education,experience,skills ) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $success = $stmt->execute([
        $data['jobSeeker_name'],
        $data['phone'],
        $data['address'],
        $data['education'],
        $data['experience'],
        $data['skills']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'user profile recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record user profile');
    }
}

function update_userProfile($conn) {
    // Accept both 'edit_id' and 'id' as the identifier
    $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    
    $required = [
        'id' => $id,
        'job_seeker_name' => $_POST['edit_jobSeeker_name'] ?? null,
        'phone' => $_POST['edit_phone'] ?? null,
        'address' => $_POST['edit_address'] ?? null,
        'education' => $_POST['edit_education'] ?? null,
        'experience' => $_POST['edit_experience'] ?? null,
        'skill' => $_POST['edit_skills'] ?? null
    ];
    
    // Validate required fields
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    // Update record
    $stmt = $conn->prepare("
        UPDATE user_profiles SET
            user_id = ?,
            phone = ?,
            address = ?,
            education = ?,
            experience = ?,
            skills = ?
        WHERE profile_id = ?
    ");
    
    $success = $stmt->execute([
        $required['job_seeker_name'],
        $required['phone'],
        $required['address'],
        $required['education'],
        $required['experience'],
        $required['skill'],
        $required['id']
    ]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User Profile updated successfully'
        ]);
    } else {
        throw new Exception('Failed to update User Profile');
    }
}

function delete_userProfile($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('User Profile ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM user_profiles WHERE profile_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User Profile deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete User Profile');
    }
}
?>