<?php
session_start();
include 'db.php';

// Check if user is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo 'unauthorized';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get_task':
            getTask();
            break;
        case 'update_task':
            updateTask();
            break;
        case 'delete_task':
            deleteTask();
            break;
        default:
            http_response_code(400);
            echo 'invalid_action';
    }
}

function getTask() {
    global $conn;
    
    $task_id = intval($_POST['task_id']);
    
    $sql = "SELECT * FROM tasks WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $task_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $task = mysqli_fetch_assoc($result);
    
    if ($task) {
        echo json_encode($task);
    } else {
        http_response_code(404);
        echo 'task_not_found';
    }
    
    mysqli_stmt_close($stmt);
}

function updateTask() {
    global $conn;
    
    $task_id = intval($_POST['task_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    $completed = intval($_POST['completed']);
    
    // Validate inputs
    if (empty($title) || empty($priority) || empty($deadline)) {
        http_response_code(400);
        echo 'missing_fields';
        return;
    }
    
    if (!in_array($priority, ['Low', 'Medium', 'High'])) {
        http_response_code(400);
        echo 'invalid_priority';
        return;
    }
    
    if (!in_array($completed, [0, 1])) {
        http_response_code(400);
        echo 'invalid_status';
        return;
    }
    
    $sql = "UPDATE tasks SET title = ?, priority = ?, deadline = ?, completed = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssii", $title, $priority, $deadline, $completed, $task_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo 'success';
    } else {
        http_response_code(500);
        echo 'database_error';
    }
    
    mysqli_stmt_close($stmt);
}

function deleteTask() {
    global $conn;
    
    $task_id = intval($_POST['task_id']);
    
    $sql = "DELETE FROM tasks WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $task_id);
    
    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo 'success';
        } else {
            http_response_code(404);
            echo 'task_not_found';
        }
    } else {
        http_response_code(500);
        echo 'database_error';
    }
    
    mysqli_stmt_close($stmt);
}
?>