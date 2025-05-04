<?php
require_once 'db.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize errors and email
$errors = [];
$success = '';
$email = isset($_GET['email']) ? filter_var($_GET['email'], FILTER_SANITIZE_EMAIL) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['csrf_token'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = 'CSRF token validation failed.';
    }

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }

    if (empty($errors)) {
        if ($_POST['action'] === 'verify' && isset($_POST['code'])) {
            $code = filter_var($_POST['code'], FILTER_SANITIZE_STRING);

            // Validate code format
            if (!preg_match('/^\d{6}$/', $code)) {
                $errors[] = 'Verification code must be 6 digits.';
            }

            if (empty($errors)) {
                // Check verification code
                $stmt = $db->prepare('SELECT * FROM verification_codes WHERE email = ? AND code = ? AND created_at >= NOW() - INTERVAL 1 HOUR');
                $stmt->execute([$email, $code]);
                $verification = $stmt->fetch();

                if ($verification) {
                    // Mark user as verified
                    $stmt = $db->prepare('UPDATE users SET is_verified = 1 WHERE email = ?');
                    $stmt->execute([$email]);

                    // Delete used verification code
                    $stmt = $db->prepare('DELETE FROM verification_codes WHERE email = ?');
                    $stmt->execute([$email]);

                    // Redirect to login
                    header('Location: login.php');
                    exit;
                } else {
                    $errors[] = 'Invalid or expired verification code.';
                }
            }
        } elseif ($_POST['action'] === 'resend') {
            // Generate new 6-digit code
            $code = sprintf("%06d", mt_rand(0, 999999));

            // Store new verification code
            $stmt = $db->prepare('INSERT INTO verification_codes (email, code, created_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE code = ?, created_at = NOW()');
            $stmt->execute([$email, $code, $code]);

            // Send verification email
            $mail = new PHPMailer(true);
            try {
                // SMTP settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'mertcaralan16@gmail.com';
                $mail->Password = 'agfw rfaz xzhb srph';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                // Email settings
                $mail->setFrom('mertcaralan16@gmail.com', 'Sustainability e-Commerce');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Email Verification';
                $mail->Body = "Your new verification code is: <b>$code</b>. Please enter this code to verify your email.";
                $mail->AltBody = "Your new verification code is: $code. Please enter this code to verify your email.";

                $mail->send();
                $success = 'A new verification code has been sent to your email.';
            } catch (Exception $e) {
                $errors[] = "Failed to send verification email: {$mail->ErrorInfo}";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body { display: flex; justify-content: center; align-items: center; font-family: "Nunito Sans"; }
        main { width: 350px; text-align: center; }
        table { margin: 0 auto; }
        td { padding: 12px 10px; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; background: #3ed643; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #52de75; }
        .error { color: red; }
        .success { color: green; }
        a { text-decoration: none; color: #0849a3; }
    </style>
</head>
<body>
    <main>
        <h1>Email Verification</h1>
        <p>Enter the 6-digit code sent to your email.</p>

        <?php if (!empty($errors)): ?>
            <ul class="error">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="action" value="verify">
            <table>
                <tr>
                    <td>Code:</td>
                    <td><input type="text" name="code" required></td>
                </tr>
            </table>
            <button type="submit">Verify</button>
        </form>

        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="action" value="resend">
            <button type="submit">Resend Code</button>
        </form>

        <p><a href="signUp.php">Back to Sign Up</a></p>
    </main>
</body>
</html>