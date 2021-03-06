<?php

session_start();
require_once "config.php";

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
        if (mysqli_stmt_fetch($stmt)) {
            if (!$is_staff) {
                header("location: index.php");
                exit;
            }
        } else {
            echo "error fetch request!";
            exit;
        }
    } else {
        echo "Error execute request!";
        exit;
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Error prepare request!";
    echo mysqli_stmt_error($stmt);
    exit;
}

if (empty(trim($_POST["product"]))) {
    echo "Error empty product_id";
    exit;
} else {
    $product_id = trim($_POST["product"]);
}

if (empty(trim($_POST["rate"]))) {
    echo "Error empty rate";
    exit;
} else {
    $rate = trim($_POST["rate"]);
}

$sql = "INSERT INTO rate (rate, creator_user, product) VALUES (?, ?, ?)";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "iii", $rate, $id, $product_id);

    if (mysqli_stmt_execute($stmt)) {
        $sql = "SELECT Avg(rate) FROM rate WHERE product = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $product_id);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                mysqli_stmt_bind_result($stmt, $avr_rate);

                if (mysqli_stmt_fetch($stmt)) {

                    $sql = "UPDATE product SET rate = ? WHERE product.id = ?";

                    if ($stmt = mysqli_prepare($link, $sql)) {
                        mysqli_stmt_bind_param($stmt, "di", $avr_rate, $product_id);

                        if (mysqli_stmt_execute($stmt)) {
                            header("location: product.php?p=$product_id");
                        }
                    }
                }
            }
        }
    } else {
        echo "????! ??????-???? ?????????? ???? ??????. ???????????????????? ?????? ?????? ??????????.";
        echo mysqli_stmt_error($stmt);
    }
}
?>