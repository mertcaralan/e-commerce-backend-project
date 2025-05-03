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
        @import url('https://fonts.googleapis.com/css2?family=Cal+Sans&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap');

        body {display: flex; justify-content: center; align-items: center;}
        main{width: 350px}
        table{margin: 0 auto}
        td{padding: 12px 10px; font-family: "Nunito Sans";}
        td:first-of-type{width: 80px;}
        .interface { display: none; margin-top: 30px;}
        .active { cursor: pointer; }
        .inactive { cursor: pointer; background-color: white;}
        h1{font-family: "Cal Sans"; font-weight: 300}
        header, h2{text-align:center;}
        #consumerBtn{border: 1px solid #0849a3; border-radius: 15px; padding: 5px 10px; display: inline-block; width: 100px; margin-right: 10px; font-family: "Nunito Sans"; font-weight: 200; transition: .3s ease}
        #consumerBtn.active{border: 0px; background:linear-gradient(to bottom, #0b53b8 5%, #537fbd 100%); color: white; box-shadow: 0px 0px 12px 2px #0849a3}
        #marketBtn{border: 1px solid #9c0505; border-radius: 15px; padding: 5px; display: inline-block; width: 100px; margin-left: 10px; font-family: "Nunito Sans"; font-weight: 200; transition: .3s ease}
        #marketBtn.active{border: 0px; background:linear-gradient(to bottom, #872424 5%, #d93030 100%); color: white; box-shadow: 0px 0px 12px 2px #9c0505}
        button{border: 0px; box-shadow: 0px 0px 12px 2px #42ff42; background:linear-gradient(to bottom, #52de75 5%, #3ed643 100%); border-radius:18px; display:block; cursor:pointer; color:#ffffff; font-family:"Nunito Sans"; font-size:15px; padding:6px 20px; text-shadow:0px 0px 0px #3c7a32; margin: 35px auto; transition: .3s ease}
        button:hover{background:linear-gradient(to bottom, #3ed643 5%, #52de75 100%);background-color:#3ed643;}
        button:active, span:active{position:relative;top:1px;}
        footer{display: flex; align-items: center; justify-content: center}
        a{transition: .3s ease; text-align: center; font-family: "Nunito Sans"; text-decoration: none; color: black; margin-top: 15px; border-radius: 15px; border: 1px solid black; padding: 5px;}
        a:hover{background-color: lightgray;}
        #consumer input{outline: none; background: #FFFFFF; color: #000000; border: 1px solid rgb(174, 196, 240); border-radius: 5px; box-shadow: 3px 3px 2px 0px rgb(160, 159, 240); transition: .3s ease; font-family: "Nunito Sans"; width: 220px;}
        #market input{outline: none; background: #FFFFFF; color: #000000; border: 1px solid rgb(243, 134, 134); border-radius: 5px; box-shadow: 3px 3px 2px 0px rgb(240, 159, 159); transition: .3s ease; font-family: "Nunito Sans"; width: 220px;}
        #consumer input:focus{background: #F2F2F2; border: 1px solid #5A7EC7; border-radius: 10px;}
        #market input:focus{background: #F2F2F2; border: 1px solid rgb(199, 90, 90); border-radius: 10px;}
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
            <form action="?type=C" method="POST">
                <table>
                    <tr>
                        <td>Email: </td>
                        <td><input type="text" name="email"></td>
                    </tr>
                    <tr>
                        <td>Full Name: </td>
                        <td><input type="text" name="name"></td>
                    </tr>
                    <tr>
                        <td>Password: </td> 
                        <td><input type="password" name="password"></td>
                    </tr>
                    <tr>
                        <td>City: </td>
                        <td><input type="text" name="city"></td>
                    </tr>
                    <tr>
                        <td>District: </td>
                        <td><input type="text" name="district"></td>
                    </tr>
                </table>
                <button>sign up</button>
            </form>
        </div>

        <div id="market" class="interface">
            <form action="?type=M" method="POST">
                <table>
                    <tr>
                        <td>Email: </td>
                        <td><input type="text" name="email"></td>
                    </tr>
                    <tr>
                        <td>Name: </td>
                        <td><input type="text" name="name"></td>
                    </tr>
                    <tr>
                        <td>Password: </td> 
                        <td><input type="password" name="password"></td>
                    </tr>
                    <tr>
                        <td>City: </td>
                        <td><input type="text" name="city"></td>
                    </tr>
                    <tr>
                        <td>District: </td>
                        <td><input type="text" name="district"></td>
                    </tr>
                </table>
                <button>sign up</button>
            </form>
        </div>

        <footer>
            <a href="index.php">back to main</a>
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