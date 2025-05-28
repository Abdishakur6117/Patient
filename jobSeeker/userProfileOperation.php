<?php
session_start(); 
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
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


function display_userProfile($conn) {

    if (!isset($_SESSION['user_id'])) {
        echo json_encode([]);
        return;
    }

    $userId = $_SESSION['user_id'];

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
        FROM user_profiles up
        JOIN users u ON up.user_id = u.user_id
        WHERE up.user_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([$userId]);

    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)); // ✅ array response
}

function create_userProfile($conn) {

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    $required = ['phone', 'address', 'education', 'experience', 'skills'];
    $data = [];

    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
        $data[$field] = $_POST[$field];
    }

    $user_id = $_SESSION['user_id'];

    // Insert record
    $stmt = $conn->prepare("
        INSERT INTO user_profiles 
        (user_id, phone, address, education, experience, skills) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $success = $stmt->execute([
        $user_id,
        $data['phone'],
        $data['address'],
        $data['education'],
        $data['experience'],
        $data['skills']
    ]);

    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'User profile recorded successfully'
        ]);
    } else {
        throw new Exception('Failed to record user profile');
    }
}


function update_userProfile($conn) {

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Hel profile_id (profile-ka la update garaynayo)
    $profile_id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
    if (empty($profile_id)) {
        throw new Exception('Profile ID is required');
    }
    
    // Fields laga filayo POST
    $required = [
        'phone' => $_POST['edit_phone'] ?? null,
        'address' => $_POST['edit_address'] ?? null,
        'education' => $_POST['edit_education'] ?? null,
        'experience' => $_POST['edit_experience'] ?? null,
        'skills' => $_POST['edit_skills'] ?? null
    ];
    
    // Hubi dhammaan fields inay buuxaan
    foreach ($required as $field => $value) {
        if (empty($value)) {
            throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
        }
    }
    
    // Update statement: user_id lama beddelayo marka la update gareynayo, 
    // laakiin haddii aad rabto inaad xaqiijiso in user_id uu yahay midka session-ka, waad ku dari kartaa WHERE clause
    $stmt = $conn->prepare("
        UPDATE user_profiles SET
            phone = ?,
            address = ?,
            education = ?,
            experience = ?,
            skills = ?
        WHERE profile_id = ? AND user_id = ?
    ");
    
    $success = $stmt->execute([
        $required['phone'],
        $required['address'],
        $required['education'],
        $required['experience'],
        $required['skills'],
        $profile_id,
        $user_id
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