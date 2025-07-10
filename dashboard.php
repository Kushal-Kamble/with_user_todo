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
</head>

<body>

  <?php
  session_start();
  if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
  }
  $user = $_SESSION['user'];

  include 'php/db.php';

  // ✅ Task Counts
  $user_id = $user['id'];

  $totalTasksQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tasks WHERE user_id = $user_id");
  $pendingTasksQuery = mysqli_query($conn, "SELECT COUNT(*) AS pending FROM tasks WHERE user_id = $user_id AND completed = 0");
  $completedTasksQuery = mysqli_query($conn, "SELECT COUNT(*) AS completed FROM tasks WHERE user_id = $user_id AND completed = 1");

  $totalTasks = mysqli_fetch_assoc($totalTasksQuery)['total'];
  $pendingTasks = mysqli_fetch_assoc($pendingTasksQuery)['pending'];
  $completedTasks = mysqli_fetch_assoc($completedTasksQuery)['completed'];
  ?>

  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 py-2">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="images/mitsde-logo.svg" alt="Logo" class="dashboard-logo me-2">
        <span class="fs-5 fw-semibold">ToDo AI</span>
      </a>
      <div class="d-flex align-items-center">
        <span class="me-3 text-dark fw-semibold">
          <i class="fas fa-user-circle me-1"></i> <?= $user['name'] ?> (<?= $user['role'] ?>)
        </span>
        <a href="php/auth.php?logout=1" class="btn btn-outline-danger btn-sm">
          <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
      </div>
    </div>
  </nav>


  <div class="container mt-4">
    <div class="row mb-4" id="statsCards">
      <div class="col-md-4 mb-3">
        <div class="stats-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stats-number text-primary" id="totalTasks"><?= $totalTasks ?></div>
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
              <div class="stats-number text-warning" id="pendingTasks"><?= $pendingTasks ?></div>
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
              <div class="stats-number text-success" id="completedTasks"><?= $completedTasks ?></div>
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



  <div class="container ">
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
            <button class="btn btn-success" type="submit" id="saveBtn">
              <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="saveLoader"></span>
              <span id="saveBtnText">Save Task</span>
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <!-- 🎙️ Voice Button -->
  <!-- 🎙️ Voice Button -->
  <div class="position-fixed bottom-0 end-0 p-4">
  <button class="btn" id="voiceBtn">
    <i class="fas fa-microphone"></i> Ask Task Status
  </button>
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

  <!-- 🔔 Toast for AI voice response -->
 

  <!-- 🤖 Modal for AI Agent Task Summary -->
  <div class="modal fade" id="taskModalVoice" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg border-0 rounded-4" style="background-color: #fff0d2;">
        <div class="modal-header" style="background-color: #ff9e42; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
          <h5 class="modal-title text-white">
            🤖 आपका <strong>AI एजेंट</strong>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <h5 id="modalTaskTitle" class="text-dark fw-bold mb-2"></h5>
          <p id="modalTaskDesc" class="text-secondary mb-3"></p>
          <p class="mb-0">
            <strong class="text-dark">⏰ डेडलाइन:</strong>
            <span id="modalTaskDeadline" class="text-danger fw-semibold"></span>
          </p>
        </div>
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
  $(document).ready(function () {
    // 🎙️ Voice button functionality
    $('#voiceBtn').on('click', function () {
      $('#toastMessage').text('Voice recognition activated...');
      const toastVoice = new bootstrap.Toast($('#toastVoice')[0]);
      toastVoice.show();
    });

    // 🎉 Welcome Toast using Toastify
    Toastify({
      text: "🎉 Welcome <?= addslashes($user['name']) ?>",
      duration: 4000,
      gravity: "top",
      position: "center",
      backgroundColor: "#28a745",
      stopOnFocus: true,
    }).showToast();
  });
</script>

  <?php endif; ?>



</body>

</html>