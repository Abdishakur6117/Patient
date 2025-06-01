<?php
$route = $_REQUEST['url'] ?? null;

if (function_exists($route)) {
    $route();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid route."]);
}

function registration() {
    include 'Connection/connection.php';

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Collect and sanitize inputs
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $userName = trim($_POST['userName'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $DOB = $_POST['DOB'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $address = trim($_POST['address'] ?? '');

    // Individual validation
    if (!$fullName) {
        echo json_encode(["status" => "error", "message" => "Full name is required."]);
        return;
    }
    if (!$email) {
        echo json_encode(["status" => "error", "message" => "Email is required."]);
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        return;
    }
    if (!$userName) {
        echo json_encode(["status" => "error", "message" => "Username is required."]);
        return;
    }
    if (!$password) {
        echo json_encode(["status" => "error", "message" => "Password is required."]);
        return;
    }
    if (!$confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Confirm password is required."]);
        return;
    }
    if ($password !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        return;
    }
    if (!$DOB) {
        echo json_encode(["status" => "error", "message" => "Date of birth is required."]);
        return;
    }
    if (!$gender) {
        echo json_encode(["status" => "error", "message" => "Gender is required."]);
        return;
    }
    if (!$phone) {
        echo json_encode(["status" => "error", "message" => "Phone number is required."]);
        return;
    }
    if (!$address) {
        echo json_encode(["status" => "error", "message" => "Address is required."]);
        return;
    }

    try {
        $db = new DatabaseConnection();
        $conn = $db->getConnection();
    
        // Check if username already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute(['username' => $userName]);
    
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(["status" => "error", "message" => "Username already exists."]);
            return;
        }
    
        // Begin transaction
        $conn->beginTransaction();
    
        // Insert into patients
        $stmt1 = $conn->prepare("INSERT INTO patients (full_name, gender, date_of_birth, phone, email, address)
                                 VALUES (:full_name, :gender, :dob, :phone, :email, :address)");
        $stmt1->execute([
            'full_name' => $fullName,
            'dob'       => $DOB,
            'gender'    => $gender,
            'phone'     => $phone,
            'email'     => $email,
            'address'   => $address
        ]);
    
        // Get last inserted patient_id
        $patient_id = $conn->lastInsertId();
    
        // Insert into users with related_patient_id
        $stmt2 = $conn->prepare("INSERT INTO users (username, password, related_patient_id)
                                 VALUES (:username, :password, :related_patient_id)");
        $stmt2->execute([
            'username'           => $userName,
            'password'           => $password,
            'related_patient_id' => $patient_id
        ]);
    
        $conn->commit();
    
        echo json_encode(["status" => "success", "message" => "Signup successful!"]);
    
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode([
            "status" => "error",
            "message" => "Something went wrong.",
            "error_details" => $e->getMessage()
        ]);
    }
    
}

?>
