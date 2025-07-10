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
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
      --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
      --dark-bg: #0f0f23;
      --card-bg: rgba(255, 255, 255, 0.95);
      --glass-bg: rgba(255, 255, 255, 0.1);
      --text-primary: #2d3748;
      --text-secondary: #718096;
      --border-radius: 20px;
      --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.1);
      --shadow-xl: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--dark-bg);
      background-image: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%);
      min-height: 100vh;
      color: var(--text-primary);
      overflow-x: hidden;
    }

    /* Animated background particles */
    .bg-animation {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: -1;
    }

    .particle {
      position: absolute;
      width: 4px;
      height: 4px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(180deg); }
    }

    /* Navigation */
    .navbar {
      background: var(--glass-bg) !important;
      backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      padding: 1rem 2rem;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar-brand {
      font-weight: 600;
      font-size: 1.2rem;
      color: white !important;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .dashboard-logo {
      max-width: 50px;
      height: auto;
      filter: brightness(0) invert(1);
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 1rem;
      color: rgba(255, 255, 255, 0.9);
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--primary-gradient);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      color: white;
    }

    .btn-logout {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 10px;
      transition: all 0.3s ease;
    }

    .btn-logout:hover {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      transform: translateY(-2px);
    }

    /* Main Content */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem;
    }

    .dashboard-card {
      background: var(--card-bg);
      backdrop-filter: blur(20px);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-lg);
      padding: 2rem;
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .dashboard-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--primary-gradient);
    }

    .dashboard-card:hover {
      box-shadow: var(--shadow-xl);
      transform: translateY(-5px);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .card-title {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text-primary);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .card-title i {
      background: var(--primary-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* Buttons */
    .btn-brand {
      background: var(--primary-gradient);
      border: none;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 12px;
      font-weight: 500;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-brand:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
      color: white;
    }

    .btn-success {
      background: var(--success-gradient) !important;
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(79, 172, 254, 0.4);
    }

    /* Task List */
    .task-item {
      background: white;
      border-radius: 15px;
      padding: 1.5rem;
      margin-bottom: 1rem;
      border: 1px solid rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .task-item::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 4px;
      background: var(--primary-gradient);
    }

    .task-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .task-priority {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .priority-high {
      background: linear-gradient(135deg, #ff6b6b, #feca57);
      color: white;
    }

    .priority-medium {
      background: linear-gradient(135deg, #54a0ff, #2e86de);
      color: white;
    }

    .priority-low {
      background: linear-gradient(135deg, #5f27cd, #341f97);
      color: white;
    }

    /* Modal */
    .modal-content {
      background: var(--card-bg);
      backdrop-filter: blur(20px);
      border-radius: var(--border-radius);
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: var(--shadow-xl);
    }

    .modal-header {
      background: var(--primary-gradient);
      border-radius: var(--border-radius) var(--border-radius) 0 0;
      border-bottom: none;
      padding: 1.5rem;
    }

    .modal-title {
      color: white;
      font-weight: 600;
    }

    .btn-close {
      filter: brightness(0) invert(1);
      opacity: 0.8;
    }

    .modal-body {
      padding: 2rem;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid rgba(0, 0, 0, 0.1);
      padding: 0.75rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-label {
      font-weight: 500;
      color: var(--text-primary);
      margin-bottom: 0.5rem;
    }

    /* Voice Button */
    .voice-btn {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: var(--warning-gradient);
      border: none;
      color: white;
      font-size: 1.2rem;
      box-shadow: 0 8px 25px rgba(67, 233, 123, 0.3);
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .voice-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 10px 30px rgba(67, 233, 123, 0.4);
    }

    /* Toast */
    .toast {
      background: var(--success-gradient) !important;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(79, 172, 254, 0.3);
    }

    /* Loading Animation */
    .loading {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .navbar {
        padding: 1rem;
      }
      
      .container {
        padding: 1rem;
      }
      
      .dashboard-card {
        padding: 1.5rem;
      }
      
      .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }
      
      .voice-btn {
        width: 50px;
        height: 50px;
        bottom: 1rem;
        right: 1rem;
      }
    }
  </style>
</head>
<body>
  <!-- Background Animation -->
  <div class="bg-animation" id="bgAnimation"></div>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <div class="navbar-brand">
        <img src="images/mitsde-logo.svg" alt="Logo" class="dashboard-logo">
        <span>ToDo AI</span>
      </div>
      <div class="user-info">
        <div class="user-avatar">J</div>
        <div>
          <div style="font-weight: 600;">John Doe</div>
          <div style="font-size: 0.9rem; opacity: 0.8;">Administrator</div>
        </div>
        <button class="btn btn-logout">
          <i class="fas fa-sign-out-alt me-1"></i>
          Logout
        </button>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="container">
    <div class="dashboard-card">
      <div class="card-header">
        <h4 class="card-title">
          <i class="fas fa-tasks"></i>
          Your Tasks
        </h4>
        <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#taskModal">
          <i class="fas fa-plus me-2"></i>
          Add Task
        </button>
      </div>
      
      <div id="taskList" class="row">
        <!-- Sample tasks for demo -->
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="task-item">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <h6 class="mb-0">Complete Project Report</h6>
              <span class="task-priority priority-high">High</span>
            </div>
            <p class="text-muted mb-3">Finish the quarterly analysis and compile the final report for stakeholders.</p>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">
                <i class="fas fa-calendar-alt me-1"></i>
                Due: Dec 15, 2024
              </small>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="task-item">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <h6 class="mb-0">Team Meeting Preparation</h6>
              <span class="task-priority priority-medium">Medium</span>
            </div>
            <p class="text-muted mb-3">Prepare agenda and materials for the upcoming team meeting.</p>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">
                <i class="fas fa-calendar-alt me-1"></i>
                Due: Dec 10, 2024
              </small>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="task-item">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <h6 class="mb-0">Code Review</h6>
              <span class="task-priority priority-low">Low</span>
            </div>
            <p class="text-muted mb-3">Review pull requests and provide feedback to team members.</p>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">
                <i class="fas fa-calendar-alt me-1"></i>
                Due: Dec 20, 2024
              </small>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Task Modal -->
  <div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="taskForm">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-plus-circle me-2"></i>
              Add New Task
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="task_id" id="task_id">
            <input type="hidden" name="user_id" value="1">
            
            <div class="row">
              <div class="col-md-8 mb-3">
                <label class="form-label">Task Title</label>
                <input name="title" class="form-control" placeholder="Enter task title..." required>
              </div>
              <div class="col-md-4 mb-3">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-control">
                  <option value="Low">Low</option>
                  <option value="Medium" selected>Medium</option>
                  <option value="High">High</option>
                </select>
              </div>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Task description..."></textarea>
            </div>
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Deadline</label>
                <input type="date" name="deadline" class="form-control" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-success" type="submit">
              <i class="fas fa-save me-2"></i>
              Save Task
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Voice Button -->
  <button class="voice-btn" id="voiceBtn" title="Ask Task Status">
    <i class="fas fa-microphone"></i>
  </button>

  <!-- Toast for AI voice response -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
    <div id="toastVoice" class="toast text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastMessage"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>

  <!-- AI Agent Task Summary Modal -->
  <div class="modal fade" id="taskModalVoice" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fas fa-robot me-2"></i>
            AI Assistant
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <h5 id="modalTaskTitle" class="text-dark mb-3"></h5>
          <p id="modalTaskDesc" class="text-muted mb-3"></p>
          <div class="d-flex align-items-center">
            <i class="fas fa-clock text-warning me-2"></i>
            <strong>Deadline:</strong>
            <span id="modalTaskDeadline" class="ms-2"></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
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
  
<script>
    // Background animation
    
    
    // Welcome message
    setTimeout(() => {
      Toastify({
        text: "🎉 Welcome John! You are logged in.",
        duration: 4000,
        gravity: "top",
        position: "center",
        style: {
          background: "linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)",
          borderRadius: "15px",
          boxShadow: "0 8px 25px rgba(79, 172, 254, 0.3)"
        },
        stopOnFocus: true,
      }).showToast();
    }, 500);
    
    // Task form demo
    document.getElementById('taskForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
      modal.hide();
      
      Toastify({
        text: "✅ Task saved successfully!",
        duration: 3000,
        gravity: "top",
        position: "right",
        style: {
          background: "linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)",
          borderRadius: "15px"
        }
      }).showToast();
    });
  </script>
</body>
</html>