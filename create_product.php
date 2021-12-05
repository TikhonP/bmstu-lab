<?php
// Initialize the session
session_start();
require_once "config.php";

$upload_dir = '/home/r/rozhko40/rozhko40.beget.tech/public_html/media/';

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

$product_type = $product_name = $product_description = $product_manufacturer = $product_price = $product_image = "";
$product_type_err = $product_name_err = $product_description_err = $product_manufacturer_err = $product_price_err = $product_image_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $filename = tempnam($upload_dir, '');
    unlink($filename);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $filename)) {

        if (empty(trim($_POST["type"]))) {
            $product_type_err = "Пожалуйста, введите типа продукта.";
        } else {
            $product_type = trim($_POST["type"]);
        }

        if (empty(trim($_POST["name"]))) {
            $product_name_err = "Пожалуйста, введите имя продукта.";
        } else {
            $product_name = trim($_POST["name"]);
        }

        if (empty(trim($_POST["description"]))) {
            $product_description_err = "Пожалуйста, введите описание продукта.";
        } else {
            $product_description = trim($_POST["description"]);
        }

        if (empty(trim($_POST["manufacturer"]))) {
            $product_manufacturer_err = "Пожалуйста, введите производителя продукта.";
        } else {
            $product_manufacturer = trim($_POST["manufacturer"]);
        }

        if (empty(trim($_POST["price"]))) {
            $product_price_err = "Пожалуйста, введите цену продукта.";
        } else {
            $product_price = trim($_POST["price"]);
        }

        if (empty($product_type_err) && empty($product_name_err) && empty($product_description_err)
            && empty($product_manufacturer_err) && empty($product_manufacturer_err) && empty($product_price_err)
            && empty($product_image_err)) {

            $sql = "INSERT INTO  product (type, name, description, manufacturer, price, image) VALUES (?, ?, ?, ?, ?, ?)";

            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssis", $product_type, $product_name,
                    $product_description, $product_manufacturer, $product_price, $param_filename);

                $param_filename = basename($filename);

                if (mysqli_stmt_execute($stmt)) {
                    header("location: index.php");
                    exit();
                } else {
                    echo "error with sql request";
                    echo mysqli_stmt_error($stmt);
                }

                mysqli_stmt_close($stmt);
            } else {
                echo "Error with prepare sql request";
            }
        }

    } else {
        $product_image_err = "Ошибка загрузки файла";
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

    <title>Добавление продукта</title>
</head>
<body>
<div class="text-center container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-md">
            <a class="navbar-brand" href="#">Привет, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</a>
        </div>
        <?php echo ($is_staff) ? "<a class='nav-link' href='create_product.php'>Создать товар</a>" : ''; ?>
        <a class="d-flex nav-link" href="logout.php">Выйти из аккаунта</a>
    </nav>
    <h1>Добавление продукта.</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <div class="form-floating mb-3">
            <select type="text" name="type"
                    class="form-select <?php echo (!empty($product_type_err)) ? 'is-invalid' : ''; ?>"
                    id="floatingSelect" aria-label="Floating label select example"
                    value="<?php echo ($product_type != '') ? $product_type : ''; ?>" required>
                <option selected>Выберите тип</option>
                <option value="радиокомплектующие">радиокомплектующие</option>
                <option value="компьютерные комплектующие">компьютерные комплектующие</option>
                <option value="различная электроника">различная электроника</option>
            </select>
            <label for="floatingSelect">Категоря товара</label>
            <span class="invalid-feedback"><?php echo $product_type_err; ?></span>
        </div>

        <div class="form-floating mb-3">
            <label for="nameField">Название</label>
            <input type="text" name="name"
                   class="form-control <?php echo (!empty($product_name_err)) ? 'is-invalid' : ''; ?>" id="nameField"
                   value="<?php echo ($product_name != '') ? $product_name : ''; ?>" required>
            <span class="invalid-feedback"><?php echo $product_name_err; ?></span>
        </div>

        <div class="form-floating mb-3">
            <textarea type="text" name="description"
                      class="form-control <?php echo (!empty($product_description_err)) ? 'is-invalid' : ''; ?>"
                      placeholder="Leave a comment here" id="floatingTextarea"
                      value="<?php echo ($product_description != '') ? $product_description : ''; ?>"></textarea>
            <label for="floatingTextarea">Описание</label>
            <span class="invalid-feedback"><?php echo $product_description_err; ?></span>
        </div>

        <div class="form-floating mb-3">
            <label for="nameField">Производитель</label>
            <input type="text" name="manufacturer"
                   class="form-control <?php echo (!empty($product_manufacturer_err)) ? 'is-invalid' : ''; ?>"
                   id="nameField"
                   value="<?php echo ($product_manufacturer != '') ? $product_manufacturer : ''; ?>">
            <span class="invalid-feedback"><?php echo $product_manufacturer_err; ?></span>
        </div>

        <div class="form-floating mb-3">
            <input type="number" name="price"
                   class="form-control <?php echo (!empty($product_price_err)) ? 'is-invalid' : ''; ?>"
                   id="floatingInput" placeholder="">
            <label for="floatingInput">Цена</label>
            <span class="invalid-feedback"><?php echo $product_price_err; ?></span>
        </div>

        <div class="mb-3">
            <label for="formFile" class="form-label">Изображение продукта</label>
            <input name="image" class="form-control <?php echo (!empty($product_image_err)) ? 'is-invalid' : ''; ?>"
                   type="file" id="formFile">
            <span class="invalid-feedback"><?php echo $product_image_err; ?></span>
        </div>

        <!--        <input type="hidden" name="MAX_FILE_SIZE" value="30000"/>-->

        <button type="submit" class="btn btn-primary">Добавить продукт</button>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
</html>
