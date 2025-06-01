<?php
session_start();
require_once '../Connection/connection.php';

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'Doctor') {
    // Redirect to login page if not logged in or not an Admin
    header("Location: login.php");
    exit();
}

$db = new DatabaseConnection();
$pdo = $db->getConnection();

$patientId = $_GET['patient_id'] ?? null;

if (!$patientId) {
    echo "<div class='alert alert-danger mt-4'>Patient ID not specified.</div>";
    exit();
}

// Query to get all patient details
$query = "
SELECT 
    p.patient_id, p.full_name, p.gender, p.date_of_birth, p.phone, p.email, p.address,p.created_at,
    a.appointment_id, a.appointment_date, a.status AS appointment_status, a.reason,
    v.visit_id, v.diagnosis, v.treatment, v.visit_date, v.charge,
    pr.prescription_id, pr.medication, pr.dosage, pr.duration,
    pay.payment_id, pay.amount, pay.paid_amount, pay.remainder, pay.payment_date, pay.status AS payment_status
FROM patients p
LEFT JOIN appointments a ON a.patient_id = p.patient_id
LEFT JOIN visits v ON v.appointment_id = a.appointment_id
LEFT JOIN prescriptions pr ON pr.visit_id = v.visit_id
LEFT JOIN payments pay ON pay.patient_id = p.patient_id
WHERE p.patient_id = :patient_id
ORDER BY a.appointment_date DESC, v.visit_date DESC, pay.payment_date DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute(['patient_id' => $patientId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<div class='alert alert-warning mt-4'>No data found for this patient.</div>";
    exit();
}

// Extract patient info
$patientInfo = [
    'patient_id' => $data[0]['patient_id'],
    'full_name' => $data[0]['full_name'],
    'gender' => $data[0]['gender'],
    'date_of_birth' => $data[0]['date_of_birth'],
    'phone' => $data[0]['phone'],
    'email' => $data[0]['email'],
    'address' => $data[0]['address'],
    'created_at' => $data[0]['created_at']
];

// Group appointments and payments
$appointments = [];
$payments = [];

foreach ($data as $row) {
    // Payments
    if ($row['payment_id'] && !isset($payments[$row['payment_id']])) {
        $payments[$row['payment_id']] = [
            'payment_date' => $row['payment_date'],
            'amount' => $row['amount'],
            'paid_amount' => $row['paid_amount'],
            'remainder' => $row['remainder'],
            'status' => $row['payment_status']
        ];
    }

    // Appointments and visits
    if ($row['appointment_id']) {
        if (!isset($appointments[$row['appointment_id']])) {
            $appointments[$row['appointment_id']] = [
                'appointment_date' => $row['appointment_date'],
                'status' => $row['appointment_status'],
                'reason' => $row['reason'],
                'visits' => []
            ];
        }

        if ($row['visit_id']) {
            if (!isset($appointments[$row['appointment_id']]['visits'][$row['visit_id']])) {
                $appointments[$row['appointment_id']]['visits'][$row['visit_id']] = [
                    'diagnosis' => $row['diagnosis'],
                    'treatment' => $row['treatment'],
                    'visit_date' => $row['visit_date'],
                    'charge' => $row['charge'],
                    'prescriptions' => []
                ];
            }

            if ($row['prescription_id']) {
                $appointments[$row['appointment_id']]['visits'][$row['visit_id']]['prescriptions'][] = [
                    'medication' => $row['medication'],
                    'dosage' => $row['dosage'],
                    'duration' => $row['duration']
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Patient Medical Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
    @media print { .no-print { display: none !important; } }
    body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .header { background: #007bff; color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
    .card { margin-bottom: 25px; }
    .section-title { margin-top: 40px; }
</style>
</head>
<body>

<div class="container mt-4 mb-5">
    <div class="card">
        <div class="header">
            <h2>Patient Information</h2>
        </div>
        <div class="card-body">
            <p><strong>Patient Name:</strong> <?= htmlspecialchars($patientInfo['full_name']) ?><</p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($patientInfo['gender']) ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($patientInfo['date_of_birth']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($patientInfo['address']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($patientInfo['phone']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($patientInfo['email']) ?></p>
            <p><small class="text-muted">Profile created on: <?= date('F d, Y', strtotime($patientInfo['created_at'])) ?></small></p>
        </div>
    </div>

    <!-- Appointments Section -->
    <h4 class="section-title">Appointments</h4>
    <?php if ($appointments): ?>
        <?php foreach ($appointments as $appt): ?>
            <div class="card">
                <div class="card-header">
                    <strong>Date:</strong> <?= htmlspecialchars($appt['appointment_date']) ?> |
                    <strong>Status:</strong> <?= htmlspecialchars($appt['status']) ?>
                </div>
                <div class="card-body">
                    <p><strong>Reason:</strong> <?= nl2br(htmlspecialchars($appt['reason'])) ?></p>
                    <?php if ($appt['visits']): ?>
                        <h5>Visits</h5>
                        <?php foreach ($appt['visits'] as $visit): ?>
                            <div class="border rounded p-3 mb-3">
                                <p><strong>Visit Date:</strong> <?= htmlspecialchars($visit['visit_date']) ?></p>
                                <p><strong>Diagnosis:</strong> <?= nl2br(htmlspecialchars($visit['diagnosis'])) ?></p>
                                <p><strong>Treatment:</strong> <?= nl2br(htmlspecialchars($visit['treatment'])) ?></p>
                                <p><strong>Charge:</strong> $<?= number_format($visit['charge'], 2) ?></p>

                                <?php if ($visit['prescriptions']): ?>
                                    <h6>Prescriptions</h6>
                                    <ul>
                                        <?php foreach ($visit['prescriptions'] as $presc): ?>
                                            <li>
                                                <strong>Medication:</strong> <?= htmlspecialchars($presc['medication']) ?> |
                                                <strong>Dosage:</strong> <?= htmlspecialchars($presc['dosage']) ?> |
                                                <strong>Duration:</strong> <?= htmlspecialchars($presc['duration']) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p><em>No prescriptions recorded.</em></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p><em>No visits recorded for this appointment.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p><em>No appointments found.</em></p>
    <?php endif; ?>

    <!-- Payments Section -->
    <h4 class="section-title">Payments</h4>
    <?php if ($payments): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Remainder</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $pay): ?>
                    <tr>
                        <td><?= htmlspecialchars($pay['payment_date']) ?></td>
                        <td>$<?= number_format($pay['amount'], 2) ?></td>
                        <td>$<?= number_format($pay['paid_amount'], 2) ?></td>
                        <td>$<?= number_format($pay['remainder'], 2) ?></td>
                        <td><?= htmlspecialchars($pay['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><em>No payments found.</em></p>
    <?php endif; ?>

    <button onclick="window.print()" class="btn btn-primary no-print mt-4">Print Report</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
