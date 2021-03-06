<?php
// Initialize the session
session_start();
require_once "config.php";

// Check if the user is logged in, if not then redirect him to login page
if (isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == true) {
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
        echo mysqli_stmt_error($stmt);
        exit;
    }
}

function display_data($data)
{
    $id = "";
    $output = "";
    foreach ($data as $key => $var) {
        //$output .= '<tr>';
        if ($key === 0) {
            $output .= '<tr>';
            foreach ($var as $col => $val) {
                if ($col != "id") {
                    $output .= "<td>" . $col . '</td>';
                }
            }
            $output .= '</tr>';
            foreach ($var as $col => $val) {
                $output .= '<td>' . $val . '</td>';
            }
            $output .= '</tr>';
        } else {
            $output .= '<tr>';
            foreach ($var as $col => $val) {
                if ($col != "id") {
                    if ($col != "name") {
                        $output .= '<td>' . $val . '</td>';
                    } else {
                        $output .= '<td><a href="/product.php?p=' . $id . '">' . $val . '</td></a>';
                    }
                } else {
                    $id = $val;
                }
            }
            $output .= '</tr>';
        }
    }
    echo $output;
}

?>


<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>????????</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <?php if (isset($_SESSION["loggedin"])): ?>
        <div class="container-md">
            <a class="navbar-brand" href="#">????????????, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</a>
        </div>
        <?php echo ($is_staff) ? "<a class='nav-link' href='create_product.php'>?????????????? ??????????</a>" : ''; ?>
        <a class="d-flex nav-link" href="/logout.php">?????????? ???? ????????????????</a>
    <?php else: ?>
        <div class="container-md">
            <a class="navbar-brand" href="#">????????????</b>.</a>
        </div>
        <a class="d-flex nav-link" href="/login.php">??????????</a>
    <?php endif ?>
</nav>
<div class="container">
    <table class="table">
        <?php
        $query = "SELECT id, name, type, description, manufacturer, price, rate FROM product";
        $result = mysqli_query($link, $query);
        //    mysqli_fetch_all($result, MYSQLI_BOTH);
        if (!$result) {
            die('Could not get data: ' . mysqli_error());
        }

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { }

        display_data($result);
        mysqli_free_result($result);
        mysqli_close($link);
        ?>
    </table>
</div>
<footer class="border-top">
    <div class="container text-center mt-2">
        <p class="text-secondary">????4-11??</p>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
</html>
