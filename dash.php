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

  <!-- ✅ Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- ✅ Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- ✅ Toastify -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />

  <!-- ✅ Custom CSS -->
  <link rel="stylesheet" href="css/style.css">

  <style>
    body {
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #fff;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
    }

    .dashboard-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
      padding: 30px;
      transition: 0.3s ease;
    }

    .dashboard-card:hover {
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
    }

    .btn-brand {
      background: linear-gradient(to right, #fe9e43, #fd7743);
      color: #fff;
      border: none;
      border-radius: 12px;
      padding: 10px 20px;
      font-weight: 600;
      transition: all 0.3s ease-in-out;
    }

    .btn-brand:hover {
      background: linear-gradient(to right, #fd7743, #fe9e43);
      transform: scale(1.05);
    }

    .dashboard-logo {
      max-width: 140px;
      height: auto;
    }

    .modal-content {
      background-color: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(8px);
    }

    .form-control,
    .form-select {
      border-radius: 10px;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-light bg-white shadow-sm px-4">
    <div class="d-flex align-items-center">
      <img src="images/mitsde-logo.svg" alt="Logo" class="dashboard-logo me-3">
      <span class="navbar-brand mb-0 h1">👋 Hello, <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)</span>
    </div>
    <a href="php/auth.php?logout=1" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
  </nav>

  <div class="container py-4">
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
              <select name="priority" class="form-select">
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success" type="submit">Save Task</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- 🎙️ Voice Button -->
  <div class="position-fixed bottom-0 end-0 p-4">
    <button class="btn btn-warning" id="voiceBtn"><i class="fas fa-microphone"></i> Ask Task Status</button>
  </div>

  <!-- 🔔 Toast for AI voice response -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
    <div id="toastVoice" class="toast text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastMessage"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
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
      stopOnFocus: true
    }).showToast();
  </script>
  <?php endif; ?>

</body>
</html>
