<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - ToDo AI</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <!-- Toastify -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">

  <style>
    body {
      background-color: #f7f9fc;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
      background: linear-gradient(90deg, #4b6cb7, #182848);
      color: white;
    }
    .navbar .navbar-brand, .navbar a, .navbar span {
      color: white !important;
    }
    .card-summary {
      border: none;
      border-radius: 1rem;
      padding: 20px;
      color: white;
    }
    .card-total { background: #5a67d8; }
    .card-pending { background: #ed8936; }
    .card-completed { background: #38a169; }
    .task-card {
      border-left: 6px solid #ccc;
      border-radius: 10px;
      padding: 20px;
      background-color: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      margin-bottom: 15px;
    }
    .priority-high { color: #e53e3e; font-weight: bold; }
    .priority-medium { color: #dd6b20; font-weight: bold; }
    .priority-low { color: #38a169; font-weight: bold; }
    .badge-overdue {
      background: #e53e3e;
      color: white;
      padding: 3px 8px;
      border-radius: 12px;
      font-size: 0.75rem;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg px-4 py-3 shadow">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <h4 class="navbar-brand fw-bold">ToDo AI</h4>
    <div class="d-flex align-items-center gap-3">
      <span><i class="fa-solid fa-user-circle me-1"></i> <?= $user['name'] ?> (<?= $user['role'] ?>)</span>
      <a href="php/auth.php?logout=1" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <!-- Summary Cards -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card-summary card-total">
        <h5>Total Tasks</h5>
        <h2>3</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card-summary card-pending">
        <h5>Pending</h5>
        <h2>2</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card-summary card-completed">
        <h5>Completed</h5>
        <h2>1</h2>
      </div>
    </div>
  </div>

  <!-- Tasks Section -->
  <h5 class="mb-3">Your Tasks</h5>
  <div id="taskList">
    <div class="task-card border-start-danger">
      <h6 class="mb-1">Complete project documentation <span class="float-end priority-high">High</span></h6>
      <p class="text-muted">Write comprehensive documentation for the new feature</p>
      <p class="mb-0"><i class="fa-regular fa-calendar me-1"></i> Due: 1/15/2025 <span class="badge-overdue ms-2">Overdue</span></p>
    </div>

    <div class="task-card border-start-warning">
      <h6 class="mb-1">Review code changes <span class="float-end priority-medium">Medium</span></h6>
      <p class="text-muted">Review pull requests from team members</p>
      <p class="mb-0"><i class="fa-regular fa-calendar me-1"></i> Due: 1/10/2025</p>
    </div>

    <div class="task-card border-start-success">
      <h6 class="mb-1">Update dependencies <span class="float-end priority-low">Low</span></h6>
      <p class="text-muted">Update all npm packages to latest versions</p>
      <p class="mb-0"><i class="fa-regular fa-calendar me-1"></i> Due: 1/12/2025 <span class="badge-overdue ms-2">Overdue</span></p>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<?php if (isset($_GET['login']) && $_GET['login'] == 1): ?>
<script>
  Toastify({
    text: "🎉 Welcome <?= addslashes($user['name']) ?>! You are logged in.",
    duration: 4000,
    gravity: "top",
    position: "center",
    backgroundColor: "#38a169",
    stopOnFocus: true,
  }).showToast();
</script>
<?php endif; ?>
</body>
</html>
