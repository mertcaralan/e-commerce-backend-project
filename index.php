<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link
    href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap"
    rel="stylesheet"
  />
  <title>Registration</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Jost', sans-serif;
    }

    body {
      background: #e8f5e9;
      color: #2e7d32;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    #main {
      background-color: #ffffff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      width: 90%;
      max-width: 500px;
      text-align: center;
    }

    header h1 {
      font-size: 24px;
      margin-bottom: 30px;
      color: #1b5e20;
    }

    header a {
      display: inline-block;
      margin: 10px 15px;
      padding: 10px 20px;
      text-decoration: none;
      color: white;
      background-color: #66bb6a;
      border-radius: 25px;
      transition: background-color 0.3s ease;
    }

    header a:hover {
      background-color: #388e3c;
    }

    footer {
      margin-top: 40px;
      font-size: 12px;
      color: #9e9e9e;
    }
  </style>
</head>
<body>
  <div id="main">
    <header>
      <h1>A Sustainability e-Commerce Project</h1>
      <a href="login.php">Login</a>
      <a href="./signUp.php?type=Market">Register Market</a>
      <a href="./signUp.php?type=Consumer">Register Consumer</a>
    </header>
   
  </div>
</body>
</html>
