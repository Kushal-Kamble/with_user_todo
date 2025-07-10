<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
$user = $_SESSION['user'];

require 'php/db.php';

// ✅ Fetch Overall Task Counts
$totalTasksQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tasks");
$pendingTasksQuery = mysqli_query($conn, "SELECT COUNT(*) AS pending FROM tasks WHERE completed = 0");
$completedTasksQuery = mysqli_query($conn, "SELECT COUNT(*) AS completed FROM tasks WHERE completed = 1");

$totalTasks = mysqli_fetch_assoc($totalTasksQuery)['total'];
$pendingTasks = mysqli_fetch_assoc($pendingTasksQuery)['pending'];
$completedTasks = mysqli_fetch_assoc($completedTasksQuery)['completed'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - ToDo AI</title>

  <!-- ✅ Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- ✅ Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

  <!-- ✅ Toastify -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" />

  <!-- ✅ Custom CSS -->
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body {
      background-color: #f8f9fa;
    }



    .dashboard-card,
    .stats-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      padding: 1.5rem;
      transition: 0.3s ease;
    }

    .dashboard-card:hover,
    .stats-card:hover {
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    }

    .dashboard-logo {
      height: 45px;
      /* ⬅️ Increase height as needed */
      width: auto;
    }


    .stats-card {
      border-radius: 16px;
      background-color: #fff;
      padding: 1.5rem;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease-in-out;
    }

    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
    }

    .stats-number {
      font-size: 2rem;
      font-weight: 700;
    }

    .stats-label {
      color: #6c757d;
      font-size: 1rem;
    }

    .stats-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      color: white;
    }

    /* Custom color backgrounds for icons */
    .icon-total {
      background-color: #4285f4;
    }

    .icon-pending {
      background-color: #fbbc04;
    }

    .icon-completed {
      background-color: #34a853;
    }


    .btn-brand {
      background: #fe9e43;
      color: #fff;
    }

    .btn-brand:hover {
      background: #5a32a3;
        color: #fff;
    }

    .card-body h5 {
      font-size: 1.2rem;
      font-weight: 600;
    }

    .card-body p {
      font-size: 0.9rem;
      margin-bottom: 0.5rem;
    }

     #voiceBtn {
    background-color: #f57421;
    color: #fff;
    border: none;
    font-weight: bold;
    font-size: 16px;
    padding: 12px 20px;
    border-radius: 50px;
    box-shadow: 0 4px 15px rgba(245, 116, 33, 0.4);
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease-in-out;
  }

  #voiceBtn:hover {
    background-color: #e9620f;
    box-shadow: 0 0 20px #f57421aa, 0 0 40px #f57421aa;
  }

  #voiceBtn::before {
    content: '';
    position: absolute;
    width: 200%;
    height: 200%;
    top: -50%;
    left: -50%;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 10%, transparent 60%);
    animation: pulse 2s infinite;
    border-radius: 50%;
  }

  @keyframes pulse {
    0% {
      transform: scale(0.8);
      opacity: 0.6;
    }
    70% {
      transform: scale(1.2);
      opacity: 0;
    }
    100% {
      transform: scale(0.8);
      opacity: 0;
    }
  }

  #voiceBtn i {
    margin-right: 8px;
    animation: bounce 2s infinite;
  }

  @keyframes bounce {
    0%, 100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-3px);
    }
  }
  </style>

  <style>
    /* Include your full custom styles here (same as user dashboard) */
    /* You can reuse the full <style> block from your existing code */
  </style>
</head>

<body>
  <!-- 🔒 Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 py-2">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="images/mitsde-logo.svg" alt="Logo" class="dashboard-logo me-2">
        <span class="fs-5 fw-semibold">ToDo AI</span>
      </a>
      <div class="d-flex align-items-center">
        <span class="me-3 text-dark fw-semibold">
          <i class="fas fa-user-shield me-1"></i> <?= $user['name'] ?> (Admin)
        </span>
        <a href="php/auth.php?logout=1" class="btn btn-outline-danger btn-sm">
          <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
      </div>
    </div>
  </nav>

  <!-- ✅ Task Stats -->
  <div class="container mt-4">
    <div class="row mb-4" id="statsCards">
      <div class="col-md-4 mb-3">
        <div class="stats-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stats-number text-primary"><?= $totalTasks ?></div>
              <div class="stats-label">Total Tasks</div>
            </div>
            <div class="stats-icon icon-total">
              <i class="fas fa-tasks"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="stats-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stats-number text-warning"><?= $pendingTasks ?></div>
              <div class="stats-label">Pending</div>
            </div>
            <div class="stats-icon icon-pending">
              <i class="fas fa-clock"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4 mb-3">
        <div class="stats-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stats-number text-success"><?= $completedTasks ?></div>
              <div class="stats-label">Completed</div>
            </div>
            <div class="stats-icon icon-completed">
              <i class="fas fa-check-circle"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ All Users' Tasks -->
  <div class="container">
    <div class="dashboard-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-users me-2"></i>All Users' Tasks</h4>
      </div>
      <div id="taskList" class="row">
        <?php
        $taskQuery = "SELECT tasks.*, users.name AS user_name FROM tasks 
                      LEFT JOIN users ON tasks.user_id = users.id 
                      ORDER BY tasks.deadline ASC";

        $result = mysqli_query($conn, $taskQuery);

        while ($task = mysqli_fetch_assoc($result)) {
          $priorityBadge = $task['priority'] === 'High' ? 'danger' : ($task['priority'] === 'Medium' ? 'warning' : 'success');
          $status = $task['completed'] ? '✅ Completed' : '❌ Pending';

          echo "<div class='col-md-4 mb-3'>
            <div class='card shadow-sm'>
              <div class='card-body'>
                <h5>{$task['title']} <span class='badge bg-$priorityBadge'>{$task['priority']}</span></h5>
                <p>{$task['description']}</p>
                <p><strong>User:</strong> {$task['user_name']}</p>
                <p><strong>Start:</strong> {$task['start_date']} | <strong>Deadline:</strong> {$task['deadline']}</p>
                <p>Status: $status</p>
              </div>
            </div>
          </div>";
        }
        ?>
      </div>
    </div>
  </div>

  

  <!-- 🎙️ Optional Voice Button or Toast if you want -->
  <!-- Include same JS libraries -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>
