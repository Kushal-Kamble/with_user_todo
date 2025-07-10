<?php
session_start();
include("db.php");

// ✅ Register
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // 🔍 Check if email already exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        header("Location: ../register.php?error=exists");
        exit();
    }

    // ✅ Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        header("Location: ../login.php?success=1");
        exit();
    } else {
        echo "Registration failed: " . $stmt->error;
    }
}

// ✅ Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            // 🟢 Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ../admin_dashboard.php?login=1");
            } else {
                header("Location: ../dashboard.php?login=1&user=" . urlencode($user['name']));
            }
            exit();
        } else {
            header("Location: ../login.php?error=1");
            exit();
        }
    } else {
        header("Location: ../login.php?error=1");
        exit();
    }
}

// 🔓 Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}
?>
