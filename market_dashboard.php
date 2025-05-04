<?php
require_once 'db.php';

// Restrict access to logged-in markets
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'M') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Market Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>This is the market dashboard.</p>
    <a href="logout.php">Logout</a>
</body>
</html>