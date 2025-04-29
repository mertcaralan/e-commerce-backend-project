<?php
    if (isset($_POST["email"], $_POST["name"], $_POST["password"], $_POST["city"], $_POST["district"])) 
    {
        require_once 'db.php';
        $newid = 1 + $db->query("select * from users")->rowCount();
        $stmt = $db->prepare("insert into users (userid, type, name, email, password, city, district) values (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$newid, 'C', $_POST["email"], $_POST["name"], $_POST["password"], $_POST["city"], $_POST["district"]]);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {display: flex; justify-content: center; align-items: center;}
    </style>
</head>
<body>
    <div id="main">
        <h1>consumer sign up</h1>
        <form action="" method="POST">
            <table>
                <tr>
                    <td>email: </td>
                    <td><input type="text" name="email"></td>
                </tr>
                <tr>
                    <td>full name: </td>
                    <td><input type="text" name="name"></td>
                </tr>
                <tr>
                    <td>password: </td> 
                    <td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td>city: </td>
                    <td><input type="text" name="city"></td>
                </tr>
                <tr>
                    <td>district: </td>
                    <td><input type="text" name="district"></td>
                </tr>
            </table>
            <button>sign up</button>
        </form>
    </div>
</body>
</html>