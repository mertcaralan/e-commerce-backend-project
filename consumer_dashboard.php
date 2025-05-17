<?php
    session_start();
    require_once 'db.php';
    

    // Restrict access to logged-in consumers
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'C') {
        header('Location: login.php');
        exit;
    }

    $uid = $_SESSION["user_id"];

    $stmt = $db->prepare("select * from users where userid = ?");
    $stmt->execute([$uid]);
    $user = $stmt->fetch();

    if(!empty($_POST) && isset($_POST["email"]))
    {
        $stmt = $db->prepare("update users set name = ?, email = ?, district = ?, city = ? where userid = ?");
        $stmt->execute([$_POST["name"], $_POST["email"], $_POST["district"], $_POST["city"], $uid]);
    }

    if(!empty($_POST) && (isset($_POST["search"]) || isset($_GET["search"])))
    {
        $stmt = $db->prepare("select products.title, products.id, products.stock, products.normalPrice, products.discounted, products.expDate, products.img, users.district, CASE WHEN users.district = ? THEN 1 ELSE 0 END as pri from products join users on products.market_id = users.userid where users.city = ? and products.expDate > CURDATE() and products.title like ? order by pri desc");
        $stmt->execute([$user["district"], $user["city"], '%' . $_POST["search"] . '%']);
        $products = $stmt->fetchAll();
    }
    else
    {
        $stmt = $db->prepare("select products.title, products.id, products.stock, products.normalPrice, products.discounted, products.expDate, products.img, users.district, CASE WHEN users.district = ? THEN 1 ELSE 0 END as pri from products join users on products.market_id = users.userid where users.city = ? and products.expDate > CURDATE() order by pri desc");
        $stmt->execute([$user["district"], $user["city"]]);
        $products = $stmt->fetchAll();
    }
if (!empty($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $quantity = 1;

    // Session sepetine ekle
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += 1;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    // Veritabanı sepetine ekle/güncelle
    $stmt = $db->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$uid, $product_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $new_quantity = $row['quantity'] + 1;
        $update = $db->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $update->execute([$new_quantity, $uid, $product_id]);
    } else {
        $insert = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->execute([$uid, $product_id, $quantity]);
    }

    // Sepete ekledikten sonra geri yönlendir
   header("Location: consumer_dashboard.php?page=$curr&added=$product_id");

    exit;
}


 
    $stmt = $db->prepare("select * from users where userid = ?");
    $stmt->execute([$uid]);
    $user = $stmt->fetch();

    $count = ceil(count($products) / 4);

    $curr = $_GET["page"] ?? 1;

    if(!($curr > 0 && $curr <=$count))
        $curr = 1;

    $offset = ($curr - 1) * 4;
    $products = array_slice($products, $offset, 4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Consumer Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cal+Sans&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap');
        body{margin: 0; padding: 0; display: flex; flex-direction: column;}
        header{display: flex; background-color:#4292c6; width: 100%; flex-shrink: 0; align-items: center; font-family: "Cal Sans";}
        header nav{flex: 1; display:flex; justify-content: flex-end; font-family: "Nunito Sans"}
        header h2{flex: 2; padding-left: 30px}
        header nav *{position: relative; color: black; margin-right: 30px; text-decoration: none; cursor: pointer; background-color: white; border: 1px solid lightgray; border-radius: 10px; padding: 5px;}
        header nav *:active{top: 2px;}
        main{display:flex; margin: 0 auto; width: 1200px;}
        #info{height: 125px; margin: 20px; flex: 1; border: 1px solid  #4292c6; border-radius: 10px; padding: 10px; font-family: "Nunito Sans"}
        #market{margin: 20px; flex: 3; font-family: "Nunito Sans";}
        #newP, #editProfile, #editProduct {background-color: white; font-family: "Nunito Sans"; position: relative; width: 300px; height: 550px; border: 1px solid black; display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 20px; z-index: 1000; border-radius: 8px;}
        #editProfile{height: 300px; width: 400px;}
        #close, #closeP, #closeE {position: absolute; top: 10px; right: 10px; background-color: #4292c6; border: 0px; border-radius: 10px; padding: 5px 8px; color: white; cursor: pointer;}
        #close:active, #closeP:active{top:12px;}
        #market{overflow: hidden; position:relative; border: 1px solid  #4292c6; border-radius: 10px}
        #market h2{font-family: "Cal Sans"; background-color: #4292c6; margin: 0; padding: 15px;}
        img{max-height: 60px;}
        table{width: 100%; text-align: center; border-collapse: collapse;}
        th, tr:not(:last-of-type) td{padding: 5px; border-bottom: 1px solid #4292c6}
        .material-symbols-outlined{color: black; cursor: pointer;}
        #editProfile input{width: 175px;}
        input { outline: none; background: #FFFFFF; color: #000000; border: 1px solid rgb(76, 108, 172); border-radius: 5px; box-shadow: 3px 3px 2px 0px #5A7EC7; transition: .3s ease; font-family: "Nunito Sans";}
        input:focus { background: #F2F2F2; border: 1px solid #5A7EC7; border-radius: 10px; }
        #market form{background-color: #4292c6; padding: 20px;}
        #market form input{width: 240px; height: 25px;}
    </style>
    <script src="./jquery-3.7.1.js"></script>
</head>
<body>
    <header>
        <h2>Consumer Dashboard</h2>
        <nav>
            <a href="cart.php"></span>View Cart</a>
            <span id="edit">Edit Profile</span>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div id="info">
            <h3>Welcome, <?= $user["name"]?>!</h3>
            <p><?= $user["email"]?></p>
            <p><?= $user["district"]?> | <?=$user["city"]?></p>
        </div>

        <div id="market">
            <form action="?" method="post">
                <input type="text" name="search" placeholder="search for products...">
            </form>

            <?php if(empty($products)) : ?>
                <p>There are currently no products available.</p>
            <?php else :?>
                <table>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Stock</th>
                        <th>Normal Price</th>
                        <th>Discounted Price</th>
                        <th>Expiration Date</th>
                        <th>Operations</th>
                    </tr>
                    <?php foreach($products as $p) : ?>
                        <tr class="<?= ($p["expDate"] < date("Y-m-d")) ? "expired" : "" ?>">
                            <td><img src="./img/<?= $p["img"]?>"></td>
                            <td><?= $p["title"]?></td>
                            <td><?= $p["stock"]?></td>
                            <td><?= $p["normalPrice"]?></td>
                            <td><?= $p["discounted"]?></td>
                            <td><?= $p["expDate"]?></td>
                            <td>
                                <a href="?id=<?= $p["id"] ?>&page=<?= $curr ?>"><span class="material-symbols-outlined">add_shopping_cart</span></a>

                            </td>
                        </tr>
                    <?php endforeach?>
                    <tr>
                        <td colspan="7">
                            <?php if($curr > 1) :?>
                                <a href="?page=<?=$curr-1?>"><span class="material-symbols-outlined">arrow_back</span></a>
                            <?php endif ?>
                            <?php for($i = 1; $i <= $count; $i++) :?>
                                <span class="<?=$i == $curr ? "current" : ""?>"><?=$i?></span>
                            <?php endfor ?>
                            <?php if($curr < $count) :?>
                                <a href="?page=<?=$curr+1?>"><span class="material-symbols-outlined">arrow_forward</span></a>
                            <?php endif ?>
                        </td>
                    </tr>
                </table>
            <?php endif ?>
        </div>

        <div id="editProfile">
            <span id="closeP">close</span>
            <h3>Edit Profile</h3>
            <form action="?" method="post">
                <p>Name:</p>
                <input type="text" value="<?=$user["name"]?>" name="name">
                <p>Email:</p>
                <input type="text" value="<?=$user["email"]?>" name="email">
                <p>District | City</p>
                <input type="text" value="<?=$user["district"]?>" name="district">
                <input type="text" value="<?=$user["city"]?>" name="city">
                <br>
                <button>Save Changes</button>
            </form>
        </div>
    </main>
    <script>
        $(function(){
            $("#edit").click(function(){
                $("#editProfile").show();
            });

            $("#closeP").click(function(){
                $("#editProfile").hide();
            });
        });
    </script>
    </script>
</body>
</html>