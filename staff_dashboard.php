<?php
session_start();
require_once 'Connection/db_connect.php';

// Hubi haddii isticmaaluhu uu galo oo uu leeyahay doorarka 'staff'
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'staff') {
    // Haddii uusan galo ama uusan ahayn 'staff', ku celiso bogga login
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];  // Isticmaal ID-ga isticmaalaha ee session-ka

// Soo hel tirada categories, products, purchases, iyo sales ee isticmaalaha
$totalCategories = $conn->query("SELECT COUNT(*) FROM categories WHERE user_id = $userId")->fetch_row()[0];
$totalProducts = $conn->query("SELECT COUNT(*) FROM products WHERE user_id = $userId")->fetch_row()[0];
$totalPurchases = $conn->query("SELECT COUNT(*) FROM purchases WHERE user_id = $userId")->fetch_row()[0];
$totalSales = $conn->query("SELECT COUNT(*) FROM sales WHERE user_id = $userId")->fetch_row()[0];

// Hubi in isticmaalaha uu leeyahay doorarka 'staff' (table name: users, column name: role)
$query = "SELECT role FROM users WHERE user_id = $userId";
$result = $conn->query($query);

if ($result) {
    $userRole = $result->fetch_row()[0];
    if ($userRole != 'staff') {
        // Haddii doorarka aysan ahayn 'staff', ku celiso bogga login
        header("Location: login.php");
        exit();
    }
} else {
    echo "Error in fetching role: " . $conn->error;
    exit();
}

?>





<!doctype html>
<html lang="en">
 
<>
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
    <title>Stock Management System</title>
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
                <a class="navbar-brand" href="staff_dashboard.php">Stock Management System</a>
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
                            <li class="nav-item">
                                <a class="nav-link margin-top-10" href="staff_dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>
                            <!-- categories -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-category"
                                    aria-expanded="false" aria-controls="submenu-category">
                                    <i class="fas fa-boxes"></i> Category
                                </a>
                                <div id="submenu-category" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="staff/Category.php">List Category</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- Products -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-products"
                                    aria-expanded="false" aria-controls="submenu-products">
                                    <i class="fas fa-boxes"></i> Products
                                </a>
                                <div id="submenu-products" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="staff/Products.php">List Products</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <!-- Purchases -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-purchases"
                                    aria-expanded="false" aria-controls="submenu-purchases">
                                    <i class="fas fa-shopping-cart"></i> Purchases
                                </a>
                                <div id="submenu-purchases" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="staff/Purchase.php">List Purchases</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>


                            <!-- Sales -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-sales"
                                    aria-expanded="false" aria-controls="submenu-sales">
                                    <i class="fas fa-dollar-sign"></i> Sales
                                </a>
                                <div id="submenu-sales" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="staff/Sale.php">List Sales</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- report -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-sale-details"
                                    aria-expanded="false" aria-controls="submenu-sale-details">
                                    <i class="fas fa-receipt"></i> Report
                                </a>
                                <div id="submenu-sale-details" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="staff/customerReport.php">Customer Report</a>
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
                        <div class="row">

                            <!-- Categories -->
                            <div class="col-md-3 mb-4">
                                <div class="card text-white bg-success">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">Categories</h5>
                                            <h3><?php echo $totalCategories; ?></h3>
                                        </div>
                                        <i class="fa fa-tags fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Products -->
                            <div class="col-md-3 mb-4">
                                <div class="card text-white bg-info">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">Products</h5>
                                            <h3><?php echo $totalProducts; ?></h3>
                                        </div>
                                        <i class="fa fa-boxes fa-2x"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Purchases -->
                            <div class="col-md-3 mb-4">
                                <div class="card text-white bg-danger">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">Purchases</h5>
                                            <h3><?php echo $totalPurchases; ?></h3>
                                        </div>
                                        <i class="fa fa-shopping-cart fa-2x"></i>
                                    </div>
                                </div>
                            </div>


                            <!-- Sales -->
                            <div class="col-md-3 mb-4">
                                <div class="card text-white bg-dark">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title">Sales</h5>
                                            <h3><?php echo $totalSales; ?></h3>
                                        </div>
                                        <i class="fa fa-dollar-sign fa-2x"></i>
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