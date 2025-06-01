<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="assets/vendor/jquery/jquery-3.3.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-blue-400 to-purple-500">
  <div class="bg-white shadow-lg rounded-lg p-6 w-[900px]">
    <h2 class="text-xl font-bold text-center text-gray-700 mb-4">Signup</h2>
    <form class="grid grid-cols-2 gap-4" method="POST" id="signupForm">
      <input type="text" name="fullName" placeholder="Full Name" class="p-2 border rounded-md" />
      <select name="gender" class="p-2 border rounded-md">
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
      </select>
      <input type="date" name="DOB" placeholder="Date of Birth" class="p-2 border rounded-md" />
      <input type="text" name="phone" placeholder="Phone" class="p-2 border rounded-md" />
      <input type="email" name="email" placeholder="Email" class="p-2 border rounded-md" />
      <input type="text" name="address" placeholder="Address" class="p-2 border rounded-md" />
      <input type="text" name="userName" placeholder="Username" class="p-2 border rounded-md" />
      <input type="password" name="password" placeholder="Password" class="p-2 border rounded-md" />
      <input type="password" name="confirmPassword" placeholder="Confirm Password" class="p-2 border rounded-md" />
      <button type="submit" class="col-span-2 bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600">Signup</button>
    </form>
    <p class="text-center mt-2">Already have an account? <a href="login.php" class="text-blue-500">Signin</a></p>
  </div>

  <script>
    $('#signupForm').submit(function (e) {
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: 'registerOperation.php?url=registration',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (res) {
          if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: res.message }).then(() => {
              window.location.href = 'login.php';
            });
          } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message });
          }
        },
        error: function () {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Server error!' });
        }
      });
    });
  </script>
</body>
</html>
