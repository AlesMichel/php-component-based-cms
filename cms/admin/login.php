<?php
require_once('../config.php');


if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($username == LOGIN && $password == PASS){
        session_start();
        $_SESSION["user"] = "admin";
        header("Location:modules/index.php");
    }else{
        echo '<div class="alert alert-danger mt-3" role="alert">
           Špatné uživatelské jméno nebo heslo.
        </div>';
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashborard</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="login-form">
                <form action="login.php" method="post">
                    <label for="loginUser" class="form-label">Uživatel</label>
                    <div class="form-field">
                        <input class="form-control" type="text" name="username" id="loginUser" placeholder="Uživatel">
                        
                    </div>
                    <label for="loginPass" class="form-label mt-3">Heslo</label>
                    <div class="form-field">
                        <input class="form-control" type="password" name="password" id="loginPass" placeholder="Heslo">
                    </div>
                    <div class="from-field mt-2">
                        <input class="btn btn-secondary" type="submit" value="Příhlásit se" name="login">
                    </div>
                </form>
            </div>
        </div>
</body>
</html>