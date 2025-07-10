$(document).ready(function () {
  const user_id = $("input[name=user_id]").val();

  // Load all tasks
  function loadTasks() {
    $.get("php/tasks.php?action=get&user_id=" + user_id, function (data) {
      const tasks = JSON.parse(data);
      let html = "";

      tasks.forEach(task => {
        html += `
        <div class="col-md-4">
          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <h5>${task.title} 
                <span class="badge bg-${task.priority === 'High' ? 'danger' : task.priority === 'Medium' ? 'warning' : 'success'}">${task.priority}</span>
              </h5>
              <p>${task.description}</p>
              <p><strong>Start:</strong> ${task.start_date} | <strong>Deadline:</strong> ${task.deadline}</p>
              <p>Status: ${task.completed == 1 ? '✅ Completed' : '❌ Pending'}</p>
              ${task.completed != 1 ? `<button class="btn btn-sm btn-outline-success completeBtn" data-id="${task.id}"><span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>Mark Completed</button>` : ''}
              <button class="btn btn-sm btn-outline-primary editBtn" data-task='${JSON.stringify(task)}'>Edit</button>
              <button class="btn btn-sm btn-outline-danger deleteBtn" data-id="${task.id}"><span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>Delete</button>
            </div>
          </div>
        </div>`;
      });

      $("#taskList").html(html);
    });
  }

  // Update task stats
  function updateStats() {
    $.get("php/get_stats.php", function (data) {
      const stats = JSON.parse(data);
      $("#totalTasks").text(stats.total);
      $("#pendingTasks").text(stats.pending);
      $("#completedTasks").text(stats.completed);
    });
  }

  // Initial load
  loadTasks();
  updateStats();

  // Add / Update Task
  $("#taskForm").submit(function (e) {
    e.preventDefault();

    // Show loader
    $("#saveBtn").attr("disabled", true);
    $("#saveLoader").removeClass("d-none");
    $("#saveBtnText").text("Saving...");

    $.post("php/tasks.php?action=save", $(this).serialize(), function () {
      $("#taskModal").modal("hide");
      loadTasks();
      updateStats();
      $("#taskForm")[0].reset();

      // Reset Save button
      $("#saveBtn").attr("disabled", false);
      $("#saveLoader").addClass("d-none");
      $("#saveBtnText").text("Save Task");

      Toastify({
        text: "✅ Task saved successfully!",
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: "green"
      }).showToast();
    });
  });

  // Edit Task
  $(document).on("click", ".editBtn", function () {
    const task = $(this).data("task");
    $("#task_id").val(task.id);
    $("input[name=title]").val(task.title);
    $("textarea[name=description]").val(task.description);
    $("input[name=start_date]").val(task.start_date);
    $("input[name=deadline]").val(task.deadline);
    $("select[name=priority]").val(task.priority);
    $("#taskModal").modal("show");
  });

  // Delete Task
  $(document).on("click", ".deleteBtn", function () {
    const $btn = $(this);
    const id = $btn.data("id");

    // Show loader
    const loader = $btn.find(".spinner-border");
    loader.removeClass("d-none");
    $btn.attr("disabled", true);

    $.post("php/tasks.php?action=delete", { id }, function () {
      loadTasks();
      updateStats();

      Toastify({
        text: "🗑️ Task deleted!",
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: "red"
      }).showToast();
    });
  });

  // Mark Task as Completed
  $(document).on("click", ".completeBtn", function () {
    const $btn = $(this);
    const id = $btn.data("id");

    // Show loader
    const loader = $btn.find(".spinner-border");
    loader.removeClass("d-none");
    $btn.attr("disabled", true);

    $.post("php/mark_completed.php", { task_id: id }, function () {
      loadTasks();
      updateStats();

      Toastify({
        text: "✅ Task marked as completed!",
        duration: 3000,
        gravity: "top",
        position: "right",
        backgroundColor: "green"
      }).showToast();
    });
  });
});
