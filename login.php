<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS (Online) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert JS (Online) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert/dist/sweetalert.min.js"></script>

    <!-- jQuery (Online) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg" style="width: 350px;">
        <h3 class="text-center">Login</h3>
        <form id="loginForm">
            <div class="mb-3">
                <label for="email" class="form-label">Username or Email</label>
                <input type="text" class="form-control" id="email" name="email" >
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" >
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary  form-control">Login</button>
            </div>
        </form>
        <p class="text-center text-sm text-gray-600 mt-2">
        Don't have an account?
        <a href="register.php" class="text-green-500 font-semibold"
          >Signup</a
        >
      </p>
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
        console.log(res);  // Log the response

        if (res.status === "success") {
          swal({
            title: "Login Successful!",
            text: res.message,
            icon: "success",
            button: "OK",
          }).then(function () {
            // Redirect based on the role
            let role = res.role.toLowerCase();
            if (role === "admin") {
              window.location.href = "index.php";
            } else if (role === "staff") {
              window.location.href = "staff_dashboard.php";
            } else if (role === "passenger") {
              window.location.href = "passenger_dashboard.php";
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
      error: function (jqXHR, textStatus, errorThrown) {
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
