<?php
     $error = [];
     require_once './vendor/autoload.php';
     require_once './Mail.php';
     require_once "./db.php";
     $verify_mail = $_SESSION['data']['email'];
     if ($_SERVER["REQUEST_METHOD"]=="GET") {
        $address_to=$verify_mail;
        $rand_code=rand(100000, 999999);
        $_SESSION["code"]=$rand_code;

        Mail::send($address_to,SUBJECT,"Verification is: {$rand_code}");
     }
     if($_SERVER["REQUEST_METHOD"]=="POST"){
        extract($_POST);
        if (strlen($verification_code) < 6) {
            $error['length'] = "Code should have six digits";
          } else if ($verification_code == $_SESSION["code"]) {
            $hash = password_hash($_SESSION['data']['password'], PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO users (email,password,name,city,district,type) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$_SESSION['data']['email'], $hash, $_SESSION['data']['name'], $_SESSION['data']['city'], $_SESSION['data']['district'], $_SESSION['type']]);
          
            header("Location: index.php") ;
            
          }
          else {
            $error["wrong"] = "Wrong code entered";
          }

     }
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>E-Posta Doğrulama</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" />
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-100 min-h-screen flex items-center justify-center">

  <div class="bg-white shadow-xl rounded-xl p-8 w-full max-w-md">
    <div class="text-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">E-Mail Verification</h1>
      <p class="text-gray-600 mt-2">Enter verification Code</p>
    </div>

    <form method="POST" class="space-y-4">
      <input
        type="text"
        name="verification_code"
        maxlength="6"
        pattern="[0-9]{6}"
        placeholder="Example: 123456"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
      />

      <button
        type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition"
      >
        Verify
      </button>
    </form>


  </div>

</body>
</html>