<?php
session_start();

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'staff') {
    // Redirect to login page if not logged in or not an Admin
    header("Location: login.php");
    exit();
}
require_once '../Connection/connection.php';

$db = new DatabaseConnection();
$pdo = $db->getConnection();

$customerId = $_GET['id'] ?? null;
if (!$customerId) {
    echo "ID-ga customer-ka waa mid aan sax ahayn.";
    exit();
}

$query = "
    SELECT 
        s.sale_id,
        s.customer_name,
        pr.product_id as product_id,
        pr.name AS product_name,
        s.sale_date,
        s.quantity,
        s.unit_price
    FROM sales s
    JOIN products pr ON s.product_id = pr.product_id
    WHERE s.sale_id = :sale_id
";

$stmt = $pdo->prepare($query);
$stmt->execute(['sale_id' => $customerId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$data) {
    echo "Xogta supplier-ka lama helin.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="so">
<head>
    <meta charset="UTF-8">
    <title>Customer Information:</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h2> Customer Information:</h2>
        
    </div>

    <?php foreach ($data as $index => $row): ?>
        <?php if ($index === 0): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['customer_name']) ?></h5>
                    <p><strong>Product Name:</strong> <?= htmlspecialchars($row['product_name']) ?></p>
                    <p><strong>Sale Date:</strong> <?= htmlspecialchars($row['sale_date']) ?></p>
                    <p><strong>Quantity:</strong> <?= htmlspecialchars($row['quantity']) ?></p>
                    <p><strong>Unit Price:</strong> <?= htmlspecialchars($row['unit_price']) ?></p>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <button onclick="window.print()" class="btn btn-primary no-print">
            <i class="fas fa-print"></i> Print Report
        </button>
</div>

<!-- JS -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- FontAwesome for print icon -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
