<?php
session_start();
require_once "db.php";

if (isset($_SESSION["user_id"], $_SESSION["role"])) {
    header("Location: " . ($_SESSION["role"] === "admin" ? "home2.php" : "home.php"));
    exit;
}

$error = "";
$success = "";
$page = $_GET["page"] ?? "login";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "register") {
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        $confirm = $_POST["confirm_password"] ?? "";
        $page = "register";

        if ($username === "" || $password === "" || $confirm === "") {
            $error = "All fields are required.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
            $error = "Username must be 3-50 characters using letters, numbers, or underscores.";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            $check = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $check->bind_param("s", $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "Username already exists.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
                $stmt->bind_param("ss", $username, $hashedPassword);

                if ($stmt->execute()) {
                    $success = "Registration successful. You can now log in.";
                    $page = "login";
                } else {
                    $error = "Registration failed. Please try again.";
                }
                $stmt->close();
            }
            $check->close();
        }

    } elseif ($action === "login") {
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";
        $page = "login";

        if ($username === "" || $password === "") {
            $error = "Please fill in all fields.";
        } else {
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();

            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user["password"])) {
                session_regenerate_id(true);
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];

                header("Location: " . ($user["role"] === "admin" ? "home2.php" : "home.php"));
                exit;
            } else {
                $error = "Invalid username or password.";
            }
            $stmt->close();
        }
    } else {
        $error = "Invalid request.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $page === "register" ? "Registration" : "Login" ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="card p-4 shadow-sm" style="width: 360px;">

    <?php if ($error !== ""): ?>
        <p class="text-danger small mb-3"><?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?></p>
    <?php endif; ?>

    <?php if ($success !== ""): ?>
        <p class="text-success small mb-3"><?= htmlspecialchars($success, ENT_QUOTES, "UTF-8") ?></p>
    <?php endif; ?>

    <?php if ($page === "register"): ?>

        <h3 class="mb-3">Registration</h3>

        <form method="POST" action="index.php?page=register">
            <input type="hidden" name="action" value="register">

            <div class="mb-2">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" minlength="3" maxlength="50" required>
            </div>

            <div class="mb-2">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" minlength="8" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password:</label>
                <input type="password" name="confirm_password" class="form-control" minlength="8" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <p class="small text-muted mt-3 mb-0">Already registered? <a href="index.php">Login here</a></p>

    <?php else: ?>

        <h3 class="mb-3">Login</h3>

        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="login">

            <div class="mb-2">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" maxlength="50" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="small text-muted mt-3 mb-0">Don't have an account? <a href="index.php?page=register">Register here</a></p>

    <?php endif; ?>

</div>

</body>
</html>