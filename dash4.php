<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ToDo AI - Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <style>
    :root {
      --primary: #6366f1;
      --danger: #f43f5e;
      --success: #22c55e;
      --warning: #facc15;
      --light: #f9fafb;
      --gray: #6b7280;
    }
    body {
      background-color: #f3f4f6;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
      background-color: #fff;
      border-bottom: 1px solid #e5e7eb;
    }
    .navbar-brand span {
      font-weight: 600;
      font-size: 1.25rem;
    }
    .summary-card {
      background-color: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .task-card {
      border-left: 4px solid transparent;
      border-radius: 10px;
      background-color: #fff;
      padding: 15px;
      margin-bottom: 20px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }
    .task-card.overdue {
      background-color: #fef2f2;
      border-color: var(--danger);
    }
    .task-card.complete {
      background-color: #ecfdf5;
      border-color: var(--success);
      text-decoration: line-through;
    }
    .priority-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 12px;
    }
    .priority-high { background-color: var(--danger); color: white; }
    .priority-medium { background-color: var(--warning); color: black; }
    .priority-low { background-color: var(--success); color: white; }
    .summary-icon {
      font-size: 1.5rem;
    }
    .btn-add {
      background: linear-gradient(to right, #6366f1, #9333ea);
      color: white;
    }
    .btn-add:hover {
      background: linear-gradient(to right, #4f46e5, #7e22ce);
    }
    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .avatar {
      width: 40px;
      height: 40px;
      background-color: #6366f1;
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
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
  <nav class="navbar px-4 py-2 d-flex justify-content-between align-items-center">
    <div>
      <span class="navbar-brand">ToDo AI</span>
      <small class="text-muted d-block" style="margin-top: -5px">Smart task management</small>
    </div>
    <div class="d-flex align-items-center gap-3">
      <div class="user-info">
        <div class="avatar"><?= strtoupper($user['name'][0]) ?></div>
        <div>
          <div class="fw-semibold"><?= $user['name'] ?></div>
          <small class="text-muted"><?= $user['role'] ?></small>
        </div>
      </div>
      <a href="php/auth.php?logout=1" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </nav>

  <div class="container py-4">
    <div class="row mb-3">
      <div class="col-md-4">
        <div class="summary-card">
          <div>Total Tasks</div>
          <div class="text-primary summary-icon"><i class="fas fa-clipboard-check"></i> 3</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="summary-card">
          <div>Pending</div>
          <div class="text-warning summary-icon"><i class="fas fa-clock"></i> 2</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="summary-card">
          <div>Completed</div>
          <div class="text-success summary-icon"><i class="fas fa-check-circle"></i> 1</div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5><i class="fas fa-list-check"></i> Your Tasks</h5>
      <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#taskModal">+ Add Task</button>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="task-card overdue">
          <div class="d-flex justify-content-between">
            <strong>Complete project documentation</strong>
            <span class="priority-badge priority-high">High</span>
          </div>
          <p class="text-muted small">Write comprehensive documentation for the new feature</p>
          <p class="small"><i class="far fa-calendar-alt"></i> Due: 1/15/2025 <span class="badge bg-danger">Overdue</span></p>
          <div class="text-end">
            <i class="fas fa-edit text-primary me-2"></i>
            <i class="fas fa-trash text-danger"></i>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="task-card complete">
          <div class="d-flex justify-content-between">
            <strong>Review code changes</strong>
            <span class="priority-badge priority-medium">Medium</span>
          </div>
          <p class="text-muted small">Review pull requests from team members</p>
          <p class="small"><i class="far fa-calendar-alt"></i> Due: 1/10/2025</p>
          <div class="text-end">
            <i class="fas fa-edit text-primary me-2"></i>
            <i class="fas fa-trash text-danger"></i>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="task-card overdue">
          <div class="d-flex justify-content-between">
            <strong>Update dependencies</strong>
            <span class="priority-badge priority-low">Low</span>
          </div>
          <p class="text-muted small">Update all npm packages to latest versions</p>
          <p class="small"><i class="far fa-calendar-alt"></i> Due: 1/12/2025 <span class="badge bg-danger">Overdue</span></p>
          <div class="text-end">
            <i class="fas fa-edit text-primary me-2"></i>
            <i class="fas fa-trash text-danger"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
