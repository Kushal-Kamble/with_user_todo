<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register - ToDo AI</title>

  <!-- Bootstrap & Custom CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">

  <!-- ✅ Toastify CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="login-card">
        <img src="images/mitsde-logo.svg" alt="MITSDE Logo" class="login-logo mb-3">
        <h6 class="text-center login-title mb-4">📝 Create Your ToDo AI Account</h6>
        <form action="php/auth.php" method="POST">
          <div class="mb-2">
            <label class="form-label">👤 Full Name</label>
            <input type="text" name="name" required class="form-control" placeholder="Enter your name">
          </div>
          <div class="mb-2">
            <label class="form-label">📧 Email</label>
            <input type="email" name="email" required class="form-control" placeholder="Enter your email">
          </div>
          <div class="mb-2">
            <label class="form-label">🔒 Password</label>
            <input type="password" name="password" required class="form-control" placeholder="Create a password">
          </div>
          <div class="mb-2">
            <label class="form-label">🎭 Role</label>
            <select name="role" class="form-control form-select" required>
              <option value="user">User</option>
              <!-- <option value="manager">Manager</option> -->
              <option value="admin">Admin</option>
            </select>
          </div>
          <button type="submit" name="register" class="btn btn-brand w-100 mt-3">Register</button>
        </form>
        <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<!-- ✅ Toastify Alerts -->
<script>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  Toastify({
    text: "🎉 Registration Successful!",
    duration: 4000,
    gravity: "top",
    position: "center",
    backgroundColor: "#28a745",
    className: "rounded",
  }).showToast();
<?php endif; ?>

<?php if (isset($_GET['login']) && $_GET['login'] == 1): ?>
  Toastify({
    text: "🎉 Logged in successfully!",
    duration: 4000,
    gravity: "top",
    position: "center",
    backgroundColor: "#007bff",
    className: "rounded",
  }).showToast();
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
  Toastify({
    text: "❌ Invalid email or password!",
    duration: 4000,
    gravity: "top",
    position: "center",
    backgroundColor: "#dc3545",
    className: "rounded",
  }).showToast();
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
  Toastify({
    text: "⚠️ Email already registered!",
    duration: 4000,
    gravity: "top",
    position: "center",
    backgroundColor: "#ffc107",
    className: "rounded",
  }).showToast();
<?php endif; ?>
</script>

</body>
</html>
