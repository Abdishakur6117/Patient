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
    <title>Job Portal Management System</title>
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
                <a class="navbar-brand" href="../index.php">Job Portal Management System</a>
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

                            <!-- Dashboard -->
                            <li class="nav-item">
                                <a class="nav-link margin-top-10" href="../index.php">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>

                            <!-- Users -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-users"
                                    aria-expanded="false" aria-controls="submenu-users">
                                    <i class="fas fa-user-friends"></i> Users
                                </a>
                                <div id="submenu-users" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/users.php">List Users</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Companies -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-companies"
                                    aria-expanded="false" aria-controls="submenu-companies">
                                    <i class="fas fa-building"></i> Companies
                                </a>
                                <div id="submenu-companies" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/company.php">List Companies</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Jobs -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-jobs"
                                    aria-expanded="false" aria-controls="submenu-jobs">
                                    <i class="fas fa-briefcase"></i> Jobs
                                </a>
                                <div id="submenu-jobs" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/job.php">List Jobs</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- User Profiles -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-profiles"
                                    aria-expanded="false" aria-controls="submenu-profiles">
                                    <i class="fas fa-id-badge"></i> User Profiles
                                </a>
                                <div id="submenu-profiles" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/userProfile.php">List Profiles</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Applications -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-applications"
                                    aria-expanded="false" aria-controls="submenu-applications">
                                    <i class="fas fa-file-alt"></i> Applications
                                </a>
                                <div id="submenu-applications" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/application.php">List Applications</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Reports -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-reports"
                                    aria-expanded="false" aria-controls="submenu-reports">
                                    <i class="fas fa-chart-line"></i> Reports
                                </a>
                                <div id="submenu-reports" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/companyReport.php">Company Report</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/jobSeekerReport.php">Job Seeker Report</a>
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
                    <h2>Company Form</h2>
                    <button type="button" class="btn btn-primary at-3" id="insertModal">Add Company</button>
                    <br>
                    <br>
                    <table id="dataTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Employee Name</td>
                                <td>Company Name</td>
                                <td>Description</td>
                                <td>Location</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!--/   INsert Modal start -->
                <div class="modal fade" id="companyModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Company</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="companyForm" method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="employee">Employee Name </label>
                                                <select class="form-control" name="employee_name" id="employee_name">
                                                    <option value="">Select employee</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company">Company Name </label>
                                                <input type="text" class="form-control" id="company_name" name="company_name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="description">Description </label>
                                                <input type="text" class="form-control" id="description" name="description">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location">Location </label>
                                                <input type="text" class="form-control" id="location" name="location">
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
                <div class="modal fade" id="edit_companyModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Company</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="edit_companyForm" method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="employee">Employee Name </label>
                                                <input type="hidden" class="form-control" id="edit_id" name="edit_id">
                                                <select class="form-control" name="edit_employee_name" id="edit_employee_name">
                                                    <option value="">Select employee</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company">Company Name </label>
                                                <input type="text" class="form-control" id="edit_company_name" name="edit_company_name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="description">Description </label>
                                                <input type="text" class="form-control" id="edit_description" name="edit_description">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location">Location </label>
                                                <input type="text" class="form-control" id="edit_location" name="edit_location">
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
    <script>
        $(document).ready(function() {
            // Initialize modals and load data
            $('#insertModal').click(function() {
                $('#companyModal').modal('show');
                $('#companyForm')[0].reset();
            });
            
            // Initial data loading
            displayData();
            loadEmployee();

            // Load aircraft for dropdown
            function loadEmployee() {
                $.ajax({
                    url: 'companyOperation.php?action=get_employee',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success' && response.data) {
                            const $select = $('#employee_name, #edit_employee_name');
                            $select.empty().append('<option value="">Select Employee</option>');
                            
                            response.data.forEach(employee => {
                                $select.append($('<option>', {
                                    value: employee.user_id,
                                    text: employee.employee_name
                                }));
                            });
                        } else {
                            showError('Failed to load employee');
                        }
                    },
                    error: function() {
                        showError('Network error loading employee');
                    }
                });
            }
            // Create user record
            $('#companyForm').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: 'companyOperation.php?action=create_company',
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            showSuccess(res.message, function() {
                                $('#companyModal').modal('hide');
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
                const userData = {
                    id: $(this).data('id'),
                    employee_name: $(this).data('employee_name'),
                    company_name: $(this).data('company_name'),
                    description: $(this).data('description'),
                    location: $(this).data('locations')
                };
                
                $('#edit_id').val(userData.id);
                $('#edit_employee_name').val(userData.employee_name);
                $('#edit_company_name').val(userData.company_name);
                $('#edit_description').val(userData.description);
                $('#edit_location').val(userData.location);
                
                $('#edit_companyModal').modal('show');
            });
            
            // Update user record
            $('#edit_companyForm').submit(function(e) {
                e.preventDefault();
                const submitBtn = $(this).find('[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                const formData = {
                  edit_id: $('#edit_id').val(),
                  edit_employee_name: $('#edit_employee_name').val(),
                  edit_company_name: $('#edit_company_name').val(),
                  edit_description: $('#edit_description').val(),
                  edit_location: $('#edit_location').val()
                };
                $.ajax({
                    url: 'companyOperation.php?action=update_company',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success') {
                            showSuccess(response.message, function() {
                                $('#edit_companyModal').modal('hide');
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
                        submitBtn.prop('disabled', false).html('Update company');
                    }
                });
            });
            // Delete user record
            $(document).on('click', '.deleteBtn', function() {
                const company_id = $(this).data('id');
                
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
                            url: 'companyOperation.php?action=delete_company',
                            data: { id: company_id },
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
                    url: 'companyOperation.php?action=display_company',
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
                                <td>${row.company_id || ''}</td>
                                <td>${row.employee_name || ''}</td>
                                <td>${row.company_name || ''}</td>
                                <td>${row.description || ''}</td>
                                <td>${row.location || ''}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm editBtn" 
                                        data-id="${row.company_id}" 
                                        data-employee_name="${row.user_id}"
                                        data-company_name="${row.company_name}"
                                        data-description="${row.description}"
                                        data-locations="${row.location}">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm deleteBtn" 
                                        data-id="${row.company_id}">
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