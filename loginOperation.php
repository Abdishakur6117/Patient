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

    // ---------- 1. Check in users table (username only) ----------
    $queryUser = 'SELECT * FROM users WHERE username = :username';
    $stmUser = $conn->prepare($queryUser);
    $stmUser->execute(['username' => $username]);
    $userData = $stmUser->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        if ($password == $userData['password']) {
            session_start();
            $_SESSION['user'] = $userData['username'];
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

    // ---------- If no match in users table ----------
    echo json_encode(["status" => "error", "message" => 'User not found']);
}
?>
