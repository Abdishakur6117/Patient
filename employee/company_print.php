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

$companyId = $_GET['id'] ?? null;
if (!$companyId) {
    echo "<div class='alert alert-danger mt-4'>Company ID not found.</div>";
    exit();
}

// Query: Get company details, jobs posted by the company, and applications for those jobs (with resume)
$query = "
    SELECT 
        c.company_id,
        c.company_name,
        c.description AS company_description,
        c.location,
        j.job_id,
        j.title AS job_title,
        j.description AS job_description,
        j.created_at AS posted_date,
        a.application_id,
        a.status AS application_status,
        a.applied_at,
        a.resume AS resume_file,
        u.name AS applicant_name
    FROM companies c
    LEFT JOIN jobs j ON j.employer_id = c.employer_id
    LEFT JOIN applications a ON a.job_id = j.job_id
    LEFT JOIN users u ON a.user_id = u.user_id
    WHERE c.company_id = :company_id
    ORDER BY j.created_at DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute(['company_id' => $companyId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<div class='alert alert-warning mt-4'>No data found for this company.</div>";
    exit();
}

// Group jobs and applications by job_id
$companyInfo = [
    'company_name' => $data[0]['company_name'],
    'company_description' => $data[0]['company_description'],
    'location' => $data[0]['location'],
    'jobs' => []
];

foreach ($data as $row) {
    $jobId = $row['job_id'];
    if ($jobId) {
        if (!isset($companyInfo['jobs'][$jobId])) {
            $companyInfo['jobs'][$jobId] = [
                'job_title' => $row['job_title'],
                'job_description' => $row['job_description'],
                'posted_date' => $row['posted_date'],
                'applications' => []
            ];
        }
        if ($row['application_id']) {
            $companyInfo['jobs'][$jobId]['applications'][] = [
                'application_id' => $row['application_id'],
                'status' => $row['application_status'],
                'applied_at' => $row['applied_at'],
                'applicant_name' => $row['applicant_name'],
                'resume_file' => $row['resume_file'] ?? null
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Company and Job Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        @media print {
            .no-print { display: none !important; }
        }
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .company-header {
            background: #007bff;
            color: white;
            padding: 25px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .job-card {
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .job-header {
            background-color: #e9ecef;
            border-bottom: 1px solid #ddd;
            padding: 15px 20px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .job-title {
            font-weight: 600;
            font-size: 1.3rem;
            color: #333;
        }
        .posted-date {
            font-size: 0.9rem;
            color: #666;
        }
        .job-body {
            padding: 20px;
        }
        .application-list {
            list-style: none;
            padding-left: 0;
        }
        .application-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .application-list li:last-child {
            border-bottom: none;
        }
        .application-status {
            font-weight: 600;
            text-transform: capitalize;
            padding: 2px 8px;
            border-radius: 4px;
            color: white;
            font-size: 0.85rem;
            margin-left: 10px;
        }
        .status-pending { background-color: #ffc107; }
        .status-accepted { background-color: #28a745; }
        .status-rejected { background-color: #dc3545; }
        .btn-resume {
            font-size: 0.85rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<div class="container mt-4 mb-5">
    <div class="company-header text-center">
        <h2><?= htmlspecialchars($companyInfo['company_name']) ?></h2>
        <p class="lead"><?= htmlspecialchars($companyInfo['company_description']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($companyInfo['location']) ?></p>
    </div>
    <h4 class="mb-4">Posted Jobs:</h4>
    <?php if (count($companyInfo['jobs']) > 0): ?>
        <?php foreach ($companyInfo['jobs'] as $job): ?>
            <div class="card job-card">
                <div class="job-header d-flex justify-content-between align-items-center">
                    <div class="job-title"><?= htmlspecialchars($job['job_title']) ?></div>
                    <div class="posted-date">Posted: <?= date('d-M-Y', strtotime($job['posted_date'])) ?></div>
                </div>
                <div class="job-body">
                    <p><?= nl2br(htmlspecialchars($job['job_description'])) ?></p>
                    <h6>Job Applications:</h6>
                    <?php if (count($job['applications']) > 0): ?>
                        <ul class="application-list">
                            <?php foreach ($job['applications'] as $app): ?>
                                <li>
                                    Applicant Name: <strong><?= htmlspecialchars($app['applicant_name']) ?></strong>
                                    <span class="application-status status-<?= htmlspecialchars($app['status']) ?>">
                                        <?= ucfirst(htmlspecialchars($app['status'])) ?>
                                    </span>
                                    <br/>
                                    Date Applied: <?= date('d-M-Y H:i', strtotime($app['applied_at'])) ?>
                                    <br/>
                                    Resume: 
                                    <?php if (!empty($app['resume_file'])): ?>
                                        <a href="../uploads/resumes/<?= htmlspecialchars($app['resume_file']) ?>" target="_blank" class="btn btn-sm btn-outline-primary btn-resume" title="View Resume">
                                            📄 View Resume
                                        </a>
                                    <?php else: ?>
                                        <em>No resume uploaded</em>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p><em>No applications received for this job.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p><em>No jobs found for this company.</em></p>
    <?php endif; ?>

    <button onclick="window.print()" class="btn btn-primary no-print mt-4">Print Report</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
