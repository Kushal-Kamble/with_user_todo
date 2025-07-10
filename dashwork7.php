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
    :root {
      --primary-color: #4f46e5;
      --secondary-color: #6366f1;
      --success-color: #10b981;
      --warning-color: #f59e0b;
      --danger-color: #ef4444;
      --info-color: #3b82f6;
      --light-bg: #f8fafc;
      --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    body {
      background-color: var(--light-bg);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border: none;
      box-shadow: var(--card-shadow);
    }

    .navbar-brand {
      color: white !important;
      font-weight: 600;
      font-size: 1.3rem;
    }

    .dashboard-card {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
      padding: 30px;
      transition: 0.3s ease;
    }

    .dashboard-card:hover {
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
    }

    .dashboard-logo {
      max-width: 160px;
      height: auto;
    }

    .stats-card {
      background: white;
      border-radius: 16px;
      padding: 1.5rem;
      box-shadow: var(--card-shadow);
      border: none;
      transition: transform 0.2s, box-shadow 0.2s;
      margin-bottom: 1.5rem;
    }

    .stats-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .stats-number {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .stats-label {
      font-size: 0.9rem;
      color: #6b7280;
      font-weight: 500;
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

    .icon-total { background: var(--info-color); }
    .icon-pending { background: var(--warning-color); }
    .icon-completed { background: var(--success-color); }

    .section-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 1.5rem;
    }

    .task-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: var(--card-shadow);
      border: none;
      transition: transform 0.2s, box-shadow 0.2s;
      margin-bottom: 1rem;
      border-left: 4px solid;
    }

    .task-card:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .task-card.priority-high {
      border-left-color: var(--danger-color);
    }

    .task-card.priority-medium {
      border-left-color: var(--warning-color);
    }

    .task-card.priority-low {
      border-left-color: var(--success-color);
    }

    .task-card.completed {
      border-left-color: var(--success-color);
      background: #f0fdf4;
    }

    .task-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 0.5rem;
    }

    .task-description {
      color: #6b7280;
      font-size: 0.9rem;
      margin-bottom: 1rem;
    }

    .task-meta {
      display: flex;
      align-items: center;
      gap: 1rem;
      font-size: 0.85rem;
      flex-wrap: wrap;
    }

    .task-deadline {
      color: #6b7280;
    }

    .task-priority {
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 500;
    }

    .priority-high {
      background: #fef2f2;
      color: var(--danger-color);
    }

    .priority-medium {
      background: #fffbeb;
      color: var(--warning-color);
    }

    .priority-low {
      background: #f0fdf4;
      color: var(--success-color);
    }

    .task-actions {
      display: flex;
      gap: 0.5rem;
    }

    .btn-action {
      padding: 0.5rem;
      border: none;
      border-radius: 8px;
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-edit {
      background: #f3f4f6;
      color: #4b5563;
    }

    .btn-edit:hover {
      background: #e5e7eb;
    }

    .btn-delete {
      background: #fef2f2;
      color: var(--danger-color);
    }

    .btn-delete:hover {
      background: #fee2e2;
    }

    .btn-complete {
      background: #f0fdf4;
      color: var(--success-color);
    }

    .btn-complete:hover {
      background: #dcfce7;
    }

    .btn-brand {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border: none;
      border-radius: 12px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
      transition: all 0.3s;
    }

    .btn-brand:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
      color: white;
    }

    .voice-btn {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      background: linear-gradient(135deg, var(--warning-color), #fbbf24);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 1rem 1.5rem;
      font-size: 1rem;
      font-weight: 500;
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
      transition: all 0.3s;
    }

    .voice-btn:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
      color: white;
    }

    .overdue-badge {
      background: var(--danger-color);
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 12px;
      font-size: 0.7rem;
      font-weight: 500;
    }

    .task-checkbox {
      width: 20px;
      height: 20px;
      border: 2px solid #d1d5db;
      border-radius: 4px;
      margin-right: 0.75rem;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .task-checkbox.completed {
      background: var(--success-color);
      border-color: var(--success-color);
    }

    .modal-content {
      border-radius: 16px;
      border: none;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border-radius: 16px 16px 0 0;
    }

    .form-control {
      border-radius: 8px;
      border: 1px solid #d1d5db;
      padding: 0.75rem;
    }

    .form-control:focus {
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
      border-color: var(--primary-color);
    }

    .ai-logo {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 1.2rem;
      margin-right: 1rem;
    }

    .btn-outline-danger {
      border-radius: 8px;
      transition: all 0.3s;
    }

    .btn-outline-danger:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .empty-state {
      text-align: center;
      padding: 3rem;
      color: #6b7280;
    }

    .empty-state i {
      font-size: 4rem;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-light px-4">
  <div class="d-flex align-items-center">
    <!-- आप अपना logo यहाँ add कर सकते हैं -->
    <div class="ai-logo">AI</div>
    <div>
      <div class="navbar-brand mb-0">👋 Hello, <?= htmlspecialchars($user['name']) ?></div>
      <small style="color: rgba(255,255,255,0.8); font-size: 0.85rem;"><?= ucfirst($user['role']) ?> Dashboard</small>
    </div>
  </div>
  <a href="php/auth.php?logout=1" class="btn btn-outline-danger">
    <i class="fas fa-sign-out-alt me-1"></i>Logout
  </a>
</nav>

<div class="container py-4">
  
  <!-- Statistics Cards -->
  <div class="row mb-4" id="statsCards">
    <div class="col-md-4 mb-3">
      <div class="stats-card">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="stats-number text-primary" id="totalTasks">0</div>
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
            <div class="stats-number text-warning" id="pendingTasks">0</div>
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
            <div class="stats-number text-success" id="completedTasks">0</div>
            <div class="stats-label">Completed</div>
          </div>
          <div class="stats-icon icon-completed">
            <i class="fas fa-check-circle"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="dashboard-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="section-title mb-0">
        <i class="fas fa-tasks me-2"></i>Your Tasks
      </h4>
      <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#taskModal">
        <i class="fas fa-plus me-2"></i>Add Task
      </button>
    </div>
    
    <div id="taskList" class="row">
      <!-- Tasks will be loaded here via JavaScript -->
      <div class="col-12">
        <div class="empty-state">
          <i class="fas fa-spinner fa-spin"></i>
          <p>Loading tasks...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="taskForm">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-plus-circle me-2"></i>
            Add New Task
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="task_id" id="task_id">
          <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

          <div class="mb-3">
            <label class="form-label">Task Title</label>
            <input name="title" class="form-control" placeholder="Enter task title" required>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Enter task description"></textarea>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input type="date" name="deadline" class="form-control" required>
              </div>
            </div>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-control">
              <option value="Low">Low</option>
              <option value="Medium">Medium</option>
              <option value="High">High</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-brand" type="submit">
            <i class="fas fa-save me-2"></i>
            Save Task
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- 🎙️ Voice Button -->
<div class="position-fixed bottom-0 end-0 p-4">
  <button class="btn voice-btn" id="voiceBtn">
    <i class="fas fa-microphone me-2"></i>
    Ask Task Status
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

<!-- 🤖 Modal for AI Agent Task Summary -->
<div class="modal fade" id="taskModalVoice" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-success">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fas fa-robot me-2"></i>
          🤖 आपका AI एजेंट
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h5 id="modalTaskTitle" class="text-dark"></h5>
        <p id="modalTaskDesc" class="text-muted mb-3"></p>
        <p><strong>⏰ डेडलाइन:</strong> <span id="modalTaskDeadline"></span></p>
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

<script>
// Enhanced JavaScript for better UI interaction
document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('[name="start_date"]').value = today;
    
    // Load tasks and update stats
    if (typeof loadTasks === 'function') {
        loadTasks();
    }
    
    // Voice button functionality
    document.getElementById('voiceBtn').addEventListener('click', function() {
        const toast = new bootstrap.Toast(document.getElementById('toastVoice'));
        document.getElementById('toastMessage').textContent = 'Voice recognition activated...';
        toast.show();
    });
    
    // Enhanced task form handling
    document.getElementById('taskForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
        submitBtn.disabled = true;
        
        // Your existing form submission logic goes here
        // After successful submission:
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Show success message
            Toastify({
                text: "✅ Task saved successfully!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#10b981",
                stopOnFocus: true,
            }).showToast();
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
            modal.hide();
            
            // Reload tasks
            if (typeof loadTasks === 'function') {
                loadTasks();
            }
        }, 1000);
    });
    
    // Add task card click handlers
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('task-checkbox')) {
            const checkbox = e.target;
            const taskCard = checkbox.closest('.task-card');
            
            if (checkbox.classList.contains('completed')) {
                checkbox.classList.remove('completed');
                checkbox.innerHTML = '';
                taskCard.classList.remove('completed');
            } else {
                checkbox.classList.add('completed');
                checkbox.innerHTML = '<i class="fas fa-check text-white"></i>';
                taskCard.classList.add('completed');
            }
        }
        
        if (e.target.closest('.btn-edit')) {
            document.getElementById('taskModal').querySelector('.modal-title').innerHTML = 
                '<i class="fas fa-edit me-2"></i>Edit Task';
            const modal = new bootstrap.Modal(document.getElementById('taskModal'));
            modal.show();
        }
        
        if (e.target.closest('.btn-delete')) {
            if (confirm('Are you sure you want to delete this task?')) {
                const taskCard = e.target.closest('.task-card').closest('.col-12');
                taskCard.style.transform = 'translateX(100%)';
                taskCard.style.opacity = '0';
                
                setTimeout(() => {
                    taskCard.remove();
                    // Update stats
                    updateStats();
                }, 300);
            }
        }
    });
});

// Function to update statistics
function updateStats() {
    const taskCards = document.querySelectorAll('.task-card');
    const completedTasks = document.querySelectorAll('.task-card.completed');
    
    document.getElementById('totalTasks').textContent = taskCards.length;
    document.getElementById('completedTasks').textContent = completedTasks.length;
    document.getElementById('pendingTasks').textContent = taskCards.length - completedTasks.length;
}

// Function to create task card HTML
function createTaskCard(task) {
    const isOverdue = new Date(task.deadline) < new Date() && !task.completed;
    const priorityClass = `priority-${task.priority.toLowerCase()}`;
    
    return `
        <div class="col-12 mb-3">
            <div class="task-card ${priorityClass} ${task.completed ? 'completed' : ''}">
                <div class="d-flex align-items-start">
                    <div class="task-checkbox ${task.completed ? 'completed' : ''} me-3">
                        ${task.completed ? '<i class="fas fa-check text-white"></i>' : ''}
                    </div>
                    <div class="flex-grow-1">
                        <div class="task-title">${task.title}</div>
                        <div class="task-description">${task.description || 'No description'}</div>
                        <div class="task-meta">
                            <div class="task-deadline">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Due: ${new Date(task.deadline).toLocaleDateString()}
                            </div>
                            ${isOverdue ? '<span class="overdue-badge">Overdue</span>' : ''}
                            <div class="task-priority ${priorityClass}">${task.priority}</div>
                        </div>
                    </div>
                    <div class="task-actions">
                        <button class="btn-action btn-edit" title="Edit" data-task-id="${task.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete" title="Delete" data-task-id="${task.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}
</script>

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