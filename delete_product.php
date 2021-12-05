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

parse_str($_SERVER["QUERY_STRING"], $query);
$product_id = $query['p'];

$sql = "DELETE FROM comment WHERE product = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (!mysqli_stmt_execute($stmt)) {
        echo "error delete comments";
        exit;
    }
}

$sql = "DELETE FROM rate WHERE product = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (!mysqli_stmt_execute($stmt)) {
        echo "error delete rate";
        exit;
    }
}

$sql = "DELETE FROM product WHERE id = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (!mysqli_stmt_execute($stmt)) {
        echo "error delete product";
        exit;
    }
}

header("location: index.php");
?>