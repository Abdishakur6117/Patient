<?php
session_start();
require_once '../Connection/connection.php';

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'Admin') {
    // Redirect to login page if not logged in or not an Admin
    header("Location: login.php");
    exit();
}
$db = new DatabaseConnection();
$pdo = $db->getConnection();

$doctorId = $_GET['doctor_id'] ?? null;

if (!$doctorId) {
    echo "<div class='alert alert-danger mt-4'>Doctor ID not specified.</div>";
    exit();
}

// Query to get doctor details using parameterized query
$query = "
    SELECT 
        doctor_id,
        full_name,
        gender,
        date_of_birth,
        address,
        phone,
        email,
        specialty,
        created_at
    FROM doctors 
    WHERE doctor_id = :doctor_id
";

$stmt = $pdo->prepare($query);
$stmt->execute(['doctor_id' => $doctorId]);
$doctorInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctorInfo) {
    echo "<div class='alert alert-danger mt-4'>Doctor not found.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Doctor Medical Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
    @media print { .no-print { display: none !important; } }
    body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .header {
        background: #007bff; 
        color: white; 
        padding: 20px; 
        border-radius: 8px 8px 0 0;
        text-align: center;
        margin-bottom: 0;
    }
    .card {
        margin-top: 30px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .card-body p {
        font-size: 1.1rem;
    }
    .section-title {
        margin-top: 40px;
        font-weight: 600;
        color: #333;
    }
</style>
</head>
<body>

<div class="container mt-4 mb-5">
    <div class="card">
        <div class="header">
            <h2>Doctor Information</h2>
        </div>
        <div class="card-body">
            <p><strong>Doctor Name:</strong> <?= htmlspecialchars($doctorInfo['full_name']) ?><</p>
            <p><strong>Specialty:</strong> <?= htmlspecialchars($doctorInfo['specialty']) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($doctorInfo['gender']) ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($doctorInfo['date_of_birth']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($doctorInfo['address']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($doctorInfo['phone']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($doctorInfo['email']) ?></p>
            <p><small class="text-muted">Profile created on: <?= date('F d, Y', strtotime($doctorInfo['created_at'])) ?></small></p>
        </div>
    </div>

    <button onclick="window.print()" class="btn btn-primary no-print mt-4">Print Report</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
