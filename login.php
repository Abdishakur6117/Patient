<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert@2.1.2/dist/sweetalert.min.js"></script>

    <!-- jQuery 3.5.1 -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <style>
    body {
      background-image: url('https://www.webmediatricks.com/uploaded_files/product/1703848338.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      min-height: 100vh;
    }

    .card {
      background-color: rgba(255, 255, 255, 0.95);
    }
  </style>

</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 350px;">
        <h3 class="text-center">Login</h3>
        <form id="loginForm">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" >
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" >
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary  form-control">Login</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
  $("#loginForm").on("submit", function (e) {
    e.preventDefault();
    let formData = $(this).serialize();

    $.ajax({
      type: "POST",
      url: "loginOperation.php?url=login",
      data: formData,
      dataType: "json",
      success: function (res) {
        if (res.status === "success") {
          swal({
            title: "Login Successful!",
            text: res.message,
            icon: "success",
            button: "OK",
          }).then(function () {
            let role = res.role;
            if (role === "admin") {
              window.location.href = "index.php";
            } else if (role === "employee") {
              window.location.href = "employee_dashboard.php";
            } else if (role === "job_seeker") {
              window.location.href = "jobSeeker_dashboard.php";
            }
          });
        } else {
          swal({
            title: "Error!",
            text: res.message,
            icon: "error",
            button: "Try Again",
          });
        }
      },
      error: function () {
        swal({
          title: "Error!",
          text: "An error occurred while submitting the form.",
          icon: "error",
          button: "Try Again",
        });
      }
    });
  });
});
</script>

</body>
</html>
