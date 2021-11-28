<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: register.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Регистрация</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        body {
            font: 14px sans-serif;
        }

        .wrapper {
            width: 360px;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="container text-center">
    <h2>Регистрация</h2>
    <p>
        Вы уже зашли как, <?php echo htmlspecialchars($_SESSION["username"]); ?>.
        <a href="logout.php">Выйти и зарегистрироваться под другим именем?</a>
    </p>
    <footer class="border-top">
        <div class="container text-center">
            <p class="text-secondary">ИУ4-11Б</p>
        </div>
    </footer>
</div>
</body>
</html>