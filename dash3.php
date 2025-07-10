<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      background-color: #f6f8fc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .top-bar {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
    }
    .top-bar .logo-section {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .top-bar .logo-section span {
      font-weight: 600;
      font-size: 20px;
    }
    .summary-card {
      background: #fff;
      padding: 20px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      text-align: center;
    }
    .task-section {
      background: #fff;
      padding: 25px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
      margin-top: 20px;
    }
    .task-card {
      border-radius: 16px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
      position: relative;
    }
    .task-card.overdue {
      background-color: #ffe5e5;
      border-left: 5px solid #ff4d4f;
    }
    .task-card.complete {
      background-color: #e6fffb;
      border-left: 5px solid #52c41a;
      text-decoration: line-through;
    }
    .task-card .actions {
      position: absolute;
      right: 15px;
      bottom: 15px;
    }
    .badge-priority {
      font-size: 12px;
      padding: 4px 8px;
      border-radius: 8px;
    }
    .badge-high { background-color: #ff4d4f; color: white; }
    .badge-medium { background-color: #ffc107; color: black; }
    .badge-low { background-color: #28a745; color: white; }
    .voice-btn {
      position: fixed;
      right: 20px;
      bottom: 20px;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      font-size: 24px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
<body>

<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}
$user = $_SESSION['user'];
?>

<div class="container mt-4">
  <div class="top-bar">
    <div class="logo-section">
      <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">AI</div>
      <div>
        <span>ToDo AI</span><br>
        <small class="text-muted">Smart task management</small>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span><i class="fas fa-user"></i> <?= $user['name'] ?> (<?= $user['role'] ?>)</span>
      <a href="php/auth.php?logout=1" class="btn btn-danger">Logout</a>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="summary-card">
        <h5>Total Tasks</h5>
        <h2 id="totalTasks">0</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="summary-card">
        <h5>Pending</h5>
        <h2 id="pendingTasks">0</h2>
      </div>
    </div>
    <div class="col-md-4">
      <div class="summary-card">
        <h5>Completed</h5>
        <h2 id="completedTasks">0</h2>
      </div>
    </div>
  </div>

  <div class="task-section mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4><i class="fas fa-tasks me-2"></i>Your Tasks</h4>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal"><i class="fas fa-plus"></i> Add Task</button>
    </div>
    <div id="taskList" class="row"></div>
  </div>
</div>

<!-- Voice Button -->
<button class="btn btn-warning voice-btn" id="voiceBtn"><i class="fas fa-microphone"></i></button>

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
          <button class="btn btn-success" type="submit">Save Task</button>
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
