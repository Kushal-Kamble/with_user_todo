<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo AI - Smart Task Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
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
        }

        .add-task-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            transition: all 0.3s;
        }

        .add-task-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
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
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <div class="ai-logo me-3">AI</div>
            <div>
                <span class="navbar-brand mb-0">ToDo AI</span>
                <br>
                <small style="color: rgba(255,255,255,0.8); font-size: 0.85rem;">Smart task management</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white">
                <i class="fas fa-user-circle me-2"></i>
                John Doe (Developer)
            </span>
            <button class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i>
                Logout
            </button>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container py-4">
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stats-number text-primary">3</div>
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
                        <div class="stats-number text-warning">2</div>
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
                        <div class="stats-number text-success">1</div>
                        <div class="stats-label">Completed</div>
                    </div>
                    <div class="stats-icon icon-completed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">
            <i class="fas fa-list-check me-2"></i>
            Your Tasks
        </h2>
        <button class="btn add-task-btn" data-bs-toggle="modal" data-bs-target="#taskModal">
            <i class="fas fa-plus me-2"></i>
            Add Task
        </button>
    </div>

    <!-- Task List -->
    <div class="row" id="taskList">
        <!-- Task 1 - High Priority, Overdue -->
        <div class="col-12 mb-3">
            <div class="task-card priority-high">
                <div class="d-flex align-items-start">
                    <div class="task-checkbox me-3"></div>
                    <div class="flex-grow-1">
                        <div class="task-title">Complete project documentation</div>
                        <div class="task-description">
                            Write comprehensive documentation for the new feature
                        </div>
                        <div class="task-meta">
                            <div class="task-deadline">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Due: 1/15/2025
                            </div>
                            <span class="overdue-badge">Overdue</span>
                            <div class="task-priority priority-high">High</div>
                        </div>
                    </div>
                    <div class="task-actions">
                        <button class="btn-action btn-edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task 2 - Medium Priority, Completed -->
        <div class="col-12 mb-3">
            <div class="task-card priority-medium completed">
                <div class="d-flex align-items-start">
                    <div class="task-checkbox completed me-3">
                        <i class="fas fa-check text-white"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="task-title">Review code changes</div>
                        <div class="task-description">
                            Review pull requests from team members
                        </div>
                        <div class="task-meta">
                            <div class="task-deadline">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Due: 1/10/2025
                            </div>
                            <div class="task-priority priority-medium">Medium</div>
                        </div>
                    </div>
                    <div class="task-actions">
                        <button class="btn-action btn-edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task 3 - Low Priority, Overdue -->
        <div class="col-12 mb-3">
            <div class="task-card priority-low">
                <div class="d-flex align-items-start">
                    <div class="task-checkbox me-3"></div>
                    <div class="flex-grow-1">
                        <div class="task-title">Update dependencies</div>
                        <div class="task-description">
                            Update all npm packages to latest versions
                        </div>
                        <div class="task-meta">
                            <div class="task-deadline">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Due: 1/12/2025
                            </div>
                            <span class="overdue-badge">Overdue</span>
                            <div class="task-priority priority-low">Low</div>
                        </div>
                    </div>
                    <div class="task-actions">
                        <button class="btn-action btn-edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
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
                    <input type="hidden" name="user_id" value="1">

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
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-save me-2"></i>
                        Save Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Voice Assistant Modal -->
<div class="modal fade" id="taskModalVoice" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
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

<!-- Voice Button -->
<button class="voice-btn" id="voiceBtn">
    <i class="fas fa-microphone me-2"></i>
    Ask Task Status
</button>

<!-- Toast Notifications -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
    <div id="toastVoice" class="toast text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mock JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Voice button functionality
    document.getElementById('voiceBtn').addEventListener('click', function() {
        const toast = new bootstrap.Toast(document.getElementById('toastVoice'));
        document.getElementById('toastMessage').textContent = 'Voice recognition activated...';
        toast.show();
    });

    // Task form submission
    document.getElementById('taskForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Task saved successfully!');
        const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
        modal.hide();
    });

    // Task completion toggle
    document.querySelectorAll('.task-checkbox').forEach(checkbox => {
        checkbox.addEventListener('click', function() {
            const taskCard = this.closest('.task-card');
            if (this.classList.contains('completed')) {
                this.classList.remove('completed');
                this.innerHTML = '';
                taskCard.classList.remove('completed');
            } else {
                this.classList.add('completed');
                this.innerHTML = '<i class="fas fa-check text-white"></i>';
                taskCard.classList.add('completed');
            }
        });
    });

    // Edit and delete buttons
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('taskModal').querySelector('.modal-title').innerHTML = 
                '<i class="fas fa-edit me-2"></i>Edit Task';
            const modal = new bootstrap.Modal(document.getElementById('taskModal'));
            modal.show();
        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this task?')) {
                this.closest('.col-12').remove();
            }
        });
    });

    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('[name="start_date"]').value = today;
});
</script>

</body>
</html>