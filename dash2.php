<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}
$user = $_SESSION['user'];
$initial = strtoupper(substr($user['name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      background: linear-gradient(to right, #1e1e2f, #2e2b45);
      color: #fff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .navbar-custom {
      background: linear-gradient(to right, #1c1c3c, #362646);
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }
    .navbar-logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .navbar-logo img {
      height: 30px;
    }
    .toast-login {
      background: linear-gradient(90deg, #00f2fe, #4facfe);
      color: #000;
      padding: 8px 16px;
      border-radius: 30px;
      font-weight: 600;
      display: inline-block;
    }
    .user-profile {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .user-initial {
      background-color: #6c5ce7;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      font-weight: bold;
      color: #fff;
    }
    .dashboard-card {
      background: #fff;
      color: #000;
      border-radius: 16px;
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
      padding: 30px;
      margin-top: 30px;
    }
    .btn-brand {
      background-color: #fe9e43;
      color: white;
      font-weight: 600;
      border-radius: 8px;
      padding: 10px 20px;
      border: none;
    }
    .btn-brand:hover {
      background-color: #e88b2a;
    }
  </style>
</head>
<body>

<nav class="navbar-custom">
  <div class="navbar-logo">
    <img src="images/mitsde-logo.svg" alt="MITSDE Logo">
    <h5 class="mb-0 text-white">ToDo AI</h5>
  </div>

  <?php if (isset($_GET['login']) && $_GET['login'] == 1): ?>
  <div class="toast-login">🎉 Welcome <?= htmlspecialchars($user['name']) ?>! You are logged in.</div>
  <?php endif; ?>

  <div class="user-profile">
    <div class="user-initial"><?= $initial ?></div>
    <div class="text-white">
      <strong><?= htmlspecialchars($user['name']) ?></strong><br>
      <small class="text-muted"><?= htmlspecialchars($user['role']) ?></small>
    </div>
    <a href="php/auth.php?logout=1" class="btn btn-outline-light"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
  </div>
</nav>

<div class="container">
  <div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4><i class="fas fa-tasks me-2"></i>Your Tasks</h4>
      <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#taskModal">+ Add Task</button>
    </div>
    <div id="taskList" class="row"></div>
  </div>
</div>

<!-- Add/Edit Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="taskForm">
        <div class="modal-header">
          <h5 class="modal-title">Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="task_id" id="task_id">
          <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

          <div class="mb-2"><label>Title</label><input name="title" class="form-control" required></div>
          <div class="mb-2"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
          <div class="mb-2"><label>Start Date</label><input type="date" name="start_date" class="form-control" required></div>
          <div class="mb-2"><label>Deadline</label><input type="date" name="deadline" class="form-control" required></div>
          <div class="mb-2"><label>Priority</label>
            <select name="priority" class="form-control">
              <option>Low</option>
              <option>Medium</option>
              <option>High</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-brand" type="submit">Save Task</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="js/script.js"></script>
<script src="voice/voice.js"></script>

<?php if (isset($_GET['login']) && $_GET['login'] == 1): ?>
<script>
  Toastify({
    text: "🎉 Welcome <?= addslashes($user['name']) ?>! You are logged in.",
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
