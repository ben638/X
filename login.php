<?php
    $dir = "./";
    session_start();
    require $dir . "lib/functions.inc.php";
    redirection(isset($_SESSION["email"]), true, false, $dir);
    if (isset($_POST["email"]) && isset($_POST["password"]))
    {
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $password = htmlspecialchars($_POST['password']);
        $userInfo = checkUserExists($email)[0];
        if (!$userInfo)
        {
            error_log("Tried to login : account doesn't exist.", 0);
        }
        else if ($email == $userInfo['email'] && $password == $userInfo['password'])
        {
            if ($userInfo["isActive"])
            {
                $_SESSION['email'] = $email;
                $_SESSION['lastLogin'] = time();
                $_SESSION['token'] = getToken($_SESSION['email']);
                if ($userInfo["isAdmin"])
                {
                    $_SESSION['isAdmin'] = true;
                }
                header('Location: index.php');
                exit(0);
            }
            else {
                error_log("Tried to login : account is disabled.", 0);
            }
        }
    }
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login - X</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i,600,600i&amp;display=swap">
    <link rel="stylesheet" href="assets/css/baguetteBox.min.css">
    <link rel="stylesheet" href="assets/css/vanilla-zoom.min.css">
</head>

<body>
    <?php 
        require "headers/navbar.php";
    ?>
    <main class="page login-page">
        <section class="clean-block clean-form dark">
            <div class="container">
                <div class="block-heading">
                    <h2 class="text-info">Connexion</h2>
                </div>
                <form method="post" action="login.php">
                    <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control item" type="email" id="email" data-bs-theme="light" name="email"></div>
                    <div class="mb-3"><label class="form-label" for="password">Mot de passe</label><input class="form-control" type="password" id="password" data-bs-theme="light" name="password"></div>
                    <a href="oubli.php">Mot de passe oublié ?</a>
                    <div class="mb-3"></div><button class="btn btn-primary" type="submit">Connexion</button>
                </form>
            </div>
        </section>
    </main>
    <?php
        require "headers/footer.php";
    ?>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/baguetteBox.min.js"></script>
    <script src="assets/js/vanilla-zoom.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>