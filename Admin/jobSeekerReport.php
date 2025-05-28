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
                    <h2>jobSeeker Report</h2>
                    <br>
                    <br>
                    <!-- Supplier Name Input -->
                    <label for="jobSeeker_name">jobSeeker:</label><br>
                    <input type="text" id="jobSeeker_name" placeholder="Enter jobSeeker Name">
                    <button onclick="searchJobSeeker()">Search</button>

                    <!-- Supplier Table -->
                    <table id="jobSeeker_table" class="display nowrap" style="width:100%; display:none;">
                    <thead>
                        <tr>
                        <td>ID</td>
                        <td>Job Seeker Name</td>
                        <td>Email</td>
                        <td>Phone</td>
                        <td>Address</td>
                        <td> Education</td>
                        <td>Experience</td>
                        <td>Skills</td>
                        <td>Actions</td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    </table>

                </div>
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
let jobSeekerTable;

function searchJobSeeker() {
  const searchTerm = $('#jobSeeker_name').val();

  if (!jobSeekerTable) {
    jobSeekerTable = $('#jobSeeker_table').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: 'fetch_jobSeeker.php',
        type: 'POST',
        data: function (d) {
          d.searchTerm = $('#jobSeeker_name').val();
        }
      },
      columns: [
        { data: 'profile_id' },
        { data: 'jobSeeker_name' }, 
        { data: 'email' },
        { data: 'phone' },
        { data: 'address' },
        { data: 'education' },
        { data: 'experience' },
        { data: 'skills' },
        {
          data: null,
          render: function (data, type, row) {
            return `<button class="btn btn-primary" onclick="report(${row.profile_id})">View</button>`;
          }
        }
      ],
      paging: true,
      searching: false,
      ordering: true,
      responsive: true,
      initComplete: function () {
        $('#jobSeeker_table').show();
      }
    });
  } else {
    jobSeekerTable.ajax.reload(null, false);
  }
}

function report(id) {
  window.open('jobSeeker_print.php?id=' + id, '_blank');
}

</script>
    
    <!-- bootstap bundle js -->
    <!-- slimscroll js -->
    <script src="../assets/vendor/slimscroll/jquery.slimscroll.js"></script>
    <!-- main js -->
    <script src="../assets/libs/js/main-js.js"></script>
</body>
</html>