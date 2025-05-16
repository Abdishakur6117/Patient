<?php
session_start();

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'staff') {
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
                <a class="navbar-brand" href="../staff_dashboard.php">Stock Management System</a>
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
                                <a class="nav-link margin-top-10" href="../staff_dashboard.php">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
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
                                            <a class="nav-link" href="../staff/Products.php">List Products</a>
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
                                            <a class="nav-link" href="../staff/Purchase.php">List Purchases</a>
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
                                            <a class="nav-link" href="../staff/PurchaseDetails.php">List Purchase Details</a>
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
                                            <a class="nav-link" href="../staff/Sale.php">List Sales</a>
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
                                            <a class="nav-link" href="../staff/SaleDetails.php">List Sale Details</a>
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
                    <h2>Sales Details Form</h2>
                    <button type="button" class="btn btn-primary at-3" id="insertModal">Add Sale Details</button>
                    <br>
                    <br>
                    <table id="dataTable" class="table table-striped table-bordered">
                      <thead>
                          <tr>
                              <td>ID</td>
                              <td>Customer Name</td>
                              <td>Product Name</td>
                              <td>Quantity</td>
                              <td>Unit Price</td>
                              <td>Actions</td>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                </div>
                <!--/   INsert Modal start -->
                <div class="modal fade" id="saleDetailsModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Sales Details</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="saleDetailsForm" method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer">Customer Name </label>
                                                <select class="form-control" name="sale_id" id="sale_id">
                                                  <option value="">Select Customer</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product">Product Name </label>
                                                <select class="form-control" name="product_id" id="product_id">
                                                  <option value="">Select product</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quantity"> Quantity </label>
                                                <input type="number" class="form-control" id="quantity" name="quantity">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="price">Unit Price </label>
                                                <input type="number" class="form-control" id="unit_price" name="unit_price">
                                            </div>
                                        </div>
                                    </div>
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
                <div class="modal fade" id="edit_saleDetailsModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Sales Details</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="edit_saleDetailsForm" method="POST" action="">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="customer">Customer Name </label>
                                                    <input type="hidden" class="form-control" id="edit_id" name="edit_id">
                                                    <select class="form-control" name="edit_sale_id" id="edit_sale_id">
                                                    <option value="">Select Customer</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="product">Product Name </label>
                                                    <select class="form-control" name="edit_product_id" id="edit_product_id">
                                                    <option value="">Select product</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="quantity"> Quantity </label>
                                                    <input type="number" class="form-control" id="edit_quantity" name="edit_quantity">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="price">Unit Price </label>
                                                    <input type="number" class="form-control" id="edit_unit_price" name="edit_unit_price">
                                                </div>
                                            </div>
                                        </div>
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
 <!-- Ku dar jsPDF CDN haddii aad rabto PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize modals and load data
        $('#insertModal').click(function() {
            $('#saleDetailsModal').modal('show');
            $('#saleDetailsForm')[0].reset();
        });
        
        // Initial data loading
        displayData();
        loadCustomer();
        loadProduct();

        // Load passenger for dropdown
        function loadCustomer() {
            $.ajax({
                url: 'saleDetailOperation.php?action=get_customer',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success' && response.data) {
                        const $select = $('#sale_id, #edit_sale_id');
                        $select.empty().append('<option value="">Select customer</option>');
                        
                        response.data.forEach(purchase => {
                            $select.append($('<option>', {
                                value: purchase.sale_id,
                                text: purchase.customer_name
                            }));
                        });
                    } else {
                        showError('Failed to load customer');
                    }
                },
                error: function() {
                    showError('Network error loading customer');
                }
            });
        }
        // Load flight for dropdown
        function loadProduct() {
            $.ajax({
                url: 'saleDetailOperation.php?action=get_product',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success' && response.data) {
                        const $select = $('#product_id, #edit_product_id');
                        $select.empty().append('<option value="">Select product</option>');
                        
                        response.data.forEach(product => {
                            $select.append($('<option>', {
                                value: product.product_id,
                                text: product.product_name,
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
        $('#saleDetailsForm').submit(function(e) {
            e.preventDefault();
            
            $.ajax({
                type: 'POST',
                url: 'saleDetailOperation.php?action=create_saleDetail',
                data: $(this).serialize(),
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        showSuccess(res.message, function() {
                            $('#saleDetailsModal').modal('hide');
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
            const saleDetailsData = {
                id: $(this).data('id'),
                sale_id: $(this).data('sale_id'),
                product_id: $(this).data('product_id'),
                quantity: $(this).data('quantity'),
                unit_price: $(this).data('unit_price')
            };
            
            $('#edit_id').val(saleDetailsData.id);
            $('#edit_sale_id').val(saleDetailsData.sale_id);
            $('#edit_product_id').val(saleDetailsData.product_id);
            $('#edit_quantity').val(saleDetailsData.quantity);
            $('#edit_unit_price').val(saleDetailsData.unit_price);
            
            $('#edit_saleDetailsModal').modal('show');
        });
        
        // Update user record
        $('#edit_saleDetailsForm').submit(function(e) {
            e.preventDefault();
            const submitBtn = $(this).find('[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
            const formData = {
                edit_id: $('#edit_id').val(),
                edit_sale_id: $('#edit_sale_id').val(),
                edit_product_id: $('#edit_product_id').val(),
                edit_quantity: $('#edit_quantity').val(),
                edit_unit_price: $('#edit_unit_price').val()
            };
            $.ajax({
                url: 'saleDetailOperation.php?action=update_saleDetail',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        showSuccess(response.message, function() {
                            $('#edit_saleDetailsModal').modal('hide');
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
                    submitBtn.prop('disabled', false).html('Update sale Detail');
                }
            });
        });
        // Delete user record
        $(document).on('click', '.deleteBtn', function() {
            const saleDetail_id = $(this).data('id');
            
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
                        url: 'saleDetailOperation.php?action=delete_saleDetail',
                        data: { id: saleDetail_id },
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
                url: 'saleDetailOperation.php?action=display_saleDetail',
                dataType: 'json',
                success: function(response) {
                    // Check if response is valid and has 'data' key as array
                    if (!response || response.status !== 'success' || !Array.isArray(response.data)) {
                        showError('Invalid data received from server');
                        return;
                    }

                    let tableData = '';
                    response.data.forEach(row => {
                        tableData += `
                        <tr>
                            <td>${row.detail_id || ''}</td>
                            <td>${row.customer_name || ''}</td>
                            <td>${row.product_name || ''}</td>
                            <td>${row.quantity || ''}</td>
                            <td>${row.unit_price || ''}</td>
                            <td>
                                <button class="btn btn-warning btn-sm editBtn" 
                                    data-id="${row.detail_id}" 
                                    data-sale_id="${row.sale_id}"
                                    data-product_id="${row.product_id}"
                                    data-quantity="${row.quantity}"
                                    data-unit_price="${row.unit_price}">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm deleteBtn" 
                                    data-id="${row.detail_id}">
                                    Delete
                                </button>
                            </td>
                        </tr>`;
                    });

                    // Destroy existing DataTable instance
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#dataTable')) {
                        $('#dataTable').DataTable().destroy();
                    }

                    $('#dataTable tbody').html(tableData);
                    initDataTable();
                },
                error: function(xhr, status, error) {
                    showError('Failed to load purchase details: ' + error);
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