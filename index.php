<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    /* Reset some default styles */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(135deg, #6fcf97, #66d3fa);
      color: #333;
      padding: 20px;
    }

    #main {
      background: white;
      padding: 40px 50px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      max-width: 400px;
      width: 100%;
      text-align: center;
    }

    header h1 {
      font-size: 1.8rem;
      margin-bottom: 25px;
      color: #2a7f62;
      font-weight: 700;
    }

    a {
      display: inline-block;
      margin: 12px 0;
      padding: 12px 25px;
      text-decoration: none;
      font-weight: 600;
      border-radius: 50px;
      background-color: #2a7f62;
      color: white;
      box-shadow: 0 4px 12px rgba(42, 127, 98, 0.4);
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    a:hover {
      background-color: #245f48;
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(36, 95, 72, 0.5);
    }

    footer {
      margin-top: 30px;
      font-size: 0.85rem;
      color: #777;
    }

    /* Responsive for smaller screens */
    @media (max-width: 480px) {
      #main {
        padding: 30px 20px;
      }

      header h1 {
        font-size: 1.5rem;
      }

      a {
        width: 100%;
        padding: 15px 0;
      }
    }
  </style>
</head>
<body>
    <div id="main">
        <header>
            <h1>A Sustainability e-Commerce Project</h3>
            <br>
            <a href="login.php">Login</a>
            <br>
            <a href="signUp.php">Sign Up</a>
        </header>
        <footer>

        </footer>
    </div>
</body>
</html>