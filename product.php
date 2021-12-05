<?php

session_start();
require_once "config.php";

parse_str($_SERVER["QUERY_STRING"], $query);
$product_id = $query['p'];

$sql = "SELECT id, type, name, description, manufacturer, price, rate, image FROM product WHERE id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) != 0) {
            mysqli_stmt_bind_result($stmt, $id, $type, $name, $description, $manufacturer, $price, $rate, $image);

            if (!mysqli_stmt_fetch($stmt)) {
                echo "error fetch request";
            }
        } else {
            http_response_code(404);
            echo "404 ";
            echo $product_id;
            exit;
        }
    } else {
        echo "error with execute sql";
    }
} else {
    echo "Error with prepare sql";
    exit;
}

$user_is_authed = (isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == true);
if ($user_is_authed) {
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

?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>Продукт - <?= $name ?></title>
</head>
<body>
<div class="container mb-3">
    <h1>Продукт</h1>
    <?php echo ($is_staff) ? "<a href='/delete_product.php?p=$product_id'>Удалить продукт.</a>" : ''; ?>
    <div class="shadow">
        <div class="card">
            <div class="card-header">
                <?= $type ?>
            </div>
            <img src="/media/<?= $image ?>" class="card-img-top" alt="<?= $name ?>">
            <div class="card-body">
                <h5 class="card-title"><?= $name ?></h5>
                <h6 class="card-subtitle mb-2 text-muted"></h6<?= $manufacturer ?></h6>
                <p class="card-text"><?= $description ?></p>
                <p class="card-text"><small class="text-muted">Цена: <?= $price ?>, оценка: <?= $rate ?></small></p>
            </div>
        </div>
    </div>

    <?php
    if ($user_is_authed) {
        ?>
        <div class="mt-3">
            <form method="post" action="/rate.php">
                <label for="customRange2" class="form-label">Оценить продукт</label>
                <input type="range" name="rate" class="form-range" min="0" max="5" id="customRange2">
                <input type="hidden" name="product" value="<?= $product_id ?>">
                <button type="submit" class="btn btn-primary">Оценить</button>
            </form>
        </div>

        <div class="mt-3">
            <form method="post" action="/comment.php">
                <div class="mb-3">
                    <textarea name="comment" placeholder="Комментарий" class="form-control"
                              id="exampleFormControlTextarea1"
                              rows="3"></textarea>
                </div>
                <input type="hidden" name="product" value="<?= $product_id ?>">
                <button type="submit" class="btn btn-primary">Добавить комментарий</button>
            </form>
        </div>
        <?php
    }
    ?>
    <h3 class="mt-3">Комментарии:</h3>
    <?php
    $query = "SELECT text FROM comment WHERE product = $product_id";
    $result = mysqli_query($link, $query);
    //    mysqli_fetch_all($result, MYSQLI_BOTH);
    if (!$result) {
        die('Could not get data: ' . mysqli_error());
    }

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {}

    foreach ($result as $key => $var) {
        echo "<div class='card mt-3'>
  <div class='card-body'>
    " . $var["text"] . "
  </div>
</div>";
    }
    mysqli_free_result($result);
    mysqli_close($link);
    ?>


</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
</html>
