<?php
$route = $_REQUEST['url'];
$route();

function login() {
    include 'Connection/connection.php';
    $db = new DatabaseConnection();
    $conn = $db->getConnection();

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$username) {
        echo json_encode(["status" => "error", "message" => 'Username is required']);
        return;
    }
    if (!$password) {
        echo json_encode(["status" => "error", "message" => 'Password is required']);
        return;
    }

    // 1. Fetch user by username
    $queryUser = 'SELECT * FROM users WHERE username = :username';
    $stmUser = $conn->prepare($queryUser);
    $stmUser->execute(['username' => $username]);
    $userData = $stmUser->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        echo json_encode(["status" => "error", "message" => 'User not found']);
        return;
    }

    // 2. Compare plain text passwords directly (Not secure!)
    if ($password === $userData['password']) {
        session_start();
        $_SESSION['user'] = $userData['username'];
        $_SESSION['user_id'] = $userData['user_id'];
        $_SESSION['role'] = $userData['role'];
        
        // Optionally save patient_id or doctor_id if needed
        if ($userData['role'] === 'Patient') {
            $_SESSION['patient_id'] = $userData['related_patient_id'] ?? null;
        } elseif ($userData['role'] === 'Doctor') {
            $_SESSION['doctor_id'] = $userData['related_doctor_id'] ?? null;
        }

        echo json_encode([
            "status" => "success",
            "message" => 'User logged in successfully',
            "role" => $userData['role']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => 'Incorrect password']);
    }
}
?>
