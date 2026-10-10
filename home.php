<?php
session_start();

if (!isset($_SESSION["user_id"], $_SESSION["role"])) {
    header("Location: index.php");
    exit;
}

if ($_SESSION["role"] !== "user") {
    header("Location: home2.php");
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
    <title>User Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="card p-4 shadow-sm text-center" style="width: 360px;">
    <h2>Welcome, <?= $username ?>!</h2>
    <p class="text-secondary mt-2">You are logged in as a regular user.</p>
    <p class="badge bg-primary fs-6 align-self-center">Role: user</p>
    <a href="home.php?logout=1" class="btn btn-outline-danger mt-3">Logout</a>
</div>

</body>
</html>