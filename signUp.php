<?php
require_once 'db.php';
require_once 'vendor/autoload.php'; // Composer autoload for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize errors and sticky form variables
$errors = [];
$old = ['email' => '', 'name' => '', 'city' => '', 'district' => ''];

// Debugging: Display session data
if (session_status() === PHP_SESSION_ACTIVE) {
    $debug[] = 'Session is active';
    $debug[] = 'Session test: ' . ($_SESSION['test'] ?? 'Not set');
    $debug[] = 'CSRF token in session: ' . ($_SESSION['csrf_token'] ?? 'Not set');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['name'], $_POST['password'], $_POST['city'], $_POST['district'])) {
    // Debugging: Display POST data
    $debug[] = 'POST data: ' . print_r($_POST, true);

    // Validate CSRF token
    $csrf_token_post = $_POST['csrf_token'] ?? '';
    $csrf_token_session = $_SESSION['csrf_token'] ?? '';
    if ($csrf_token_post !== $csrf_token_session) {
        $errors[] = 'CSRF token validation failed. Post: ' . $csrf_token_post . ', Session: ' . $csrf_token_session;
    }

    $type = $_GET['type'] ?? '';
    if (!in_array($type, ['C', 'M'])) {
        $errors[] = 'Invalid user type.';
    }

    // Sanitize and validate inputs
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? htmlspecialchars($_POST['email']) : '';
    $name = htmlspecialchars($_POST['name']);
    $password = $_POST['password'];
    $city = htmlspecialchars($_POST['city']);
    $district = htmlspecialchars($_POST['district']);

    // Store for sticky form
    $old = ['email' => $email, 'name' => $name, 'city' => $city, 'district' => $district];

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if (empty($name) || empty($city) || empty($district)) {
        $errors[] = 'All fields are required.';
    }

    // Check if email is already registered
    $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = 'Email already registered.';
    }

    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user (userid is auto-incremented)
        $stmt = $db->prepare('INSERT INTO users (type, name, email, password, city, district, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)');
        $stmt->execute([$type, $name, $email, $hashed_password, $city, $district]);

        // Generate 6-digit verification code
        $code = sprintf("%06d", mt_rand(0, 999999));

        // Store verification code
        $stmt = $db->prepare('INSERT INTO verification_codes (email, code, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$email, $code]);

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
            $mail->Body = "Your verification code is: <b>$code</b>. Please enter this code to verify your email.";
            $mail->AltBody = "Your verification code is: $code. Please enter this code to verify your email.";

            $mail->send();
            // Redirect to verification page
            header('Location: verify.php?email=' . urlencode($email));
            exit;
        } catch (Exception $e) {
            $errors[] = "Failed to send verification email: SMTP Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cal+Sans&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap');
        body { display: flex; justify-content: center; align-items: center; }
        main { width: 350px; }
        table { margin: 0 auto; }
        td { padding: 12px 10px; font-family: "Nunito Sans"; }
        td:first-of-type { width: 80px; }
        .interface { display: none; margin-top: 30px; }
        .active { cursor: pointer; }
        .inactive { cursor: pointer; background-color: white; }
        h1 { font-family: "Cal Sans"; font-weight: 300; }
        header, h2 { text-align: center; }
        #consumerBtn { border: 1px solid #0849a3; border-radius: 15px; padding: 5px 10px; display: inline-block; width: 100px; margin-right: 10px; font-family: "Nunito Sans"; font-weight: 200; transition: .3s ease; }
        #consumerBtn.active { border: 0px; background: linear-gradient(to bottom, #0b53b8 5%, #537fbd 100%); color: white; box-shadow: 0px 0px 12px 2px #0849a3; }
        #marketBtn { border: 1px solid #9c0505; border-radius: 15px; padding: 5px; display: inline-block; width: 100px; margin-left: 10px; font-family: "Nunito Sans"; font-weight: 200; transition: .3s ease; }
        #marketBtn.active { border: 0px; background: linear-gradient(to bottom, #872424 5%, #d93030 100%); color: white; box-shadow: 0px 0px 12px 2px #9c0505; }
        button { border: 0px; box-shadow: 0px 0px 12px 2px #42ff42; background: linear-gradient(to bottom, #52de75 5%, #3ed643 100%); border-radius: 18px; display: block; cursor: pointer; color: #ffffff; font-family: "Nunito Sans"; font-size: 15px; padding: 6px 20px; text-shadow: 0px 0px 0px #3c7a32; margin: 35px auto; transition: .3s ease; }
        button:hover { background: linear-gradient(to bottom, #3ed643 5%, #52de75 100%); background-color: #3ed643; }
        button:active, span:active { position: relative; top: 1px; }
        footer { display: flex; align-items: center; justify-content: center; }
        a { transition: .3s ease; text-align: center; font-family: "Nunito Sans"; text-decoration: none; color: black; margin-top: 15px; border-radius: 15px; border: 1px solid black; padding: 5px; }
        a:hover { background-color: lightgray; }
        #consumer input { outline: none; background: #FFFFFF; color: #000000; border: 1px solid rgb(174, 196, 240); border-radius: 5px; box-shadow: 3px 3px 2px 0px rgb(160, 159, 240); transition: .3s ease; font-family: "Nunito Sans"; width: 220px; }
        #market input { outline: none; background: #FFFFFF; color: #000000; border: 1px solid rgb(243, 134, 134); border-radius: 5px; box-shadow: 3px 3px 2px 0px rgb(240, 159, 159); transition: .3s ease; font-family: "Nunito Sans"; width: 220px; }
        #consumer input:focus { background: #F2F2F2; border: 1px solid #5A7EC7; border-radius: 10px; }
        #market input:focus { background: #F2F2F2; border: 1px solid rgb(199, 90, 90); border-radius: 10px; }
        .error { color: red; }
        .debug { color: blue; }
    </style>
    <script src="./jquery-3.7.1.js"></script>
</head>
<body>
    <main>
        <header>
            <h1>Sign Up</h1>
            <span id="consumerBtn" class="inactive">As a Consumer</span>
            <span id="marketBtn" class="inactive">As a Market</span>
        </header>

        <?php if (!empty($errors)): ?>
            <ul class="error">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div id="consumer" class="interface">
            <form action="?type=C" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <table>
                    <tr>
                        <td>Email: </td>
                        <td><input type="text" name="email" value="<?php echo htmlspecialchars($old['email']); ?>"></td>
                    </tr>
                    <tr>
                        <td>Full Name: </td>
                        <td><input type="text" name="name" value="<?php echo htmlspecialchars($old['name']); ?>"></td>
                    </tr>
                    <tr>
                        <td>Password: </td>
                        <td><input type="password" name="password"></td>
                    </tr>
                    <tr>
                        <td>City: </td>
                        <td><input type="text" name="city" value="<?php echo htmlspecialchars($old['city']); ?>"></td>
                    </tr>
                    <tr>
                        <td>District: </td>
                        <td><input type="text" name="district" value="<?php echo htmlspecialchars($old['district']); ?>"></td>
                    </tr>
                </table>
                <button>Sign Up</button>
            </form>
        </div>

        <div id="market" class="interface">
            <form action="?type=M" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <table>
                    <tr>
                        <td>Email: </td>
                        <td><input type="text" name="email" value="<?php echo htmlspecialchars($old['email']); ?>"></td>
                    </tr>
                    <tr>
                        <td>Market Name: </td>
                        <td><input type="text" name="name" value="<?php echo htmlspecialchars($old['name']); ?>"></td>
                    </tr>
                    <tr>
                        <td>Password: </td>
                        <td><input type="password" name="password"></td>
                    </tr>
                    <tr>
                        <td>City: </td>
                        <td><input type="text" name="city" value="<?php echo htmlspecialchars($old['city']); ?>"></td>
                    </tr>
                    <tr>
                        <td>District: </td>
                        <td><input type="text" name="district" value="<?php echo htmlspecialchars($old['district']); ?>"></td>
                    </tr>
                </table>
                <button>Sign Up</button>
            </form>
        </div>

        <footer>
            <a href="index.php">Back to Home</a>
        </footer>
    </main>
    <script>
        $(function(){
            $('#consumerBtn').click(function(){
                $('#consumer').show();
                $('#market').hide();
                $('#consumerBtn').addClass('active').removeClass('inactive');
                $('#marketBtn').addClass('inactive').removeClass('active');
            });

            $('#marketBtn').click(function(){
                $('#market').show();
                $('#consumer').hide();
                $('#marketBtn').addClass('active').removeClass('inactive');
                $('#consumerBtn').addClass('inactive').removeClass('active');
            });
        });
    </script>
</body>
</html>