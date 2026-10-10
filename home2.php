<?php
session_start();

if (!isset($_SESSION["user_id"], $_SESSION["role"])) {
    header("Location: index.php");
    exit;
}

if ($_SESSION["role"] !== "admin") {
    header("Location: home.php");
    exit;
}

if (isset($_GET["logout"])) {
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit;
}

$username = htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Administrator Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="card p-4 shadow-sm text-center" style="width: 380px;">
    <h2>Welcome, Admin <?= $username ?>!</h2>
    <p class="text-secondary mt-2">You are logged in as an administrator.</p>
    <p class="badge bg-danger fs-6 align-self-center">Role: admin</p>

    <div class="mt-3 p-3 bg-white border rounded">
        <h5 class="mb-1">Admin Dashboard</h5>
        <p class="text-muted small mb-0">You have access to administrator-only content.</p>
    </div>

    <a href="home2.php?logout=1" class="btn btn-outline-danger mt-3">Logout</a>
</div>

</body>
</html>