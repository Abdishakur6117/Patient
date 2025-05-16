<?php
session_start();

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    // Redirect to login page if not logged in or not an Admin
    header("Location: login.php");
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="../assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/libs/css/style.css">
    <link rel="stylesheet" href="../assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- <link rel="stylesheet" href="../assets/vendor/datatables/css/dataTables.bootstrap4.css"> -->
    <title>Stock Management System</title>
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
                <a class="navbar-brand" href="../index.php">Stock Management System</a>
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
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="../assets/images/avatar-1.jpg" alt="" class="user-avatar-md rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <div class="nav-user-info">
                                    <h5 class="mb-0 text-white nav-user-name"><?php echo htmlspecialchars($_SESSION['user']); ?> </h5>
                                    <span class="status"></span><span class="ml-2"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                                </div>
                                <a class="dropdown-item" href="../logout.php"><i class="fas fa-power-off mr-2"></i>Logout</a>
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
                                <a class="nav-link margin-top-10" href="../index.php">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </li>

                            <!-- Users -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-users"
                                    aria-expanded="false" aria-controls="submenu-users">
                                    <i class="fas fa-users"></i> Users
                                </a>
                                <div id="submenu-users" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/users.php">List Users</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Categories -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-categories"
                                    aria-expanded="false" aria-controls="submenu-categories">
                                    <i class="fas fa-tags"></i> Categories
                                </a>
                                <div id="submenu-categories" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/Category.php">List Categories</a>
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
                                            <a class="nav-link" href="../Admin/Products.php">List Products</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Suppliers -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-suppliers"
                                    aria-expanded="false" aria-controls="submenu-suppliers">
                                    <i class="fas fa-truck"></i> Suppliers
                                </a>
                                <div id="submenu-suppliers" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/Supplier.php">List Suppliers</a>
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
                                            <a class="nav-link" href="../Admin/Purchase.php">List Purchases</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Purchase Details -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-purchase-details"
                                    aria-expanded="false" aria-controls="submenu-purchase-details">
                                    <i class="fas fa-file-invoice"></i> Purchase Details
                                </a>
                                <div id="submenu-purchase-details" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/PurchaseDetails.php">List Purchase Details</a>
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
                                            <a class="nav-link" href="../Admin/Sale.php">List Sales</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Sale Details -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-sale-details"
                                    aria-expanded="false" aria-controls="submenu-sale-details">
                                    <i class="fas fa-receipt"></i> Sale Details
                                </a>
                                <div id="submenu-sale-details" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/SaleDetails.php">List Sale Details</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Reports -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-reports"
                                    aria-expanded="false" aria-controls="submenu-reports">
                                    <i class="fas fa-chart-bar"></i> Reports
                                </a>
                                <div id="submenu-reports" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/supplierReport.php">Supplier Report</a>
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
                    <h2>purchase Form</h2>
                    <button type="button" class="btn btn-primary at-3" id="insertModal">Add purchase</button>
                    <br>
                    <br>
                    <table id="dataTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Supplier Name</td>
                                <td>UserName</td>
                                <td> Purchase Date</td>
                                <td>Total Amount</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!--/   INsert Modal start -->
                <div class="modal fade" id="purchaseModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Purchases</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="purchaseForm" method="POST" >
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="supplier">Supplier Name </label>
                                                <select class="form-control" name="supplier_id" id="supplier_id">
                                                    <option value="">Select Supplier</option>
                                                    <!-- Populate this dynamically using backend data -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="user">User Name </label>
                                                <select class="form-control" name="user_id" id="user_id">
                                                    <option value="">Select User</option>
                                                    <!-- Populate this dynamically using backend data -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="purchase_date">Purchase Date </label>
                                                <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Start of Purchase Items Section -->
                                    <div class="purchase-items">
                                        <div class="row purchase-item">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="product_id[]">Product</label>
                                                    <select class="form-control" name="product_id[]" id="product_id">
                                                        <option value="">Select Product</option>
                                                        <!-- Populate this dynamically with products -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="quantity[]">Quantity</label>
                                                    <input type="number" class="form-control" name="quantity[]" placeholder="Enter Quantity" >
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="unit_price[]">Unit Price</label>
                                                    <input type="number" class="form-control" name="unit_price[]" placeholder="Enter Price" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Button to Add New Product Line -->
                                    <button type="button" id="addItemBtn" class="btn btn-secondary">Add Another Item</button>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!--/   INsert Modal end -->
                <!-- start Update Model  -->
                <div class="modal fade" id="edit_purchaseModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Purchases</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="edit_purchaseForm" method="POST" >
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="supplier">Supplier Name </label>
                                                <input type="hidden" class="form-control" id="edit_id" name="edit_id">
                                                <select class="form-control" name="edit_supplier_id" id="edit_supplier_id">
                                                    <option value="">Select Supplier</option>
                                                    <!-- Populate this dynamically using backend data -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="user">User Name </label>
                                                <select class="form-control" name="edit_user_id" id="edit_user_id">
                                                    <option value="">Select User</option>
                                                    <!-- Populate this dynamically using backend data -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="purchase_date">Purchase Date </label>
                                                <input type="date" class="form-control" id="edit_purchase_date" name="edit_purchase_date">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Start of Purchase Items Section -->
                                    <div class="purchase-items">
                                        <div class="row purchase-item">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="product_id[]">Product</label>
                                                    <select class="form-control" name="edit_product_id[]" id="edit_product_id">
                                                        <option value="">Select Product</option>
                                                        <!-- Populate this dynamically with products -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="quantity[]">Quantity</label>
                                                    <input type="number" class="form-control" name="edit_quantity[]" placeholder="Enter Quantity" >
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="unit_price[]">Unit Price</label>
                                                    <input type="number" class="form-control" name="edit_unit_price[]" placeholder="Enter Price" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Button to Add New Product Line -->
                                    <button type="button" id="addItemBtn" class="btn btn-secondary">Add Another Item</button>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Ends Update Model  -->
            </div>
            <!-- ============================================================== -->
                                 <!-- footer -->
            <!-- ============================================================== -->
            <div class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            Copyright © 2018 Concept. All rights reserved. Dashboard by <a href="https://colorlib.com/wp/">Colorlib</a>.
                        </div>
                    </div>
                </div>
            </div>
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
<!-- jQuery first -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
 <script src="../assets/vendor/jquery/jquery-3.3.1.min.js"></script>
 <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
 <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize modals and load data
            $('#insertModal').click(function() {
                $('#purchaseModal').modal('show');
                $('#purchaseForm')[0].reset();
            });
            getProductOptions(function (optionsHtml) {
                $('#product_id').html(optionsHtml);
            });
            
            // Initial data loading
            displayData();
            loadSupplier();
            loadUser();

            // Load aircraft for dropdown
            function loadSupplier() {
                $.ajax({
                    url: 'purchaseOperation.php?action=get_supplier',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success' && response.data) {
                            const $select = $('#supplier_id, #edit_supplier_id');
                            $select.empty().append('<option value="">Select Supplier</option>');
                            
                            response.data.forEach(supplier => {
                                $select.append($('<option>', {
                                    value: supplier.supplier_id,
                                    text: supplier.name
                                }));
                            });
                        } else {
                            showError('Failed to load supplier');
                        }
                    },
                    error: function() {
                        showError('Network error loading supplier');
                    }
                });
            }
            function loadUser() {
                $.ajax({
                    url: 'purchaseOperation.php?action=get_user',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success' && response.data) {
                            const $select = $('#user_id, #edit_user_id');
                            $select.empty().append('<option value="">Select User</option>');
                            
                            response.data.forEach(user => {
                                $select.append($('<option>', {
                                    value: user.user_id,
                                    text: user.username
                                }));
                            });
                        } else {
                            showError('Failed to load user');
                        }
                    },
                    error: function() {
                        showError('Network error loading user');
                    }
                });
            }
            // Function to add a new purchase item row
            document.getElementById('addItemBtn').addEventListener('click', function () {
                getProductOptions(function (optionsHtml) {
                    const itemRow = document.createElement('div');
                    itemRow.classList.add('row', 'purchase-item', 'mt-2');

                    itemRow.innerHTML = `
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Product</label>
                                <select class="form-control" name="product_id[]">
                                    ${optionsHtml}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" class="form-control" name="quantity[]" placeholder="Enter Quantity" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Unit Price</label>
                                <input type="number" class="form-control" name="unit_price[]" placeholder="Enter Price" required>
                            </div>
                        </div>
                    `;

                    document.querySelector('.purchase-items').appendChild(itemRow);
                });
            });

            function getProductOptions(callback) {
                // $.ajax({
                //     url: 'purchaseOperation.php?action=get_product',
                //     method: 'GET',
                //     dataType: 'json',
                //     success: function(response) {
                //         if(response.status === 'success' && response.data) {
                //             const $select = $('#product_id, #edit_product_id');
                //             let options = '<option value="">Select product</option>';
                //             response.data.forEach(product => {
                //                 options += `<option value="${product.product_id}">${product.name}</option>`;
                //             });
                //             callback(options);
                //         } else {
                //             showError('Failed to load product');
                //         }
                //     },
                //     error: function() {
                //         showError('Network error loading product');
                //     }
                // });
                $.ajax({
                    url: 'purchaseOperation.php?action=get_product',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success' && response.data) {
                            const $select = $('#product_id, #edit_product_id');
                            $select.empty().append('<option value="">Select product</option>');
                            
                            response.data.forEach(product => {
                                $select.append($('<option>', {
                                    value: product.product_id,
                                    text: product.name
                                }));
                            });
                        } else {
                            showError('Failed to load product');
                        }
                    },
                    error: function() {
                        showError('Network error loading product');
                    }
                });
            }


            // Create user record
            $('#purchaseForm').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: 'purchaseOperation.php?action=create_purchase',
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            showSuccess(res.message, function() {
                                $('#purchaseModal').modal('hide');
                                displayData();
                            });
                        } else {
                            showError(res.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        showError('An error occurred: ' + error);
                    }
                });
            });
            // Edit user record
            $(document).on('click', '.editBtn', function() {
                const purchaseData = {
                    id: $(this).data('id'),
                    supplier_id: $(this).data('supplier_id'),
                    user_id: $(this).data('user_id'),
                    purchase_date: $(this).data('purchase_date')
                };
                
                $('#edit_id').val(purchaseData.id);
                $('#edit_supplier_id').val(purchaseData.supplier_id);
                $('#edit_user_id').val(purchaseData.user_id);
                $('#edit_purchase_date').val(purchaseData.purchase_date);
                
                $('#edit_purchaseModal').modal('show');
            });
            
            // Update user record
            $('#edit_purchaseForm').submit(function(e) {
                e.preventDefault();
                const submitBtn = $(this).find('[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                const formData = {
                  edit_id: $('#edit_id').val(),
                  edit_supplier_id: $('#edit_supplier_id').val(),
                  edit_user_id: $('#edit_user_id').val(),
                  edit_purchase_date: $('#edit_purchase_date').val()
                };
                $.ajax({
                    url: 'purchaseOperation.php?action=update_purchase',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success') {
                            showSuccess(response.message, function() {
                                $('#edit_purchaseModal').modal('hide');
                                displayData();
                            });
                        } else {
                            showError(response.message);
                        }
                    },
                    error: function(xhr) {
                        showError('An error occurred: ' + xhr.statusText);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('Update purchase');
                    }
                });
            });

            // Delete user record
            $(document).on('click', '.deleteBtn', function() {
                const purchase_id = $(this).data('id');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            url: 'purchaseOperation.php?action=delete_purchase',
                            data: { id: purchase_id },
                            dataType: 'json',
                            success: function(res) {
                                if (res.status === 'success') {
                                    showSuccess(res.message, function() {
                                        displayData();
                                    });
                                } else {
                                    showError(res.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                showError('An error occurred: ' + error);
                            }
                        });
                    }
                });
            });
            
            // Display user data in table
            function displayData() {
                $.ajax({
                    url: 'purchaseOperation.php?action=display_purchase',
                    dataType: 'json',
                    success: function(response) {
                        // Check if response is valid and contains data
                        if (!response || !Array.isArray(response)) {
                            showError('Invalid data received from server');
                            return;
                        }
                        
                        let tableData = '';
                        response.forEach(row => {
                            tableData += `
                            <tr>
                                <td>${row.purchase_id || ''}</td>
                                <td>${row.supplier_name || ''}</td>
                                <td>${row.user_name || ''}</td>
                                <td>${row.purchase_date || ''}</td>
                                <td>${row.total_amount || ''}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm editBtn" 
                                        data-id="${row.purchase_id  }" 
                                        data-supplier_id="${row.supplier_id}"
                                        data-user_id="${row.user_id}"
                                        data-purchase_date="${row.purchase_date}"
                                        data-total_amount="${row.total_amount}">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm deleteBtn" 
                                        data-id="${row.purchase_id}">
                                        Delete
                                    </button>
                                </td>
                            </tr>`;
                        });
                        
                        // Check if DataTable exists before destroying
                        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#dataTable')) {
                            $('#dataTable').DataTable().destroy();
                        }
                        
                        $('#dataTable tbody').html(tableData);
                        initDataTable();
                    },
                    error: function(xhr, status, error) {
                        showError('Failed to load user data: ' + error);
                    }
                });
            }
            
            // Initialize DataTable
            function initDataTable() {
                $('#dataTable').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    responsive: true
                });
            }

            
            // Helper function to show success messages
            function showSuccess(message, callback) {
                Swal.fire({
                    title: 'Success!',
                    text: message,
                    icon: 'success',
                    confirmButtonText: 'OK',
                    timer: 3000
                }).then(callback);
            }
            
            // Helper function to show error messages
            function showError(message) {
                Swal.fire({
                    title: 'Error!',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
    
    <!-- bootstap bundle js -->
    <!-- slimscroll js -->
    <script src="../assets/vendor/slimscroll/jquery.slimscroll.js"></script>
    <!-- main js -->
    <script src="../assets/libs/js/main-js.js"></script>
</body>
</html>