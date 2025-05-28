<?php
session_start();
require_once '../Connection/connection.php';

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'employee') {
    // Redirect to login page if not logged in or not an Admin
    header("Location: login.php");
    exit();
}
$db = new DatabaseConnection();
$pdo = $db->getConnection();

$profileId = $_GET['id'] ?? null;

if (!$profileId) {
    echo "<div class='alert alert-danger'>Job Seeker ID not found.</div>";
    exit();
}

// 1. Get job seeker details
$query = "
    SELECT 
        u.name, u.email, up.phone, up.address, up.education, up.experience, up.skills, u.user_id
    FROM user_profiles up
    JOIN users u ON up.user_id = u.user_id
    WHERE up.profile_id = :profile_id
";
$stmt = $pdo->prepare($query);
$stmt->execute(['profile_id' => $profileId]);
$jobSeeker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jobSeeker) {
    echo "<div class='alert alert-warning'>Job Seeker not found.</div>";
    exit();
}

// 2. Get job applications for this job seeker
$appQuery = "
    SELECT 
        j.title AS job_title,
        j.description AS job_description,
        a.applied_at,
        a.status,
        a.resume
    FROM applications a
    JOIN jobs j ON a.job_id = j.job_id
    WHERE a.user_id = :user_id
    ORDER BY a.applied_at DESC
";
$stmt = $pdo->prepare($appQuery);
$stmt->execute(['user_id' => $jobSeeker['user_id']]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Seeker Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        @media print {
            .no-print { display: none; }
        }
        body {
            background-color: #f5f5f5;
        }
        .report-container {
            background: white;
            padding: 30px;
            margin: 30px auto;
            border-radius: 10px;
            max-width: 900px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        .section-title {
            margin-top: 30px;
            font-weight: 600;
        }
        .status-pending { color: #ffc107; font-weight: bold; }
        .status-accepted { color: #28a745; font-weight: bold; }
        .status-rejected { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
<div class="report-container">
    <h2 class="text-center">Job Seeker Profile Report</h2>
    <hr>

    <h4>Personal Information</h4>
    <p><strong>Name:</strong> <?= htmlspecialchars($jobSeeker['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($jobSeeker['email']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($jobSeeker['phone']) ?></p>
    <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($jobSeeker['address'])) ?></p>
    <p><strong>Education:</strong> <?= nl2br(htmlspecialchars($jobSeeker['education'])) ?></p>
    <p><strong>Experience:</strong> <?= nl2br(htmlspecialchars($jobSeeker['experience'])) ?></p>
    <p><strong>Skills:</strong> <?= nl2br(htmlspecialchars($jobSeeker['skills'])) ?></p>

    <h4 class="section-title">Job Applications</h4>
    <?php if (count($applications) > 0): ?>
        <ul class="list-group">
            <?php foreach ($applications as $app): ?>
                <li class="list-group-item">
                    <h5><?= htmlspecialchars($app['job_title']) ?></h5>
                    <p><?= nl2br(htmlspecialchars($app['job_description'])) ?></p>
                    <p><strong>Applied At:</strong> <?= date('d-M-Y H:i', strtotime($app['applied_at'])) ?></p>
                    <p>
                        <strong>Status:</strong> 
                        <span class="status-<?= $app['status'] ?>"><?= ucfirst($app['status']) ?></span>
                    </p>
                    <p>
                        <strong>Resume:</strong> 
                        <?php if (!empty($app['resume'])): ?>
                            <a href="../uploads/resumes/<?= htmlspecialchars($app['resume']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                📄 View Resume
                            </a>
                        <?php else: ?>
                            <em>No resume uploaded</em>
                        <?php endif; ?>
                    </p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p><em>No job applications submitted.</em></p>
    <?php endif; ?>

    <button onclick="window.print()" class="btn btn-primary no-print mt-4">Print Report</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
