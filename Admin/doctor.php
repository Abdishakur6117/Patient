<?php
session_start();

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'Admin') {
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
    <title> Patient Management System</title>
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
                <a class="navbar-brand" href="../index.php">Patient Management System</a>
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

                            <!-- Patient -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-patient"
                                    aria-expanded="false" aria-controls="submenu-patient">
                                    <i class="fas fa-user-injured"></i> Patients
                                </a>
                                <div id="submenu-patient" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/patient.php">List Patients</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Doctor -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-doctor"
                                    aria-expanded="false" aria-controls="submenu-doctor">
                                    <i class="fas fa-user-md"></i> Doctors
                                </a>
                                <div id="submenu-doctor" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/doctor.php">List Doctors</a>
                                        </li>
                                    </ul>
                                </div>
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
                                            <a class="nav-link" href="../Admin/appointment.php">List Appointments</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <!-- Visits -->
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-toggle="collapse" data-target="#submenu-visits"
                                    aria-expanded="false" aria-controls="submenu-visits">
                                    <i class="fas fa-notes-medical"></i> Visits
                                </a>
                                <div id="submenu-visits" class="collapse submenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/visit.php">List Visits</a>
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
                                            <a class="nav-link" href="../Admin/prescription.php">List Prescriptions</a>
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
                                            <a class="nav-link" href="../Admin/payment.php">List Payments</a>
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
                                            <a class="nav-link" href="../Admin/patientReport.php">Patient Report</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="../Admin/doctorReport.php">Doctor Report</a>
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
                    <h2>Doctor Form</h2>
                    <button type="button" class="btn btn-primary at-3" id="insertModal">Add Doctor</button>
                    <br>
                    <br>
                    <table id="dataTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Doctor Name</td>
                                <td>Gender</td>
                                <td>Date of Birth</td>
                                <td>Address</td>
                                <td>Phone</td>
                                <td>Email</td>
                                <td>Specialty</td>
                                <td>Created at</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!--/   INsert Modal start -->
                <div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Doctor</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="doctorForm" method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="doctor">Doctor Name </label>
                                                <input type="text" class="form-control" id="doctor_name" name="doctor_name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="gender">Gender </label>
                                                <select class="form-control" name="gender" id="gender">
                                                    <option value="">Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Date of Birth </label>
                                                <input type="date" class="form-control" id="DOB" name="DOB">
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
                                                <label for="phone">Phone </label>
                                                <input type="number" class="form-control" id="phone" name="phone">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email </label>
                                                <input type="email" class="form-control" id="email" name="email">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="specialty">Specialty </label>
                                                <input type="text" class="form-control" id="specialty" name="specialty">
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
                <div class="modal fade" id="edit_doctorModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Doctor</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="edit_doctorForm" method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="doctor">Doctor Name </label>
                                                <input type="hidden" class="form-control" id="edit_id" name="edit_id">
                                                <input type="text" class="form-control" id="edit_doctor_name" name="edit_doctor_name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="gender">Gender </label>
                                                <select class="form-control" name="edit_gender" id="edit_gender">
                                                    <option value="">Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Date of Birth </label>
                                                <input type="date" class="form-control" id="edit_DOB" name="edit_DOB">
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
                                                <label for="phone">Phone </label>
                                                <input type="number" class="form-control" id="edit_phone" name="edit_phone">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email </label>
                                                <input type="email" class="form-control" id="edit_email" name="edit_email">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="specialty">Specialty </label>
                                                <input type="text" class="form-control" id="edit_specialty" name="edit_specialty">
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
                $('#doctorModal').modal('show');
                $('#doctorForm')[0].reset();
            });
            
            // Initial data loading
            displayData();
            
            // Create doctor record
            $('#doctorForm').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: 'doctorOperation.php?action=create_doctor',
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            showSuccess(res.message, function() {
                                $('#doctorModal').modal('hide');
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
            
            // Edit doctor record
            $(document).on('click', '.editBtn', function() {
                const doctorData = {
                    id: $(this).data('id'),
                    doctor_name: $(this).data('doctor_name'),
                    gender: $(this).data('gender'),
                    DOB: $(this).data('dob'),
                    address: $(this).data('address'),
                    phone: $(this).data('phone'),
                    email: $(this).data('email'),
                    specialty: $(this).data('specialty'),
                };
                
                $('#edit_id').val(doctorData.id);
                $('#edit_doctor_name').val(doctorData.doctor_name);
                $('#edit_gender').val(doctorData.gender);
                $('#edit_DOB').val(doctorData.DOB);
                $('#edit_address').val(doctorData.address);
                $('#edit_phone').val(doctorData.phone);
                $('#edit_email').val(doctorData.email);
                $('#edit_specialty').val(doctorData.specialty);
                
                $('#edit_doctorModal').modal('show');
            });
            
            // Update doctor record
            $('#edit_doctorForm').submit(function(e) {
                e.preventDefault();
                const submitBtn = $(this).find('[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                const formData = {
                  edit_id: $('#edit_id').val(),
                  edit_doctor_name: $('#edit_doctor_name').val(),
                  edit_gender: $('#edit_gender').val(),
                  edit_DOB: $('#edit_DOB').val(),
                  edit_address: $('#edit_address').val(),
                  edit_phone: $('#edit_phone').val(),
                  edit_email: $('#edit_email').val(),
                  edit_specialty: $('#edit_specialty').val()
                };
                $.ajax({
                    url: 'doctorOperation.php?action=update_doctor',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success') {
                            showSuccess(response.message, function() {
                                $('#edit_doctorModal').modal('hide');
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
                        submitBtn.prop('disabled', false).html('Update doctor');
                    }
                });
            });
            // Delete doctor record
            $(document).on('click', '.deleteBtn', function() {
                const doctor_id = $(this).data('id');
                
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
                            url: 'doctorOperation.php?action=delete_doctor',
                            data: { id: doctor_id },
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
            
            // Display doctor data in table
            function displayData() {
                $.ajax({
                    url: 'doctorOperation.php?action=display_doctor',
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
                                <td>${row.doctor_id || ''}</td>
                                <td>${row.doctor_name || ''}</td>
                                <td>${row.gender || ''}</td>
                                <td>${row.date_of_birth || ''}</td>
                                <td>${row.address || ''}</td>
                                <td>${row.phone || ''}</td>
                                <td>${row.email || ''}</td>
                                <td>${row.specialty || ''}</td>
                                <td>${row.created_at || ''}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm editBtn" 
                                        data-id="${row.doctor_id}" 
                                        data-doctor_name="${row.doctor_name}"
                                        data-gender="${row.gender}"
                                        data-dob="${row.date_of_birth}"
                                        data-address="${row.address}"
                                        data-phone="${row.phone}"
                                        data-email="${row.email}"
                                        data-specialty="${row.specialty}">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm deleteBtn" 
                                        data-id="${row.doctor_id}">
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
                        showError('Failed to load doctor data: ' + error);
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