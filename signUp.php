<?php
    if (isset($_POST["email"], $_POST["name"], $_POST["password"], $_POST["city"], $_POST["district"])) 
    {
        require_once 'db.php';
        $newid = 1 + $db->query("select * from users")->rowCount();
        $stmt = $db->prepare("insert into users (userid, type, name, email, password, city, district) values (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$newid, $_GET["type"], $_POST["email"], $_POST["name"], $_POST["password"], $_POST["city"], $_POST["district"]]);
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
        .interface { display: none; }
        .active { font-weight: bold; color: blue; cursor: pointer; }
        .inactive { color: gray; cursor: pointer; }
    </style>
    <script src="./jquery-3.7.1.js"></script>
</head>
<body>
    <main>
        <header>
            <h1>sign up</h1>
                <span id="consumerBtn" class="inactive">as a consumer</span>
                <span id="marketBtn" class="inactive">as a market</span>
        </header>

        <div id="consumer" class="interface">
            <h1>consumer sign up</h1>
            <form action="?type=C" method="POST">
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

        <div id="market" class="interface">
            <h1>market user sign up</h1>
            <form action="?type=C" method="POST">
                <table>
                    <tr>
                        <td>email: </td>
                        <td><input type="text" name="email"></td>
                    </tr>
                    <tr>
                        <td>name: </td>
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