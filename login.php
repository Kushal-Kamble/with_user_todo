<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - ToDo AI</title>

  <!-- ✅ Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  
  <!-- ✅ Toastify -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
  
  <!-- ✅ Custom CSS -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="login-card text-center">
          
          <!-- 🖼 Logo -->
          <div class="mb-4">
            <img src="images/mitsde-logo.svg" alt="MITSDE Logo" class="login-logo">
          </div>

          <!-- 🔐 Title -->
          <h3 class="login-title mb-4">🔐 Login to ToDo AI</h3>

          <!-- 📧 Login Form -->
          <form action="php/auth.php" method="POST" class="text-start">
            <div class="mb-3">
              <label class="form-label">📧 Email</label>
              <input type="email" name="email" required class="form-control" placeholder="Enter your email">
            </div>
            <div class="mb-3">
              <label class="form-label">🔒 Password</label>
              <input type="password" name="password" required class="form-control" placeholder="Enter your password">
            </div>
            <button type="submit" name="login" class="btn btn-brand w-100">Login</button>
          </form>

          <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  <!-- ✅ Toastify Alerts -->
  <?php if (isset($_GET['success'])): ?>
  <script>
    Toastify({
      text: "🎉 Registration Successful! Please login.",
      duration: 4000,
      gravity: "top",
      position: "center",
      backgroundColor: "#28a745",
      stopOnFocus: true,
    }).showToast();
  </script>
  <?php endif; ?>

  <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
  <script>
    Toastify({
      text: "❌ Invalid email or password!",
      duration: 4000,
      gravity: "top",
      position: "center",
      backgroundColor: "#dc3545",
      stopOnFocus: true,
    }).showToast();
  </script>
  <?php endif; ?>

  <?php if (isset($_GET['error']) && $_GET['error'] == "exists"): ?>
  <script>
    Toastify({
      text: "⚠️ Email already exists. Try another!",
      duration: 4000,
      gravity: "top",
      position: "center",
      backgroundColor: "#ffc107",
      stopOnFocus: true,
    }).showToast();
  </script>
  <?php endif; ?>

  <?php if (isset($_GET['login']) && isset($_GET['user'])): ?>
  <script>
    const user = decodeURIComponent("<?= $_GET['user'] ?>");
    Toastify({
      text: `🎉 Welcome back, ${user}!`,
      duration: 4000,
      gravity: "top",
      position: "center",
      backgroundColor: "#28a745",
      stopOnFocus: true,
    }).showToast();
  </script>
  <?php endif; ?>

</body>
</html>
