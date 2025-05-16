<?php
session_start();

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    // Redirect to login page if not logged in or not an Admin
    header("Location: login.php");
    exit();
}
require_once '../Connection/connection.php';

$db = new DatabaseConnection();
$pdo = $db->getConnection();

$supplierId = $_GET['id'] ?? null;
if (!$supplierId) {
    echo "ID-ga supplier-ka waa mid aan sax ahayn.";
    exit();
}

$query = "
    SELECT 
        s.supplier_id,
        s.name AS supplier_name,
        s.contact_person,
        s.phone,
        s.email,
        s.gender,
        s.address,
        p.purchase_id,
        p.purchase_date,
        pr.name AS product_name,
        pd.quantity,
        pd.unit_price
    FROM suppliers s
    LEFT JOIN purchases p ON s.supplier_id = p.supplier_id
    LEFT JOIN purchaseDetails pd ON p.purchase_id = pd.purchase_id
    LEFT JOIN products pr ON pd.product_id = pr.product_id
    WHERE s.supplier_id = :supplier_id
";

$stmt = $pdo->prepare($query);
$stmt->execute(['supplier_id' => $supplierId]);
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
    <title>Supplier Information:</title>
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
        <h2> Supplier Information:</h2>
        
    </div>

    <?php foreach ($data as $index => $row): ?>
        <?php if ($index === 0): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['supplier_name']) ?></h5>
                    <p><strong>Contact Person:</strong> <?= htmlspecialchars($row['contact_person']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                    <p><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($row['purchase_id']): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Purchase Details:<?= htmlspecialchars($row['purchase_id']) ?></h5>
                    <p><strong>Purchase Date:</strong> <?= htmlspecialchars($row['purchase_date']) ?></p>
                    <p><strong>Product Name:</strong> <?= htmlspecialchars($row['product_name']) ?></p>
                    <p><strong>Quantity:</strong> <?= htmlspecialchars($row['quantity']) ?></p>
                    <p><strong>Price:</strong> <?= htmlspecialchars($row['unit_price']) ?></p>
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
