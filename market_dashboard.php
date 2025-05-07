<?php
    require_once 'db.php';

    // Restrict access to logged-in markets
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'M') {
        header('Location: login.php');
        exit;
    }

    $uid = $_SESSION["user_id"];

    if(!empty($_POST) && isset($_POST["title"]))
    {
        $res = upload("img");

        if(isset($res["filename"]))
        {
            $stmt = $db->prepare("insert into products(market_id, title, stock, normalPrice, discounted, expDate, img) values(?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$uid, $_POST["title"], $_POST["stock"], $_POST["normalP"], $_POST["discounted"], $_POST["expDate"], $res["filename"]]);
        }
    }

    if(!empty($_POST) && isset($_POST["email"]))
    {
        $stmt = $db->prepare("update users set name = ?, email = ?, district = ?, city = ? where userid = ?");
        $stmt->execute([$_POST["name"], $_POST["email"], $_POST["district"], $_POST["city"], $uid]);
    }

    if(!empty($_POST) && isset($_POST["editid"]))
    {
        $res = upload("img");

        if(!empty($res["filename"]))
        {
            $stmt = $db->prepare("update products set title = ?, stock = ?, normalPrice = ?, discounted = ?, expDate = ?, img = ? where id = ?");
            $stmt->execute([$_POST["title"], $_POST["stock"], $_POST["normalP"], $_POST["discounted"], $_POST["expDate"], $res["filename"], $_POST["editid"]]);
        }
        else{
            $stmt = $db->prepare("update products set title = ?, stock = ?, normalPrice = ?, discounted = ?, expDate = ? where id = ?");
            $stmt->execute([$_POST["title"], $_POST["stock"], $_POST["normalP"], $_POST["discounted"], $_POST["expDate"], $_POST["editid"]]);
        }
    }

    if(isset($_GET["delete"]))
    {
        $stmt = $db->prepare("delete from products where id = ?");
        $stmt->execute([$_GET["delete"]]);
    }

    $stmt = $db->prepare("select * from users where userid = ?");
    $stmt->execute([$uid]);
    $user = $stmt->fetch();

    $stmt = $db->prepare("select * from products where market_id = ?");
    $stmt->execute([$uid]);
    $products = $stmt->fetchAll();


    function upload($img)
    {
        $error = null;

        if(isset($_FILES[$img]))
        {
            $file = $_FILES[$img];

            $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

            if($file["error"] == UPLOAD_ERR_NO_FILE)
                $error = "No file chosen";
            else if (!in_array($ext, ["jpg", "png", "gif"]))
                $error = "{$file["name"]} : Not an image file.";
            else
            {
                $filename = bin2hex(random_bytes(8)).".$ext";

                if(move_uploaded_file($file["tmp_name"], "./img/" . $filename))
                    return ["filename" => $filename];
                else
                    $error = "{$file["tmp_name"]} cannot be moved. (check permissions)";
            }
        }
        return ["error" => $error];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cal+Sans&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap');
        body{margin: 0; padding: 0; display: flex; flex-direction: column;}
        header{display: flex; background-color:rgb(238, 106, 97); width: 100%; flex-shrink: 0; align-items: center; font-family: "Cal Sans";}
        header nav{flex: 1; display:flex; justify-content: flex-end; font-family: "Nunito Sans"}
        header h2{flex: 2; padding-left: 30px}
        header nav *{position: relative; color: black; margin-right: 30px; text-decoration: none; cursor: pointer; background-color: white; border: 1px solid lightgray; border-radius: 10px; padding: 5px;}
        header nav *:active{top: 2px;}
        main{display:flex; margin: 0 auto; width: 1200px;}
        #info{height: 125px; margin: 20px; flex: 1; border: 1px solid  rgb(238, 106, 97); border-radius: 10px; padding: 10px; font-family: "Nunito Sans"}
        #list{margin: 20px; flex: 3; font-family: "Nunito Sans";}
        #newP, #editProfile, #editProduct {background-color: white; font-family: "Nunito Sans"; position: relative; width: 300px; height: 550px; border: 1px solid black; display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 20px; z-index: 1000; border-radius: 8px;}
        #editProfile{height: 300px; width: 400px;}
        #close, #closeP, #closeE {position: absolute; top: 10px; right: 10px; background-color: #eb4034; border: 0px; border-radius: 10px; padding: 5px 8px; color: white; cursor: pointer;}
        #close:active, #closeP:active{top:12px;}
        #list{overflow: hidden; position:relative; border: 1px solid  rgb(238, 106, 97); border-radius: 10px}
        #new:active{top: 17px;}
        #new{position: absolute; top: 15px; right: 25px; cursor: pointer; background-color: white; border: 1px solid lightgray; border-radius: 10px; padding: 5px;}
        #list h2{font-family: "Cal Sans"; background-color: rgb(238, 106, 97); margin: 0; padding: 15px;}
        .expired{background-color: lightgray;}
        .expired td:nth-of-type(6){color: red;}
        img{max-height: 60px;}
        table{width: 100%; text-align: center; border-collapse: collapse;}
        th, tr:not(:last-of-type) td{padding: 5px; border-bottom: 1px solid rgb(238, 106, 97);}
        .material-symbols-outlined{color: black; cursor: pointer;}
        #editProfile input{width: 175px;}
        input { outline: none; background: #FFFFFF; color: #000000; border: 1px solid rgb(243, 134, 134); border-radius: 5px; box-shadow: 3px 3px 2px 0px rgb(240, 159, 159); transition: .3s ease; font-family: "Nunito Sans";}
        input:focus { background: #F2F2F2; border: 1px solid rgb(243, 134, 134); border-radius: 10px; }
    </style>
    <script src="./jquery-3.7.1.js"></script>
</head>
<body>
    <header>
        <h2>Market Dashboard</h2>
        <nav>
            <span id="edit">Edit Profile</span>
            <a id="logout" href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <div id="info">
            <h3>Welcome, <?= $user["name"]?>!</h3>
            <p><?= $user["email"]?></p>
            <p><?= $user["district"]?> | <?=$user["city"]?></p>
        </div>

        <div id="newP">
            <span id="close">close</span>
            <h3>New Product</h3>
            <form action="?" method="post" enctype="multipart/form-data">
                <p>Title:</p>
                <input type="text" name="title">
                <p>Stock:</p>
                <input type="number" name="stock" min="1">
                <p>Normal Price:</p>
                <input type="text" name="normalP"> TL
                <p>Discounted Price:</p>
                <input type="text" name="discounted"> TL
                <p>Expiration Date:</p>
                <input type="date" name="expDate">
                <p>Product Image:</p>
                <input type="file" name="img">
                <br>
                <button>Add Product</button>
            </form>
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

        <div id="editProduct">
            <span id="closeE">close</span>
            <h3>Edit Product</h3>
            <form action="?" method="post" enctype="multipart/form-data">
                <input type="hidden" name="editid" id="editId" >
                <p>Title:</p>
                <input type="text" name="title" id="edit-title">
                <p>Stock:</p>
                <input type="number" name="stock" id="edit-stock" min="1">
                <p>Normal Price:</p>
                <input type="text" name="normalP" id="edit-normalP"> TL
                <p>Discounted Price:</p>
                <input type="text" name="discounted" id="edit-discounted"> TL
                <p>Expiration Date:</p>
                <input type="date" name="expDate" id="edit-expDate">
                <p>Product Image (leave empty to keep current):</p>
                <input type="file" name="img">
                <br>
                <button>Save Changes</button>
            </form>
        </div>

        <div id="list">
            <span id="new">add new</span>
            <h2>List of Products in The Inventory</h2>
            <?php if(empty($products)) : ?>
                <p>There are currently no products in the inventory.</p>
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
                                <a href="?delete=<?= $p["id"]?>"><span class="material-symbols-outlined">delete</span></a>
                                <span class="material-symbols-outlined productEdit" data-id="<?=$p["id"]?>">edit</span>
                            </td>
                        </tr>
                    <?php endforeach?>
                </table>
            <?php endif ?>
        </div>
    </main>
    <script>
        $(function(){
            $("#new").click(function(){
                $("#editProfile").hide();
                $(".editProduct").hide();
                $("#newP").show();
            });

            $("#close").click(function(){
                $("#newP").hide();
            });

            $("#edit").click(function(){
                $("#newP").hide();
                $("#editProduct").hide();
                $("#editProfile").show();
            });

            $("#closeP").click(function(){
                $("#editProfile").hide();
            });

            $(".productEdit").click(function(){
                $("#newP").hide();
                $("#editProfile").hide();
                
                const products = <?= json_encode($products) ?>;
                const id = $(this).data("id");
                const product = products.find(p => p.id == id);

                if(product){
                    
                    $("#edit-title").val(product.title);
                    $("#edit-stock").val(product.stock);
                    $("#edit-normalP").val(product.normalPrice);
                    $("#edit-discounted").val(product.discounted);
                    $("#edit-expDate").val(product.expDate);
                    $("#editId").val(product.id);

                     // display the form
                }
                $("#editProduct").show();
            });

            $("#closeE").click(function(){
                $("#editProduct").hide();
            });
        });
    </script>
</body>
</html>