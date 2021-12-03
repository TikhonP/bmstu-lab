<?php

echo $_SERVER["QUERY_STRING"];

$parts = parse_url($_SERVER["QUERY_STRING"]);
parse_str($parts['query'], $query);
$product_id = $query['p'];

echo $product_id;

$sql = "SELECT id, type, name, description, manufacturer, price, rate, image FROM users WHERE id = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 0) {
            mysqli_stmt_bind_result($stmt, $id, $type, $name, $description, $manufacturer, $price, $rate, $image);

            if (!mysqli_stmt_fetch($stmt)) {
                echo "error fetch request";
            }
        } else {
            http_response_code(404);
            exit;
        }
    } else {
        echo "error with execute sql";
    }
} else {
    echo "Error with prepare sql";
    echo mysqli_stmt_error($stmt);
    exit;
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
<h1>Продукт</h1>
<div class="container">
    <div class="card">
        <img src="/media/<?= $image ?>" class="card-img-top" alt="<?= $name ?>">
        <div class="card-body">
            <div class="card-header">
                <?= $type ?>
            </div>
            <h5 class="card-title"><?= $name ?></h5>
            <h6 class="card-subtitle mb-2 text-muted"></h6<?= $manufacturer ?></h6>
            <p class="card-text"><?= $description ?></p>
            <p class="card-text"><small class="text-muted">Цена: <?= $price ?>, оценка: <?= $rate ?></small></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
</html>
