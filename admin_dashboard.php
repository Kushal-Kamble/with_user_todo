<?php 
session_start();
include 'php/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  header('Location: login.php');
  exit();
}
$user = $_SESSION['user'];

// ✅ Summary Data
$summarySql = "SELECT 
  COUNT(*) as total, 
  SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed,
  SUM(CASE WHEN completed = 0 THEN 1 ELSE 0 END) as pending
FROM tasks";
$summaryResult = mysqli_query($conn, $summarySql);
$summary = mysqli_fetch_assoc($summaryResult);

// ✅ Priority Distribution Data
$prioritySql = "SELECT priority, COUNT(*) as count FROM tasks GROUP BY priority";
$priorityResult = mysqli_query($conn, $prioritySql);
$priorityData = [];
while($row = mysqli_fetch_assoc($priorityResult)) {
  $priorityData[] = $row;
}

// ✅ User Task Distribution
$userTaskSql = "SELECT users.name, COUNT(tasks.id) as task_count 
                FROM users 
                LEFT JOIN tasks ON users.id = tasks.user_id 
                GROUP BY users.id, users.name
                ORDER BY task_count DESC";
$userTaskResult = mysqli_query($conn, $userTaskSql);
$userTaskData = [];
while($row = mysqli_fetch_assoc($userTaskResult)) {
  $userTaskData[] = $row;
}

// ✅ Task List
$taskSql = "SELECT tasks.*, users.name as user_name, users.email as user_email FROM tasks 
            LEFT JOIN users ON tasks.user_id = users.id 
            ORDER BY tasks.deadline ASC";
$taskResult = mysqli_query($conn, $taskSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - ToDo AI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
  <link rel="stylesheet" type="text/css" href="admin.css">
  
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm px-4 py-2">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="images/mitsde-logo.svg" alt="Logo" class="dashboard-logo me-2">
        <span class="fs-5 fw-semibold">ToDo AI - Admin</span>
      </a>
      <div class="d-flex align-items-center">
        <span class="me-3 text-dark fw-semibold">
          <i class="fas fa-user-shield me-1"></i> <?= htmlspecialchars($user['name']) ?> (Admin)
        </span>
        <a href="php/auth.php?logout=1" class="btn btn-outline-danger btn-sm">
          <i class="fas fa-sign-out-alt me-1"></i> Logout
        </a>
      </div>
    </div>
  </nav>

  <!-- Dashboard -->
  <div class="container mt-4">
    <!-- Stats Cards -->
    <div class="row mb-4">
      <div class="col-md-4 mb-3">
        <div class="stats-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stats-number text-primary"><?= $summary['total'] ?></div>
              <div class="stats-label">Total Tasks</div>
            </div>
            <div class="stats-icon icon-total"><i class="fas fa-tasks"></i></div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="stats-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stats-number text-warning"><?= $summary['pending'] ?></div>
              <div class="stats-label">Pending</div>
            </div>
            <div class="stats-icon icon-pending"><i class="fas fa-clock"></i></div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4 mb-3">
        <div class="stats-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stats-number text-success"><?= $summary['completed'] ?></div>
              <div class="stats-label">Completed</div>
            </div>
            <div class="stats-icon icon-completed"><i class="fas fa-check-circle"></i></div>
          </div>
        </div>
      </div>
    </div>

    <!-- 📈 Charts Section -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="dashboard-card">
          <h5><i class="fas fa-chart-pie me-2"></i>Task Status Distribution</h5>
          <div class="chart-container">
            <canvas id="statusPieChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="dashboard-card">
          <h5><i class="fas fa-chart-bar me-2"></i>Priority Distribution</h5>
          <div class="chart-container">
            <canvas id="priorityBarChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- User Task Distribution Chart -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="dashboard-card">
          <h5><i class="fas fa-chart-line me-2"></i>User Task Distribution</h5>
          <div class="chart-container">
            <canvas id="userTaskChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- All Tasks Table -->
    <div class="dashboard-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-list-check me-2"></i>All User Tasks</h4>
        <div class="mb-3 d-flex gap-2">
          <a href="php/export_tasks.php?format=csv" class="btn btn-success">
            <i class="fas fa-file-csv me-1"></i> Export to CSV
          </a>
          <a href="php/export_tasks.php?format=excel" class="btn btn-primary">
            <i class="fas fa-file-excel me-1"></i> Export to Excel
          </a>
        </div>


      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>User</th>
              <th>Priority</th>
              <th>Start</th>
              <th>Deadline</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- HTML TABLE -->
<?php while($task = mysqli_fetch_assoc($taskResult)) { ?>
<tr>
  <td><?= $task['id'] ?></td>
  <td><?= htmlspecialchars($task['title']) ?></td>
  <td><?= htmlspecialchars($task['user_name']) ?></td>
  <td><?= $task['priority'] ?></td>
  <td><?= $task['start_date'] ?></td>
  <td><?= $task['deadline'] ?></td>
  <td><?= $task['completed'] ? '<span class="badge bg-success">Completed</span>' : '<span class="badge bg-danger">Pending</span>' ?></td>
  <td>
     <div class="action-buttons">
  <!-- Edit Button -->
  <button class="btn btn-edit" onclick="editTask(<?= $task['id'] ?>)" title="Edit Task">
    <i class="fas fa-edit"></i>
  </button>

  <!-- Delete Button -->
  <button class="btn btn-delete" onclick="deleteTask(<?= $task['id'] ?>)" title="Delete Task">
    <i class="fas fa-trash"></i>
  </button>

  <!-- Send Reminder Button with Spinner -->
  <?php if (!$task['completed']) { ?>
    <button class="btn btn-sm  send-reminder-btn" style="background-color:rgb(252, 150, 54)" data-id="<?= $task['id'] ?>" id="sendReminderBtn<?= $task['id'] ?>">
      <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="reminderLoader<?= $task['id'] ?>"></span>
      <span class="ms-1 text-white" id="reminderBtnText<?= $task['id'] ?>"><i class="fas fa-envelope"></i> </span>
    </button>
  <?php } ?>
</div>

  </td>
</tr>
<?php } ?>

<!-- Loader -->
<div id="loaderOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:#fed0bc;z-index:9999;text-align:center;">
  <div style="position:relative;top:40%;font-size:20px;color:#007bff;">
    <div class="spinner-border text-primary" style="width:3rem;height:3rem;" role="status"></div>
    <div class="mt-3 fw-bold">Sending Reminder...</div>
  </div>
</div>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Edit Task Modal -->
  <div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editTaskForm">
            <input type="hidden" id="editTaskId" name="task_id">
            <div class="mb-3">
              <label for="editTitle" class="form-label">Title</label>
              <input type="text" class="form-control" id="editTitle" name="title" required>
            </div>
            <div class="mb-3">
              <label for="editPriority" class="form-label">Priority</label>
              <select class="form-select" id="editPriority" name="priority" required>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="editDeadline" class="form-label">Deadline</label>
              <input type="date" class="form-control" id="editDeadline" name="deadline" required>
            </div>
            <div class="mb-3">
              <label for="editStatus" class="form-label">Status</label>
              <select class="form-select" id="editStatus" name="completed">
                <option value="0">Pending</option>
                <option value="1">Completed</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="updateTask()">Update Task</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Welcome Toast (Bootstrap) -->
  <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1100;">
    <div id="welcomeToast" class="toast toast-welcome" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body text-center">
        <i class="fas fa-crown me-2"></i>
        🎉 Welcome <?= htmlspecialchars($user['name']) ?>! Admin Dashboard Loaded Successfully
      </div>
    </div>
  </div>

  <!-- Voice Toast -->
  <div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="toastVoice" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body" id="toastMessage">
        Voice recognition activated...
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
  <script src="js/script.js"></script>
  <script src="voice/voice.js"></script>

  <script>
    $(document).ready(function() {
      // Initialize Charts
      initializeCharts();

      // 🎙️ Voice button functionality
      $('#voiceBtn').on('click', function() {
        $('#toastMessage').text('Voice recognition activated...');
        const toastVoice = new bootstrap.Toast($('#toastVoice')[0]);
        toastVoice.show();
      });

      <?php if (isset($_GET['login']) && $_GET['login'] == 1): ?>
        // 🎉 Welcome Toast using Toastify
        // 🎉 Welcome Toast using Toastify
        Toastify({
          text: "🎉 Welcome <?= addslashes($user['name']) ?>! Admin Dashboard Ready",
          duration: 4000,
          gravity: "top",
          position: "center",
          backgroundColor: "#28a745",
          stopOnFocus: true,
        }).showToast();


        // Toastify({
        //   text: "🎉 Welcome <?= addslashes($user['name']) ?>! Admin Dashboard Ready",
        //   duration: 5000,
        //   gravity: "top",
        //   position: "center",
        //   backgroundColor: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
        //   stopOnFocus: true,
        //   className: "custom-toast",
        //   onClick: function(){} // Callback after click
        // }).showToast();
      <?php endif; ?>
    });

    // 📈 Initialize Charts
    function initializeCharts() {
      // Task Status Pie Chart
      const statusCtx = document.getElementById('statusPieChart').getContext('2d');
      new Chart(statusCtx, {
        type: 'pie',
        data: {
          labels: ['Completed', 'Pending'],
          datasets: [{
            data: [<?= $summary['completed'] ?>, <?= $summary['pending'] ?>],
            backgroundColor: ['#34a853', '#fbbc04'],
            borderWidth: 2,
            borderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });

      // Priority Bar Chart
      const priorityCtx = document.getElementById('priorityBarChart').getContext('2d');
      const priorityLabels = <?= json_encode(array_column($priorityData, 'priority')) ?>;
      const priorityCounts = <?= json_encode(array_column($priorityData, 'count')) ?>;

      new Chart(priorityCtx, {
        type: 'bar',
        data: {
          labels: priorityLabels,
          datasets: [{
            label: 'Number of Tasks',
            data: priorityCounts,
            backgroundColor: ['#34a853', '#fbbc04', '#ea4335'],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          }
        }
      });

      // User Task Distribution Chart
      const userTaskCtx = document.getElementById('userTaskChart').getContext('2d');
      const userNames = <?= json_encode(array_column($userTaskData, 'name')) ?>;
      const userTaskCounts = <?= json_encode(array_column($userTaskData, 'task_count')) ?>;

      new Chart(userTaskCtx, {
        type: 'bar',
        data: {
          labels: userNames,
          datasets: [{
            label: 'Tasks per User',
            data: userTaskCounts,
            backgroundColor: '#4285f4',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          }
        }
      });
    }

    // ✏️ Edit Task Function
    function editTask(taskId) {
      $.ajax({
        url: 'php/admin_actions.php',
        type: 'POST',
        data: {
          action: 'get_task',
          task_id: taskId
        },
        success: function(response) {
          const task = JSON.parse(response);
          $('#editTaskId').val(task.id);
          $('#editTitle').val(task.title);
          $('#editPriority').val(task.priority);
          $('#editDeadline').val(task.deadline);
          $('#editStatus').val(task.completed);
          $('#editTaskModal').modal('show');
        }
      });
    }

    // 💾 Update Task Function
    function updateTask() {
      const formData = new FormData();
      formData.append('action', 'update_task');
      formData.append('task_id', $('#editTaskId').val());
      formData.append('title', $('#editTitle').val());
      formData.append('priority', $('#editPriority').val());
      formData.append('deadline', $('#editDeadline').val());
      formData.append('completed', $('#editStatus').val());

      $.ajax({
        url: 'php/admin_actions.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if (response === 'success') {
            $('#editTaskModal').modal('hide');
            Toastify({
              text: "✅ Task updated successfully!",
              duration: 3000,
              gravity: "top",
              position: "center",
              backgroundColor: "#28a745"
            }).showToast();
            setTimeout(() => location.reload(), 1500);
          } else {
            Toastify({
              text: "❌ Error updating task!",
              duration: 3000,
              gravity: "top",
              position: "center",
              backgroundColor: "#dc3545"
            }).showToast();
          }
        }
      });
    }

    // 🗑️ Delete Task Function
    function deleteTask(taskId) {
      if (confirm('Are you sure you want to delete this task?')) {
        $.ajax({
          url: 'php/admin_actions.php',
          type: 'POST',
          data: {
            action: 'delete_task',
            task_id: taskId
          },
          success: function(response) {
            if (response === 'success') {
              Toastify({
                text: "🗑️ Task deleted successfully!",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "#28a745"
              }).showToast();
              setTimeout(() => location.reload(), 1500);
            } else {
              Toastify({
                text: "❌ Error deleting task!",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "#dc3545"
              }).showToast();
            }
          }
        });
      }
    }
  </script>

  <?php while($task = mysqli_fetch_assoc($taskResult)) { ?>
<tr>
  <td><?= $task['id'] ?></td>
  <td><?= htmlspecialchars($task['title']) ?></td>
  <td><?= htmlspecialchars($task['user_name']) ?></td>
  <td><?= $task['priority'] ?></td>
  <td><?= $task['start_date'] ?></td>
  <td><?= $task['deadline'] ?></td>
  <td><?= $task['completed'] ? '<span class="badge bg-success">Completed</span>' : '<span class="badge bg-danger">Pending</span>' ?></td>
  <td>
    <div class="action-buttons">
      <button class="btn btn-edit" onclick="editTask(<?= $task['id'] ?>)" title="Edit Task">
        <i class="fas fa-edit"></i>
      </button>
      <button class="btn btn-delete" onclick="deleteTask(<?= $task['id'] ?>)" title="Delete Task">
        <i class="fas fa-trash"></i>
      </button>
      <?php if (!$task['completed']) { ?>
        <a href="?remind=<?= $task['id'] ?>" class="btn btn-sm btn-info" title="Send Reminder">
          <i class="fas fa-envelope"></i>
        </a>
      <?php } ?>
    </div>
  </td>
</tr>
<?php } ?>

<?php if (isset($_GET['mail_sent']) && $_GET['mail_sent'] == 1): ?>
<script>
  Toastify({
    text: "📩 Reminder sent successfully!",
    duration: 4000,
    gravity: "top",
    position: "center",
    backgroundColor: "#198754",
    stopOnFocus: true,
  }).showToast();

  // Remove the parameter from URL after showing toast
  if (window.history.replaceState) {
    const url = new URL(window.location);
    url.searchParams.delete('mail_sent');
    window.history.replaceState({}, document.title, url.toString());
  }
</script>
<?php endif; ?>



 


  <!-- Custom Toast Styles -->
  <style>
    .custom-toast {
      border-radius: 12px !important;
      font-weight: 600 !important;
      font-size: 1.1rem !important;
      padding: 15px 20px !important;
    }
  </style>

  <script>
$(document).ready(function () {
  $('.send-reminder-btn').click(function () {
    const taskId = $(this).data('id');

    // Show spinner for this specific button
    $('#reminderLoader' + taskId).removeClass('d-none');
    $('#reminderBtnText' + taskId).text('Sending...');

    $.ajax({
      url: 'php/send_reminder.php',
      type: 'POST',
      data: { task_id: taskId },
      success: function (response) {
        $('#reminderLoader' + taskId).addClass('d-none');
        $('#reminderBtnText' + taskId).html('<i class="fas fa-envelope"></i> Send Reminder');

        if (response.trim() === 'success') {
          Toastify({
            text: "📩 Reminder sent successfully!",
            duration: 4000,
            gravity: "top",
            position: "center",
            backgroundColor: "#198754",
          }).showToast();
        } else {
          Toastify({
            text: "❌ Error: " + response,
            duration: 4000,
            gravity: "top",
            position: "center",
            backgroundColor: "#dc3545",
          }).showToast();
        }
      },
      error: function (xhr, status, error) {
        $('#reminderLoader' + taskId).addClass('d-none');
        $('#reminderBtnText' + taskId).html('<i class="fas fa-envelope"></i> Send Reminder');
        Toastify({
          text: "❌ AJAX Error: " + error,
          duration: 4000,
          gravity: "top",
          position: "center",
          backgroundColor: "#dc3545",
        }).showToast();
      },
    });
  });
});

</script>


</body>

</html>