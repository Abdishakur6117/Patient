<?php
session_start();
require_once 'Connection/db_connect.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'Patient') {
    header("Location: login.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

// Appointments count
$stmt1 = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ?");
$stmt1->bind_param("i", $patient_id);
$stmt1->execute();
$stmt1->bind_result($totalAppointments);
$stmt1->fetch();
$stmt1->close();

// Prescriptions count
$stmt2 = $conn->prepare("
    SELECT COUNT(*) 
    FROM prescriptions pr
    JOIN visits v ON pr.visit_id = v.visit_id
    JOIN appointments a ON v.appointment_id = a.appointment_id
    WHERE a.patient_id = ?
");
$stmt2->bind_param("i", $patient_id);
$stmt2->execute();
$stmt2->bind_result($totalPrescriptions);
$stmt2->fetch();
$stmt2->close();

// Payments count
$stmt3 = $conn->prepare("SELECT COUNT(*) FROM payments WHERE patient_id = ?");
$stmt3->bind_param("i", $patient_id);
$stmt3->execute();
$stmt3->bind_result($totalPayments);
$stmt3->fetch();
$stmt3->close();

?>


<!doctype html>
<html lang="en">
 
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/libs/css/style.css">
    <link rel="stylesheet" href="assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="assets/vendor/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="assets/vendor/charts/morris-bundle/morris.css">
    <link rel="stylesheet" href="assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendor/charts/c3charts/c3.css">
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <title>Patient Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .nav-left-sidebar .navbar-nav .nav-item {
            margin-top: 10px;
        }
    .nav-left-sidebar {
        height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .nav-left-sidebar.sidebar-dark {
        overflow-y: auto;
        height: 100vh;
        position: fixed;
    }

    .menu-list {
        padding-bottom: 100px;
    }

    </style>
</head>

<body>
    <!-- ============================================================== -->
    <!-- main wrapper -->
    <!-- ============================================================== -->
    <div class="dashboard-main-wrapper">
        <!-- ============================================================== -->
        <!-- navbar -->
        <!-- ============================================================== -->
        <div class="dashboard-header">
            <nav class="navbar navbar-expand-lg bg-white fixed-top">
                <a class="navbar-brand" href="patient_dashboard.php">Patient Management System</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse " id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto navbar-right-top">
                        <li class="nav-item">
                            <div id="custom-search" class="top-search-bar">
                                <input class="form-control" type="text" placeholder="Search..">
                            </div>
                        </li>
                        <li class="nav-item dropdown nav-user">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="assets/images/avatar-1.jpg" alt="" class="user-avatar-md rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <div class="nav-user-info">
                                    <h5 class="mb-0 text-white nav-user-name"><?php echo htmlspecialchars($_SESSION['user']); ?> </h5>
                                    <span class="status"></span><span class="ml-2"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                                </div>
                                <a class="dropdown-item" href="logout.php"><i class="fas fa-power-off mr-2"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <!-- ============================================================== -->
        <!-- end navbar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- left sidebar -->
        <!-- ============================================================== -->
        <div class="nav-left-sidebar sidebar-dark">
            <div class="menu-list">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav flex-column">

                            <!-- Dashboard -->
                            <li class="nav-item">
                                <a class="nav-link margin-top-10" href="patient_dashboard.php">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <!-- Appointment -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-appointment"
                                    aria-expanded="false" aria-controls="submenu-appointment">
                                    <i class="fas fa-calendar-check"></i> Appointments
                                </a>
                                <div id="submenu-appointment" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="Patient/appointment.php">List Appointments</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Prescription -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-prescription"
                                    aria-expanded="false" aria-controls="submenu-prescription">
                                    <i class="fas fa-file-prescription"></i> Prescriptions
                                </a>
                                <div id="submenu-prescription" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="Patient/prescription.php">List Prescriptions</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Payments -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-payments"
                                    aria-expanded="false" aria-controls="submenu-payments">
                                    <i class="fas fa-credit-card"></i> Payments
                                </a>
                                <div id="submenu-payments" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="Patient/payment.php">List Payments</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- end left sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- wrapper  -->
        <!-- ============================================================== -->
        <div class="dashboard-wrapper">
            <div class="dashboard-ecommerce">
                <div class="container-fluid dashboard-content ">
                    <!-- ============================================================== -->
                    <!-- pageheader  -->
                    <!-- ============================================================== -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="page-header">
                                <h2 class="pageheader-title"> Dashboard </h2>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- end pageheader  -->
                    <!-- ============================================================== -->

                    <div class="container mt-5">
                        <div class="row g-4">
                            <!-- Appointments -->
                            <div class="col-md-3">
                                <div class="card text-white bg-warning h-100">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">Appointments</h5>
                                            <h3><?php echo $totalAppointments; ?></h3>
                                        </div>
                                        <i class="fas fa-calendar-check fa-3x"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Prescriptions -->
                            <div class="col-md-3">
                                <div class="card text-white bg-secondary h-100 ">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">Prescriptions</h5>
                                            <h3><?php echo $totalPrescriptions; ?></h3>
                                        </div>
                                        <i class="fas fa-file-prescription fa-3x"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Payments -->
                            <div class="col-md-3">
                                <div class="card text-white bg-dark h-100 ">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">Payments</h5>
                                            <h3><?php echo $totalPayments; ?></h3>
                                        </div>
                                        <i class="fas fa-credit-card fa-3x"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>



                </div>
            </div>
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- end footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- end wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- end main wrapper  -->
    <!-- ============================================================== -->
    <!-- Optional JavaScript -->
    <!-- jquery 3.3.1 -->
    <script src="assets/vendor/jquery/jquery-3.3.1.min.js"></script>
    <!-- bootstap bundle js -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <!-- slimscroll js -->
    <script src="assets/vendor/slimscroll/jquery.slimscroll.js"></script>
    <!-- main js -->
    <script src="assets/libs/js/main-js.js"></script>
    <!-- chart chartist js -->
    <script src="assets/vendor/charts/chartist-bundle/chartist.min.js"></script>
    <!-- sparkline js -->
    <script src="assets/vendor/charts/sparkline/jquery.sparkline.js"></script>
    <!-- morris js -->
    <script src="assets/vendor/charts/morris-bundle/raphael.min.js"></script>
    <script src="assets/vendor/charts/morris-bundle/morris.js"></script>
    <!-- chart c3 js -->
    <script src="assets/vendor/charts/c3charts/c3.min.js"></script>
    <script src="assets/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
    <script src="assets/vendor/charts/c3charts/C3chartjs.js"></script>
    <script src="assets/libs/js/dashboard-ecommerce.js"></script>
</body>
 
</html>