<?php
require_once 'db.php';

// Restrict access to logged-in consumers
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'C') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Consumer Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
    <p>This is the consumer dashboard.</p>
    <a href="logout.php">Logout</a>
</body>
</html>