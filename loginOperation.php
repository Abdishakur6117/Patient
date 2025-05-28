<?php
$route = $_REQUEST['url'];
$route();

function login() {
    include 'Connection/connection.php';
    $db = new DatabaseConnection();
    $conn = $db->getConnection();

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email) {
        echo json_encode(["status" => "error", "message" => 'Email is required']);
        return;
    }
    if (!$password) {
        echo json_encode(["status" => "error", "message" => 'Password is required']);
        return;
    }

    // ---------- 1. Check in users table (username only) ----------
    $queryUser = 'SELECT * FROM users WHERE email = :email';
    $stmUser = $conn->prepare($queryUser);
    $stmUser->execute(['email' => $email]);
    $userData = $stmUser->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        if ($password == $userData['password']) {
            session_start();
            $_SESSION['user'] = $userData['email'];
            $_SESSION['user_id'] = $userData['user_id'];
            $_SESSION['role'] = $userData['role'];

            echo json_encode([
                "status" => "success",
                "message" => 'User logged in successfully',
                "role" => $userData['role']
            ]);
            return;
        } else {
            echo json_encode(["status" => "error", "message" => 'Incorrect password']);
            return;
        }
    }
}
?>
