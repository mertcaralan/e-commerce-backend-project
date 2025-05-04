<?php
require_once 'db.php';

// Initialize errors and sticky form variables
$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if (empty($errors)) {
        // Check if user exists and is verified
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ? AND is_verified = 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Start session and store user info
            $_SESSION['user_id'] = $user['userid'];
            $_SESSION['user_type'] = $user['type'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect based on user type
            if ($user['type'] === 'C') {
                header('Location: consumer_dashboard.php');
            } else {
                header('Location: market_dashboard.php');
            }
            exit;
        } else {
            $errors[] = 'Invalid email/password or email not verified.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body { display: flex; justify-content: center; align-items: center; font-family: "Nunito Sans"; }
        main { width: 350px; text-align: center; }
        table { margin: 0 auto; }
        td { padding: 12px 10px; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; background: #3ed643; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #52de75; }
        .error { color: red; }
        a { text-decoration: none; color: #0849a3; }
    </style>
</head>
<body>
    <main>
        <h1>Login</h1>

        <?php if (!empty($errors)): ?>
            <ul class="error">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="" method="POST">
            <table>
                <tr>
                    <td>Email:</td>
                    <td><input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" required></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="password" required></td>
                </tr>
            </table>
            <button type="submit">Login</button>
        </form>
        <p><a href="signUp.php">Don't have an account? Sign up</a></p>
    </main>
</body>
</html>