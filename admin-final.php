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
$taskSql = "SELECT tasks.*, users.name as user_name FROM tasks 
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
  <style>
    :root {
      --primary-color: #FE9F42;
      --secondary-color: #FFF0D2;
      --accent-color: #FF8C00;
      --success-color: #28a745;
      --warning-color: #ffc107;
      --danger-color: #dc3545;
    }

    body {
      background: linear-gradient(135deg, var(--secondary-color) 0%, #fff 100%);
      min-height: 100vh;
    }

    .dashboard-logo {
      height: 45px;
    }

    .stats-card {
      border-radius: 20px;
      background: linear-gradient(135deg, #fff 0%, var(--secondary-color) 100%);
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(254, 159, 66, 0.1);
      transition: all 0.3s ease-in-out;
      border: 1px solid rgba(254, 159, 66, 0.1);
    }

    .stats-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(254, 159, 66, 0.2);
    }

    .stats-number {
      font-size: 2.5rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .stats-label {
      color: #666;
      font-size: 1.1rem;
      font-weight: 600;
    }

    .stats-icon {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      color: white;
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      box-shadow: 0 8px 20px rgba(254, 159, 66, 0.3);
    }

    .dashboard-card {
      background: linear-gradient(135deg, #ffffff 0%, #fefefe 100%);
      border-radius: 20px;
      box-shadow: 0 12px 35px rgba(254, 159, 66, 0.08);
      padding: 2rem;
      border: 1px solid rgba(254, 159, 66, 0.1);
      transition: all 0.3s ease;
    }

    .dashboard-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 15px 45px rgba(254, 159, 66, 0.12);
    }

    .dashboard-card h4, .dashboard-card h5 {
      color: var(--primary-color);
      font-weight: 700;
    }

    /* Export Buttons */
    .export-buttons {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    .btn-export {
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      border: none;
      color: white;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(254, 159, 66, 0.3);
    }

    .btn-export:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(254, 159, 66, 0.4);
      color: white;
    }

    .btn-export i {
      margin-right: 8px;
    }

    /* Chart Containers */
    .chart-container {
      position: relative;
      height: 350px;
      margin: 20px 0;
      background: linear-gradient(135deg, #fff 0%, var(--secondary-color) 100%);
      border-radius: 15px;
      padding: 20px;
      box-shadow: inset 0 2px 10px rgba(254, 159, 66, 0.05);
    }

    /* Custom Table Styles */
    .table {
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(254, 159, 66, 0.1);
    }

    .table thead th {
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      color: white;
      font-weight: 600;
      border: none;
      padding: 15px;
    }

    .table tbody tr:hover {
      background-color: var(--secondary-color);
    }

    .action-buttons {
      display: flex;
      gap: 8px;
    }

    .btn-edit {
      background: linear-gradient(135deg, var(--warning-color), #f39c12);
      border: none;
      color: white;
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .btn-delete {
      background: linear-gradient(135deg, var(--danger-color), #c0392b);
      border: none;
      color: white;
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .btn-edit:hover, .btn-delete:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    /* Priority Badges */
    .badge {
      border-radius: 20px;
      padding: 8px 15px;
      font-weight: 600;
    }

    .bg-danger {
      background: linear-gradient(135deg, #e74c3c, #c0392b) !important;
    }

    .bg-warning {
      background: linear-gradient(135deg, #f39c12, #e67e22) !important;
      color: white !important;
    }

    .bg-success {
      background: linear-gradient(135deg, #27ae60, #229954) !important;
    }

    /* Navbar Enhancement */
    .navbar {
      background: linear-gradient(135deg, #fff 0%, var(--secondary-color) 100%) !important;
      box-shadow: 0 4px 20px rgba(254, 159, 66, 0.1);
    }

    /* Toast Styles */
    .toast-welcome {
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      color: white;
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(254, 159, 66, 0.3);
    }

    .toast-welcome .toast-body {
      font-size: 1.2rem;
      font-weight: 700;
      padding: 1.5rem;
    }

    /* Chart Legend Styling */
    .chart-legend {
      display: flex;
      justify-content: center;
      margin-top: 15px;
      gap: 20px;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 600;
    }

    .legend-color {
      width: 15px;
      height: 15px;
      border-radius: 50%;
    }

    /* Animation for cards */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .dashboard-card, .stats-card {
      animation: fadeInUp 0.6s ease-out;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .export-buttons {
        flex-direction: column;
      }
      
      .chart-container {
        height: 250px;
      }
      
      .stats-card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light px-4 py-3">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="images/mitsde-logo.svg" alt="Logo" class="dashboard-logo me-2">
        <span class="fs-4 fw-bold" style="color: var(--primary-color);">ToDo AI - Admin</span>
      </a>
      <div class="d-flex align-items-center">
        <span class="me-3 text-dark fw-semibold">
          <i class="fas fa-user-shield me-2" style="color: var(--primary-color);"></i> 
          <?= htmlspecialchars($user['name']) ?> (Admin)
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
    <div class="row mb-5">
      <div class="col-md-4 mb-4">
        <div class="stats-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stats-number"><?= $summary['total'] ?></div>
              <div class="stats-label">Total Tasks</div>
            </div>
            <div class="stats-icon">
              <i class="fas fa-tasks"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="stats-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stats-number"><?= $summary['pending'] ?></div>
              <div class="stats-label">Pending Tasks</div>
            </div>
            <div class="stats-icon">
              <i class="fas fa-clock"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="stats-card">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="stats-number"><?= $summary['completed'] ?></div>
              <div class="stats-label">Completed Tasks</div>
            </div>
            <div class="stats-icon">
              <i class="fas fa-check-circle"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 📈 Enhanced Charts Section -->
    <div class="row mb-5">
      <div class="col-md-6 mb-4">
        <div class="dashboard-card">
          <h5><i class="fas fa-chart-pie me-2"></i>Task Status Distribution</h5>
          <div class="chart-container">
            <canvas id="statusPieChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-6 mb-4">
        <div class="dashboard-card">
          <h5><i class="fas fa-chart-bar me-2"></i>Priority Distribution</h5>
          <div class="chart-container">
            <canvas id="priorityBarChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- User Task Distribution Chart -->
    <div class="row mb-5">
      <div class="col-12">
        <div class="dashboard-card">
          <h5><i class="fas fa-chart-line me-2"></i>User Task Distribution</h5>
          <div class="chart-container">
            <canvas id="userTaskChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- All Tasks Table with Export -->
    <div class="dashboard-card">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-list-check me-2"></i>All User Tasks</h4>
        <div class="export-buttons">
          <button class="btn btn-export" onclick="exportToExcel()">
            <i class="fas fa-file-excel"></i>Export to Excel
          </button>
          <button class="btn btn-export" onclick="exportToCSV()">
            <i class="fas fa-file-csv"></i>Export to CSV
          </button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle" id="tasksTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>User</th>
              <th>Priority</th>
              <th>Start Date</th>
              <th>Deadline</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $tasks = [];
            mysqli_data_seek($taskResult, 0); // Reset result pointer
            while($task = mysqli_fetch_assoc($taskResult)) {
              $tasks[] = $task; // Store for JavaScript
              $priorityColor = $task['priority'] === 'High' ? 'danger' : ($task['priority'] === 'Medium' ? 'warning' : 'success');
              $status = $task['completed'] ? "<span class='badge bg-success'>Completed</span>" : "<span class='badge bg-danger'>Pending</span>";
            ?>
              <tr>
                <td><?= $task['id'] ?></td>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td><?= htmlspecialchars($task['user_name']) ?></td>
                <td><span class="badge bg-<?= $priorityColor ?>"><?= $task['priority'] ?></span></td>
                <td><?= date('M d, Y', strtotime($task['start_date'])) ?></td>
                <td><?= date('M d, Y', strtotime($task['deadline'])) ?></td>
                <td><?= $status ?></td>
                <td>
                  <div class="action-buttons">
                    <button class="btn btn-edit" onclick="editTask(<?= $task['id'] ?>)" title="Edit Task">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-delete" onclick="deleteTask(<?= $task['id'] ?>)" title="Delete Task">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Edit Task Modal -->
  <div class="modal fade" id="editTaskModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content" style="border-radius: 15px; border: 1px solid var(--primary-color);">
        <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-color), var(--accent-color)); color: white; border-radius: 15px 15px 0 0;">
          <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Task</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="editTaskForm">
            <input type="hidden" id="editTaskId" name="task_id">
            <div class="mb-3">
              <label for="editTitle" class="form-label fw-semibold">Title</label>
              <input type="text" class="form-control" id="editTitle" name="title" required style="border-radius: 10px;">
            </div>
            <div class="mb-3">
              <label for="editPriority" class="form-label fw-semibold">Priority</label>
              <select class="form-select" id="editPriority" name="priority" required style="border-radius: 10px;">
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="editDeadline" class="form-label fw-semibold">Deadline</label>
              <input type="date" class="form-control" id="editDeadline" name="deadline" required style="border-radius: 10px;">
            </div>
            <div class="mb-3">
              <label for="editStatus" class="form-label fw-semibold">Status</label>
              <select class="form-select" id="editStatus" name="completed" style="border-radius: 10px;">
                <option value="0">Pending</option>
                <option value="1">Completed</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 25px;">Cancel</button>
          <button type="button" class="btn btn-export" onclick="updateTask()">Update Task</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Welcome Toast -->
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="js/script.js"></script>
  <script src="voice/voice.js"></script>

  <script>
    // Store tasks data for export
    const tasksData = <?= json_encode($tasks) ?>;
    
    $(document).ready(function () {
      // Initialize Charts
      initializeCharts();

      // 🎙️ Voice button functionality
      $('#voiceBtn').on('click', function () {
        $('#toastMessage').text('Voice recognition activated...');
        const toastVoice = new bootstrap.Toast($('#toastVoice')[0]);
        toastVoice.show();
      });

      <?php if (isset($_GET['login']) && $_GET['login'] == 1): ?>
        // 🎉 Welcome Toast using Toastify
        Toastify({
          text: "🎉 Welcome <?= addslashes($user['name']) ?>! Admin Dashboard Ready",
          duration: 5000,
          gravity: "top",
          position: "center",
          backgroundColor: "linear-gradient(135deg, #FE9F42 0%, #FF8C00 100%)",
          stopOnFocus: true,
          className: "custom-toast",
          onClick: function(){} // Callback after click
        }).showToast();
      <?php endif; ?>
    });

    // 📈 Initialize Enhanced Charts
    function initializeCharts() {
      // Enhanced Task Status Pie Chart
      const statusCtx = document.getElementById('statusPieChart').getContext('2d');
      new Chart(statusCtx, {
        type: 'doughnut',
        data: {
          labels: ['Completed', 'Pending'],
          datasets: [{
            data: [<?= $summary['completed'] ?>, <?= $summary['pending'] ?>],
            backgroundColor: [
              'linear-gradient(135deg, #27ae60, #229954)',
              'linear-gradient(135deg, #FE9F42, #FF8C00)'
            ],
            borderWidth: 0,
            hoverOffset: 15
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '60%',
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                padding: 20,
                font: {
                  size: 14,
                  weight: 'bold'
                },
                usePointStyle: true,
                pointStyle: 'circle'
              }
            }
          },
          animation: {
            animateScale: true,
            duration: 2000
          }
        }
      });

      // Enhanced Priority Bar Chart
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
            backgroundColor: [
              'linear-gradient(135deg, #27ae60, #229954)',
              'linear-gradient(135deg, #FE9F42, #FF8C00)',
              'linear-gradient(135deg, #e74c3c, #c0392b)'
            ],
            borderRadius: 10,
            borderSkipped: false,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                font: {
                  weight: 'bold'
                }
              },
              grid: {
                color: 'rgba(254, 159, 66, 0.1)'
              }
            },
            x: {
              ticks: {
                font: {
                  weight: 'bold'
                }
              },
              grid: {
                display: false
              }
            }
          },
          animation: {
            duration: 2000,
            easing: 'easeOutBounce'
          }
        }
      });

      // Enhanced User Task Distribution Chart
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
            backgroundColor: 'linear-gradient(135deg, #FE9F42, #FF8C00)',
            borderRadius: 8,
            borderSkipped: false,
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                font: {
                  weight: 'bold'
                }
              },
              grid: {
                color: 'rgba(254, 159, 66, 0.1)'
              }
            },
            x: {
              ticks: {
                font: {
                  weight: 'bold'
                }
              },
              grid: {
                display: false
              }
            }
          },
          animation: {
            duration: 2000,
            easing: 'easeOutQuart'
          }
        }
      });
    }

    // 📊 Export to Excel Function
    function exportToExcel() {
      const exportData = tasksData.map(task => ({
        'ID': task.id,
        'Title': task.title,
        'User': task.user_name,
        'Priority': task.priority,
        'Start Date': task.start_date,
        'Deadline': task.deadline,
        'Status': task.completed ? 'Completed' : 'Pending',
        'Description': task.description || ''
      }));

      const wb = XLSX.utils.book_new();
      const ws = XLSX.utils.json_to_sheet(exportData);
      
      // Add styling
      const range = XLSX.utils.decode_range(ws['!ref']);
      for (let C = range.s.c; C <= range.e.c; ++C) {
        const address = XLSX.utils.encode_col(C) + "1";
        if (!ws[address]) continue;
        ws[address].s = {
          font: { bold: true },
          fill: { fgColor: { rgb: "FE9F42" } }
        };
      }

      XLSX.utils.book_append_sheet(wb, ws, "Tasks");
      XLSX.writeFile(wb, `tasks_export_${new Date().toISOString().split('T')[0]}.xlsx`);
      
      Toastify({
        text: "📊 Excel file exported successfully!",
        duration: 3000,
        gravity: "top",
        position: "center",
        backgroundColor: "linear-gradient(135deg, #FE9F42, #FF8C00)"
      }).showToast();
    }

    // 📋 Export to CSV Function
    function exportToCSV() {
      const exportData = tasksData.map(task => ({
        'ID': task.id,
        'Title': task.title,
        'User': task.user_name,
        'Priority': task.priority,
        'Start Date': task.start_date,
        'Deadline': task.deadline,
        'Status': task.completed ? 'Completed' : 'Pending',
        'Description': task.description || ''
      }));

      const csv = convertToCSV(exportData);
      downloadCSV(csv, `tasks_export_${new Date().toISOString().split('T')[0]}.csv`);
      
      Toastify({
        text: "📋 CSV file exported successfully!",
        duration: 3000,
        gravity: "top",
        position: "center",
        backgroundColor: "linear-gradient(135deg, #FE9F42, #FF8C00)"
      }).showToast();
    }

    // Helper function to convert JSON to CSV
    function convertToCSV(data) {
      if (!data || !data.length) return '';
      
      const headers = Object.keys(data[0]);
      const csvHeaders = headers.join(',');
      
      const csvRows = data.map(row => 
        headers.map(header => {
          const value = row[header] || '';
          return `"${value.toString().replace(/"/g, '""')}"`;
        }).join(',')
      );
      
      return [csvHeaders, ...csvRows].join('\n');
    }

    // Helper function to download CSV
    function downloadCSV(csv, filename) {
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      
      if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
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
          if(response === 'success') {
            $('#editTaskModal').modal('hide');
            Toastify({
              text: "✅ Task updated successfully!",
              duration: 3000,
              gravity: "top",
              position: "center",
              backgroundColor: "linear-gradient(135deg, #27ae60, #229954)"
            }).showToast();
            setTimeout(() => location.reload(), 1500);
          } else {
            Toastify({
              text: "❌ Error updating task!",
              duration: 3000,
              gravity: "top",
              position: "center",
              backgroundColor: "linear-gradient(135deg, #e74c3c, #c0392b)"
            }).showToast();
          }
        }
      });
    }

    // 🗑️ Delete Task Function
    function deleteTask(taskId) {
      if(confirm('Are you sure you want to delete this task?')) {
        $.ajax({
          url: 'php/admin_actions.php',
          type: 'POST',
          data: {
            action: 'delete_task',
            task_id: taskId
          },
          success: function(response) {
            if(response === 'success') {
              Toastify({
                text: "🗑️ Task deleted successfully!",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "linear-gradient(135deg, #27ae60, #229954)"
              }).showToast();
              setTimeout(() => location.reload(), 1500);
            } else {
              Toastify({
                text: "❌ Error deleting task!",
                duration: 3000,
                gravity: "top",
                position: "center",
                backgroundColor: "linear-gradient(135deg, #e74c3c, #c0392b)"
              }).showToast();
            }
          }
        });
      }
    }

    // 📱 Add responsive chart resize
    window.addEventListener('resize', function() {
      Chart.helpers.each(Chart.instances, function(instance) {
        instance.resize();
      });
    });

    // 🎯 Add loading animation for export buttons
    function showExportLoading(button) {
      const originalText = button.innerHTML;
      button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Exporting...';
      button.disabled = true;
      
      setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
      }, 2000);
    }

    // Enhanced export functions with loading
    const originalExportToExcel = exportToExcel;
    const originalExportToCSV = exportToCSV;

    exportToExcel = function() {
      const button = event.target.closest('.btn-export');
      showExportLoading(button);
      setTimeout(originalExportToExcel, 500);
    };

    exportToCSV = function() {
      const button = event.target.closest('.btn-export');
      showExportLoading(button);
      setTimeout(originalExportToCSV, 500);
    };
  </script>

  <!-- Additional PHP file needed for export functionality -->
  <script>
    // Create export.php file for server-side export (optional)
    function createExportPHP() {
      const exportPHPContent = `<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit();
}

if (isset($_GET['format'])) {
    $format = $_GET['format'];
    
    $taskSql = "SELECT tasks.*, users.name as user_name FROM tasks 
                LEFT JOIN users ON tasks.user_id = users.id 
                ORDER BY tasks.deadline ASC";
    $taskResult = mysqli_query($conn, $taskSql);
    
    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="tasks_export_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Title', 'User', 'Priority', 'Start Date', 'Deadline', 'Status', 'Description']);
        
        while ($row = mysqli_fetch_assoc($taskResult)) {
            fputcsv($output, [
                $row['id'],
                $row['title'],
                $row['user_name'],
                $row['priority'],
                $row['start_date'],
                $row['deadline'],
                $row['completed'] ? 'Completed' : 'Pending',
                $row['description'] ?? ''
            ]);
        }
        
        fclose($output);
    }
}
?>`;
      
      console.log('Export PHP file content ready for server-side implementation');
    }
  </script>

  <!-- Custom Toast Styles -->
  <style>
    .custom-toast {
      border-radius: 15px !important;
      font-weight: 700 !important;
      font-size: 1.2rem !important;
      padding: 20px 25px !important;
      box-shadow: 0 10px 30px rgba(254, 159, 66, 0.3) !important;
    }

    /* Loading animation for export buttons */
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .fa-spinner {
      animation: spin 1s linear infinite;
    }

    /* Hover effects for cards */
    .stats-card:hover .stats-icon {
      transform: scale(1.1);
      transition: transform 0.3s ease;
    }

    .dashboard-card:hover h4,
    .dashboard-card:hover h5 {
      color: var(--accent-color);
      transition: color 0.3s ease;
    }

    /* Enhanced table styling */
    .table tbody td {
      padding: 15px;
      vertical-align: middle;
      border-color: rgba(254, 159, 66, 0.1);
    }

    .table tbody tr:nth-child(even) {
      background-color: rgba(254, 159, 66, 0.02);
    }

    /* Badge enhancements */
    .badge {
      font-size: 0.9rem;
      padding: 8px 16px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Modal enhancements */
    .modal-content {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.95);
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(254, 159, 66, 0.25);
    }

    /* Print styles */
    @media print {
      .export-buttons,
      .action-buttons,
      .navbar {
        display: none !important;
      }
      
      .dashboard-card,
      .stats-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
      }
      
      .table {
        font-size: 12px;
      }
    }
  </style>

</body>
</html>