<?php
  require "./db.php";
  if(isset($_GET["destroy"]))
    session_destroy();
  
  $error = [];
  if(!empty($_POST)){
    extract($_POST);
    if($email == ""){
      $error["email"]="Please fill the email field";
    }
    if($password == ""){
      $error["password"] ="Please fill the password field";
    }
    
 }
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap"
    rel="stylesheet"
  />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Jost', sans-serif;
    }

    body {
      background-color: #f1f8e9;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    form {
      background-color: #ffffff;
      padding: 40px 50px;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
      min-width: 320px;
    }

    table {
      margin: 0 auto 20px auto;
    }

    td {
      padding: 10px;
      font-size: 16px;
      color: #2e7d32;
    }

    input[type="text"],
    input[type="password"] {
      padding: 10px;
      width: 200px;
      border: 1px solid #c8e6c9;
      border-radius: 8px;
      outline: none;
      transition: border-color 0.3s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #66bb6a;
    }

    button {
      padding: 10px 25px;
      background-color: #66bb6a;
      color: white;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      margin: 10px 10px 0 10px;
      transition: background-color 0.3s ease;
      font-size: 15px;
    }

    button:hover {
      background-color: #388e3c;
    }

    .register-btn {
      display: inline-block;
      margin-top: 10px;
      padding: 10px 20px;
      background-color: #aed581;
      color: #1b5e20;
      text-decoration: none;
      border-radius: 20px;
      transition: background-color 0.3s ease;
    }

    .register-btn:hover {
      background-color: #9ccc65;
    }
  </style>
</head>
<body>
  <form action="" method="post">
    <h2 style="color:#2e7d32; margin-bottom: 20px;">Login</h2>
    <table>
      <tr>
        <td>Email</td>
        <td><input type="text" name="email" required /></td>
      </tr>
      <tr>
        <td>Password</td>
        <td><input type="password" name="password" required /></td>
      </tr>
    </table>
    <button type="submit">Login</button>
    <br>
    <a class="register-btn" href="index.php">Register</a>
  </form>
  <ul>
    <?php
    if (!empty($error)) {
      echo "<p class='err'>Errors</p>";
      foreach ($error as $e) {
        if($e!=""){
          $sanitized_message = filter_var($e, FILTER_SANITIZE_SPECIAL_CHARS);
          echo "<li class='error_msg'>$sanitized_message</li>";
        }
      }
      
    }
    ?>
  </ul>
</body>
</html>
