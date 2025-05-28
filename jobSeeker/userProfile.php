<?php
session_start();

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'job_seeker') {
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
                <a class="navbar-brand" href="../jobSeeker_dashboard.php">Job Portal Management System</a>
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
                                <a class="nav-link margin-top-10" href="../jobSeeker_dashboard.php">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
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
                                            <a class="nav-link" href="../jobSeeker/userProfile.php">List Profiles</a>
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
                                            <a class="nav-link" href="../jobSeeker/application.php">List Applications</a>
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
                    <h2>UserProfile Form</h2>
                    <button type="button" class="btn btn-primary at-3" id="insertModal">Add UserProfile</button>
                    <br>
                    <br>
                    <table id="dataTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Job Seeker Name</td>
                                <td>Phone</td>
                                <td>Address</td>
                                <td>Education</td>
                                <td>Experience</td>
                                <td>Skills</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!--/   INsert Modal start -->
                <div class="modal fade" id="userProfileModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New UserProfile</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="userProfileForm" method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone </label>
                                                <input type="number" class="form-control" id="phone" name="phone">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address">Address </label>
                                                <input type="text" class="form-control" id="address" name="address">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="education">Education </label>
                                                <input type="text" class="form-control" id="education" name="education">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="experience">Experience </label>
                                                <input type="text" class="form-control" id="experience" name="experience">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="skills">Skills </label>
                                                <input type="text" class="form-control" id="skills" name="skills">
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
                <div class="modal fade" id="edit_userProfileModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update UserProfile</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="edit_userProfileForm" method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone </label>
                                                <input type="hidden" class="form-control" id="edit_id" name="edit_id">
                                                <input type="number" class="form-control" id="edit_phone" name="edit_phone">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address">Address </label>
                                                <input type="text" class="form-control" id="edit_address" name="edit_address">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="education">Education </label>
                                                <input type="text" class="form-control" id="edit_education" name="edit_education">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="experience">Experience </label>
                                                <input type="text" class="form-control" id="edit_experience" name="edit_experience">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="skills">Skills </label>
                                                <input type="text" class="form-control" id="edit_skills" name="edit_skills">
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
                $('#userProfileModal').modal('show');
                $('#userProfileForm')[0].reset();
            });
            
            // Initial data loading
            displayData();
            // Create user record
            $('#userProfileForm').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: 'userProfileOperation.php?action=create_userProfile',
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            showSuccess(res.message, function() {
                                $('#userProfileModal').modal('hide');
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
                const userProfileData = {
                    id: $(this).data('id'),
                    phone: $(this).data('phone'),
                    address: $(this).data('address'),
                    education: $(this).data('education'),
                    experience: $(this).data('experience'),
                    skill: $(this).data('skill')
                };
                
                $('#edit_id').val(userProfileData.id);
                $('#edit_phone').val(userProfileData.phone);
                $('#edit_address').val(userProfileData.address);
                $('#edit_education').val(userProfileData.education);
                $('#edit_experience').val(userProfileData.experience);
                $('#edit_skills').val(userProfileData.skill);
                
                $('#edit_userProfileModal').modal('show');
            });
            
            // Update user record
            $('#edit_userProfileForm').submit(function(e) {
                e.preventDefault();
                const submitBtn = $(this).find('[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                const formData = {
                  edit_id: $('#edit_id').val(),
                  edit_phone: $('#edit_phone').val(),
                  edit_address: $('#edit_address').val(),
                  edit_education: $('#edit_education').val(),
                  edit_experience: $('#edit_experience').val(),
                  edit_skills: $('#edit_skills').val()
                };
                $.ajax({
                    url: 'userProfileOperation.php?action=update_userProfile',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success') {
                            showSuccess(response.message, function() {
                                $('#edit_userProfileModal').modal('hide');
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
                        submitBtn.prop('disabled', false).html('Update userProfile');
                    }
                });
            });
            // Delete user record
            $(document).on('click', '.deleteBtn', function() {
                const userProfile_id = $(this).data('id');
                
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
                            url: 'userProfileOperation.php?action=delete_userProfile',
                            data: { id: userProfile_id },
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
                    url: 'userProfileOperation.php?action=display_userProfile',
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
                                <td>${row.profile_id || ''}</td>
                                <td>${row.job_seeker_name || ''}</td>
                                <td>${row.phone || ''}</td>
                                <td>${row.address || ''}</td>
                                <td>${row.education || ''}</td>
                                <td>${row.experience || ''}</td>
                                <td>${row.skills || ''}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm editBtn" 
                                        data-id="${row.profile_id}" 
                                        data-job_seeker_name="${row.user_id}"
                                        data-phone="${row.phone}"
                                        data-address="${row.address}"
                                        data-education="${row.education}"
                                        data-experience="${row.experience}"
                                        data-skill="${row.skills}">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm deleteBtn" 
                                        data-id="${row.profile_id}">
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