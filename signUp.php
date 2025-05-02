<?php

    $error = [];
    $type=$_GET["type"] ?? null;
    $heading=$type=="Market"?"Market":"Consumer";
    $nm=$type=="Market"?"Market Name":"Full Name";
    require_once "./db.php";
    if (!empty($_POST)) {
        extract($_POST);
        $error = [];
        if($name==""){
          $error["name"]="Please fill the name field";
        }
        if($email == ""){
          $error["email"]="Please fill the email field";
        }
        if($password == ""){
          $error["password"] ="Please fill the password field";
        }
        if($city == ""){
          $error["city"] = "Please fill the city field";
        }
        if($district == ""){
          $error["district"] = "Please fill the district field" ;
        }
        if ($password!="" && strlen($password) < 6) {
          $error["pass"] = "Please enter more than 6 characters for your password";
        }
        if(empty($error)){
          $_SESSION['data']=$_POST;
          $_SESSION['type']=$type;
          var_dump($_SESSION);
          header("Location:verification.php");
         
        }
      }
      
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>

    <link rel="stylesheet" href="signUp.css">


<script src="./jquery-3.7.1.js"></script>
</head>
<body>
    <main>
        <div class="consumer" class="interface">
            <form method="post">
                <table>
                    <tr>
                        <td>Email: </td>
                        <td><input type="text" name="email"  value="<?= isset($email) ? filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "" ?>"></td>
                    </tr>
                    <tr>
                        <td><?php echo $nm ?> </td>
                        <td><input type="text" name="name" value="<?= isset($name) ? filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "" ?>"></td>
                    </tr>
                    <tr>
                        <td>Password: </td> 
                        <td><input type="password" name="password" value="<?= isset($password) ? filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "" ?>"></td>
                    </tr>
                    <tr>
                        <td>City: </td>
                        <td><input type="text" name="city"  value="<?= isset($city) ? filter_var($city, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "" ?>"></td>
                    </tr>
                    <tr>
                        <td>District: </td>
                        <td><input type="text" name="district"  value="<?= isset($district) ? filter_var($district, FILTER_SANITIZE_FULL_SPECIAL_CHARS) : "" ?>"></td>
                    </tr>
                   
                </table>
                <div class="button">
                    <button id="Register" type="submit">Register</button>
                </div>
            
            </form>
        </div>
        <ul>
        <?php
            if (!empty($error)) {
                echo "<div class='err'>Please fix the following issues:</div>"; 
                foreach ($error as $e) {
                    if($e!="") {
                        $sanitized_message = filter_var($e, FILTER_SANITIZE_SPECIAL_CHARS);
                        echo "<li class='error_msg'><span class='error_icon'></span><p>$sanitized_message</p></li>";
                    }
                }
            }
        ?>
        </ul>
        <footer>
            <a href="index.php">back to main</a>
        </footer>
    </main>
  
</body>
</html>
