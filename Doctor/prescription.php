<?php
session_start();

// Check if the user is logged in and has the 'Admin' role
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'Doctor') {
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
                <a class="navbar-brand" href="../doctor_dashboard.php">Patient Management System</a>
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
                                <a class="nav-link margin-top-10" href="../doctor_dashboard.php">
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
                                            <a class="nav-link" href="../Doctor/appointment.php">List Appointments</a>
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
                                            <a class="nav-link" href="../Doctor/visit.php">List Visits</a>
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
                                            <a class="nav-link" href="../Doctor/prescription.php">List Prescriptions</a>
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
                                            <a class="nav-link" href="../Doctor/patientReport.php">Patient Report</a>
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
                    <h2>prescription Form</h2>
                    <button type="button" class="btn btn-primary at-3" id="insertModal">Add prescription</button>
                    <br>
                    <br>
                    <table id="dataTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Patient Name</td>
                                <td>Medication</td>
                                <td>Dosage</td>
                                <td>Duration</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!--/   INsert Modal start -->
                <div class="modal fade" id="prescriptionModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New prescription</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="prescriptionForm" method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="patient">Patient Name </label>
                                                <select class="form-control" name="visit_id" id="visit_id">
                                                    <option value="">Select visit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="medication">Medication </label>
                                                <input type="text" class="form-control" id="medication" name="medication">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="dosage">Dosage </label>
                                                <input type="text" class="form-control" id="dosage" name="dosage">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="duration">Duration </label>
                                                <input type="text" class="form-control" id="duration" name="duration">
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
                <div class="modal fade" id="edit_prescriptionModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update prescription</h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="edit_prescriptionForm" method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="patient">Patient Name </label>
                                                <input type="hidden" class="form-control" id="edit_id" name="edit_id">
                                                <select class="form-control" name="edit_visit_id" id="edit_visit_id">
                                                    <option value="">Select visit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="medication">Medication </label>
                                                <input type="text" class="form-control" id="edit_medication" name="edit_medication">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="dosage">Dosage </label>
                                                <input type="text" class="form-control" id="edit_dosage" name="edit_dosage">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="duration">Duration </label>
                                                <input type="text" class="form-control" id="edit_duration" name="edit_duration">
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
                $('#prescriptionModal').modal('show');
                $('#prescriptionForm')[0].reset();
            });
            
            // Initial data loading
            displayData();
            loadPatient();
            // Load patient for dropdown
            function loadPatient() {
                $.ajax({
                    url: 'prescriptionOperation.php?action=get_patient',
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success' && response.data) {
                            const $select = $('#visit_id, #edit_visit_id');
                            $select.empty().append('<option value="">Select Patent</option>');
                            
                            response.data.forEach(patient => {
                                $select.append($('<option>', {
                                    value: patient.visit_id,
                                    text: patient.patient_name
                                }));
                            });
                        } else {
                            showError('Failed to load Patient');
                        }
                    },
                    error: function() {
                        showError('Network error loading Patient');
                    }
                });
            }
            // Create prescription record
            $('#prescriptionForm').submit(function(e) {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: 'prescriptionOperation.php?action=create_prescription',
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            showSuccess(res.message, function() {
                                $('#prescriptionModal').modal('hide');
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
            
            // Edit prescription record
            $(document).on('click', '.editBtn', function() {
                const prescriptionData = {
                    id: $(this).data('id'),
                    patient_name: $(this).data('visit_id'),
                    medication: $(this).data('medication'),
                    dosage: $(this).data('dosage'),
                    duration: $(this).data('duration')
                };
                
                $('#edit_id').val(prescriptionData.id);
                $('#edit_visit_id').val(prescriptionData.patient_name);
                $('#edit_medication').val(prescriptionData.medication);
                $('#edit_dosage').val(prescriptionData.dosage);
                $('#edit_duration').val(prescriptionData.duration);
                
                $('#edit_prescriptionModal').modal('show');
            });
            
            // Update prescription record
            $('#edit_prescriptionForm').submit(function(e) {
                e.preventDefault();
                const submitBtn = $(this).find('[type="submit"]');
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
                const formData = {
                  edit_id: $('#edit_id').val(),
                  edit_visit_id: $('#edit_visit_id').val(),
                  edit_medication: $('#edit_medication').val(),
                  edit_dosage: $('#edit_dosage').val(),
                  edit_duration: $('#edit_duration').val()
                };
                $.ajax({
                    url: 'prescriptionOperation.php?action=update_prescription',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if(response.status === 'success') {
                            showSuccess(response.message, function() {
                                $('#edit_prescriptionModal').modal('hide');
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
                        submitBtn.prop('disabled', false).html('Update prescription');
                    }
                });
            });
            // Delete prescription record
            $(document).on('click', '.deleteBtn', function() {
                const prescription_id = $(this).data('id');
                
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
                            url: 'prescriptionOperation.php?action=delete_prescription',
                            data: { id: prescription_id },
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
            
            // Display prescription data in table
            function displayData() {
                $.ajax({
                    url: 'prescriptionOperation.php?action=display_prescription',
                    dataType: 'json',
                    success: function(response) {
                        if (!response || response.status !== 'success' || !Array.isArray(response.data)) {
                            showError('Invalid data received from server');
                            return;
                        }

                        let tableData = '';
                        response.data.forEach(row => {
                            tableData += `
                            <tr>
                                <td>${row.prescription_id || ''}</td>
                                <td>${row.patient_name || ''}</td>
                                <td>${row.medication || ''}</td>
                                <td>${row.dosage || ''}</td>
                                <td>${row.duration || ''}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm editBtn" 
                                        data-id="${row.prescription_id}" 
                                        data-visit_id="${row.visit_id}"
                                        data-medication="${row.medication}"
                                        data-dosage="${row.dosage}"
                                        data-duration="${row.duration}">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm deleteBtn" 
                                        data-id="${row.prescription_id}">
                                        Delete
                                    </button>
                                </td>
                            </tr>`;
                        });

                        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#dataTable')) {
                            $('#dataTable').DataTable().destroy();
                        }

                        $('#dataTable tbody').html(tableData);
                        initDataTable();
                    },
                    error: function(xhr, status, error) {
                        showError('Failed to load prescription data: ' + error);
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