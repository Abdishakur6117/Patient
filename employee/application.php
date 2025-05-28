<?php
session_start();

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'employee') {
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
                <a class="navbar-brand" href="../employee_dashboard.php">Job Portal Management System</a>
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
                                <a class="nav-link margin-top-10" href="../employee_dashboard.php">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
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
                                            <a class="nav-link" href="../employee/company.php">List Companies</a>
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
                                            <a class="nav-link" href="../employee/job.php">List Jobs</a>
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
                                            <a class="nav-link" href="../employee/application.php">List Applications</a>
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
                                            <a class="nav-link" href="../employee/companyReport.php">Company Report</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="../employee/jobSeekerReport.php">Job Seeker Report</a>
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
                    <h2>Application Form</h2>
                    <br>
                    <br>
                    <table id="dataTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Job Name</td>
                                <td>Job Seeker Name</td>
                                <td>Resume</td>
                                <td>Applied at</td>
                                <td>Status</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!--/   INsert Modal start -->
                <div class="modal fade" id="applicationModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Application</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="applicationForm" method="POST" action=""  enctype="multipart/form-data"> 
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="job">Job Name </label>
                                                <select class="form-control" name="job_name" id="job_name">
                                                    <option value="">Select Job </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="job_seeker">Job seeker Name </label>
                                                <select class="form-control" name="job_seeker_name" id="job_seeker_name">
                                                    <option value="">Select job seeker </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="resume">Resume </label>
                                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address">Status </label>
                                                <select class="form-control"  name="status" id="status">
                                                    <option value="">Select Status</option>
                                                    <option value="accepted">accepted</option>
                                                    <option value="pending">pending</option>
                                                    <option value="rejected">rejected</option>
                                                </select>
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
                <div class="modal fade" id="edit_applicationModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Application</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                            <form id="edit_applicationForm" method="POST" action=""  enctype="multipart/form-data"> 
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="job">Job Name </label>
                                                <input type="hidden" class="form-control" id="edit_id" name="edit_id">
                                                <select class="form-control" name="edit_job_name" id="edit_job_name" readonly>
                                                    <option value="">Select Job </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address">Status </label>
                                                <select class="form-control"  name="edit_status" id="edit_status">
                                                    <option value="">Select Status</option>
                                                    <option value="accepted">accepted</option>
                                                    <option value="pending">pending</option>
                                                    <option value="rejected">rejected</option>
                                                </select>
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
            $('#applicationModal').modal('show');
            $('#applicationForm')[0].reset();
        });
        
        // Initial data loading
        displayData();
        loadJob();
        loadUser();

        // Load aircraft for dropdown
        function loadJob() {
            $.ajax({
                url: 'applicationOperation.php?action=get_job',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success' && response.data) {
                        const $select = $('#job_name, #edit_job_name');
                        $select.empty().append('<option value="">Select Job</option>');
                        
                        response.data.forEach(job => {
                            $select.append($('<option>', {
                                value: job.job_id,
                                text: job.job_name
                            }));
                        });
                    } else {
                        showError('Failed to load Job');
                    }
                },
                error: function() {
                    showError('Network error loading Job');
                }
            });
        }
        function loadUser() {
            $.ajax({
                url: 'applicationOperation.php?action=get_job_seeker',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success' && response.data) {
                        const $select = $('#job_seeker_name, #edit_job_seeker_name');
                        $select.empty().append('<option value="">Select Job Seeker </option>');
                        
                        response.data.forEach(job_seeker => {
                            $select.append($('<option>', {
                                value: job_seeker.user_id,
                                text: job_seeker.job_seeker_name
                            }));
                        });
                    } else {
                        showError('Failed to load Job seeker');
                    }
                },
                error: function() {
                    showError('Network error loading Job seeker');
                }
            });
        }
        // Create user record
        $('#applicationForm').submit(function(e) {
            e.preventDefault();
            
            // Create FormData object
            var formData = new FormData(this);
            
            $.ajax({
                type: 'POST',
                url: 'applicationOperation.php?action=create_application',
                data: formData,
                processData: false,  // Important for file uploads
                contentType: false,  // Important for file uploads
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        showSuccess(res.message, function() {
                            $('#applicationModal').modal('hide');
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
            const applicationData = {
                id: $(this).data('id'),
                job_name: $(this).data('job_name'),
                status: $(this).data('status')
            };
            
            $('#edit_id').val(applicationData.id);
            $('#edit_job_name').val(applicationData.job_name);
            $('#edit_status').val(applicationData.status);
            
            $('#edit_applicationModal').modal('show');
        });
        
        // Submit Edit Form via AJAX
        $('#edit_applicationForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = $(this).find('[type="submit"]');

            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

            $.ajax({
                url: 'applicationOperation.php?action=update_application',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Debug output (comment this out in production)
                    // console.log("Server response:", response);

                    let res = response;

                    // If response is a string, parse JSON (sometimes jQuery auto-parses)
                    if (typeof response === 'string') {
                        try {
                            res = JSON.parse(response);
                        } catch (e) {
                            showError('Invalid response from server.');
                            submitBtn.prop('disabled', false).html('Save');
                            return;
                        }
                    }

                    if (res.status === 'success') {
                        showSuccess(res.message, function() {
                            $('#edit_applicationModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        showError(res.message || 'Update failed.');
                    }
                },
                error: function(xhr, status, error) {
                    showError('Error: ' + error);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('Save');
                }
            });
        });
        // Delete user record
        $(document).on('click', '.deleteBtn', function() {
            const application_id = $(this).data('id');
            
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
                        url: 'applicationOperation.php?action=delete_application',
                        data: { id: application_id },
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
                url: 'applicationOperation.php?action=display_application',
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
                            <td>${row.application_id || ''}</td>
                            <td>${row.job_name || ''}</td>
                            <td>${row.job_seeker_name || ''}</td>
                            <td>${row.resume || ''}</td>
                            <td>${row.applied_at || ''}</td>
                            <td>${row.status || ''}</td>
                            <td>
                                <button class="btn btn-warning btn-sm editBtn" 
                                    data-id="${row.application_id}" 
                                    data-job_name="${row.job_id}"
                                    data-job_seeker_name="${row.user_id}"
                                    data-resume="${row.resume}"
                                    data-applied_at="${row.applied_at}"
                                    data-status="${row.status}">
                                    Edit
                                </button>
                                <button class="btn btn-info btn-sm viewCvBtn" 
                                    data-resume="${row.resume}">
                                    View CV
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

                    // Event listener for View CV buttons
                    $('.viewCvBtn').on('click', function() {
                        const resumeFile = $(this).data('resume');
                        if (resumeFile) {
                            const url = '../uploads/resumes/' + resumeFile;
                            window.open(url, '_blank');
                        } else {
                            showError('No resume file available.');
                        }
                    });
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