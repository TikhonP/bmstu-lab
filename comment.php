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
                header("location: welcome.php");
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

if (empty(trim($_POST["product_id"]))) {
    echo "Error empty product_id";
} else {
    $product_id = trim($_POST["product_id"]);
}

if (empty(trim($_POST["comment"]))) {
    echo "Error empty comment";
} else {
    $comment = trim($_POST["comment"]);
}
$sql = "INSERT INTO comment (text, creator_user, product) VALUES (?, ?, ?)";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "sii", $comment, $id, $product_id);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect to login page
        header("location: product.php?p=$product_id");
    } else {
        echo "Ой! Что-то пошло не так. Попробуйте еще раз позже.";
        echo mysqli_stmt_error($stmt);
    }
}
?>