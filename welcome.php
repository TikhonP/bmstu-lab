<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$sql = "SELECT id, username, is_staff, email FROM users WHERE username = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_username);
    $param_username = $_SESSION["username"];

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        mysqli_stmt_bind_result($stmt, $id, $username, $is_staff, $email);
        if (!mysqli_stmt_fetch($stmt)) {
            echo "error fetch request!";
            exit;
        }
    } else {
        echo "Error execute request!";
        exit;
    }
} else {
    echo "Error prepare request!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        body {
            font: 14px sans-serif;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-md">
            <a class="navbar-brand" href="#">Привет, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</a>
        </div>
        <?php echo ($is_staff) ? "<a class='nav-link' href='create_product.php'>Создать товар</a>" : ''; ?>
        <a class="d-flex nav-link" href="logout.php">Выйти из аккаунта</a>
    </nav>
    <p>
        контент
    </p>
    <footer class="border-top">
        <div class="container text-center mt-2">
            <p class="text-secondary">ИУ4-11Б</p>
        </div>
    </footer>
</div>
</body>
</html>