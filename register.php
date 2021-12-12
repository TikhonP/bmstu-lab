<?php

session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: register_authed.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = "";
$username_err = $password_err = $confirm_password_err = $email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Введите имя пользователя. ";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Имя пользователя может включать только буквы, цифры и нижние подчеркивания. ";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Имя пользователя уже занято. ";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Пожалуйста, введите пароль. ";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Парольдолжен включать, как минимум 6 символов. ";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Подтвердите пароль.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Пароли не совпадают.";
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email = "";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Введите верный email. ";
    } else {
        $email = trim($_POST["email"]);
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_email);

            // Set parameters
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: login.php");
            } else {
                echo "Ой! Что-то пошло не так. Попробуйте еще раз позже.";
                echo mysqli_stmt_error($stmt);
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    } else {
        echo ($username_err . $password_err . $confirm_password_err . $email_err);
        exit();
    }

    // Close connection
    mysqli_close($link);
} else {
?>

<!DOCTYPE html>
<html lang="ru">
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
    <p>Пожалуйста, заполните все поля, чтобы войти в аккаунт.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="registerForm">
        <div class="alert alert-danger" role="alert" id="formAlert"></div>

        <div class="form-floating mb-3">
            <input type="text" name="username"
                   class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" id="floatingInput"
                   placeholder="username" value="<?php echo $username; ?>">
            <label for="floatingInput">Имя пользователя</label>
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>

        <div class="form-floating mb-3">
            <input type="email" name="email"
                   class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" id="floatingInputE"
                   placeholder="username" value="<?php echo $email; ?>">
            <label for="floatingInputE">Email</label>
            <span class="invalid-feedback"><?php echo $email_err; ?></span>
        </div>

        <div class="form-floating mb-3">
            <input type="password" name="password"
                   class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" id="floatingPassword"
                   placeholder="Password" value="<?php echo $password; ?>">
            <label for="floatingPassword">Пароль</label>
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>

        <div class="form-floating mb-3">
            <input type="password" name="confirm_password"
                   class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>"
                   id="floatingPasswordc" placeholder="Password" value="<?php echo $confirm_password; ?>">
            <label for="floatingPasswordc">Подтвердите пароль</label>
            <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
        </div>

        <div class="container">
            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
            <button type="reset" class="btn btn-secondary ml-2">Сбросить</button>
            <p class="mt-2">Уже есть аккаунт? <a href="login.php">Войти</a>.</p>
        </div>
    </form>
    <footer class="border-top">
        <div class="container text-center mt-2">
            <p class="text-secondary">ИУ4-11Б</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script>
        const commitForm = document.getElementById('registerForm');

        commitForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const form = $("#registerForm");
            const url = form.attr('action');

            $.ajax({
                type: "POST",
                url: url,
                dataType: 'html',
                data: form.serialize(),
                success: function(data) {
                    $('#formAlert').html(data);
                    alert(data);
                }
            });
        });
    </script>
</div>
</body>
</html>

<?php } ?>