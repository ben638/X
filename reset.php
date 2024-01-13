<?php
    $dir = "./";
    session_start();
    require $dir . "lib/functions.inc.php";
    redirection(isset($_SESSION["email"]), true, false, $dir);
    $email = filter_input(INPUT_GET, "email", FILTER_SANITIZE_EMAIL);
    if (isset($_POST["password"]) && isset($_POST["passwordConfirm"]) && isset($_GET["email"]))
    {
        $password = htmlspecialchars($_POST['password']);
        $passwordConfirm = htmlspecialchars($_POST['passwordConfirm']);
        if ($password == $passwordConfirm)
        {
            updatePassword($email, $password);
        }
        else
        {
            error_log("Tried to register : passwords don't match.", 0);
            $passwordMessageError = "Les mots de passe ne correspondent pas";
        }
        
    }
    
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Modification du mot de passe - X</title>
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
                    <h2 class="text-info">Modification de votre mot de passe</h2>
                </div>
                <form method="post" action="reset.php?email=<?php echo htmlspecialchars($email); ?>">
                <div class="mb-3"><label class="form-label" for="password">Entrer un nou deveau mot de passe</label><input class="form-control item" type="password" id="password" data-bs-theme="light" name="password"></div>
                    <div class="mb-3"><label class="form-label" for="passwordConfirm">Confirmation du mot de passe</label><input class="form-control item" type="password" id="passwordConfirm" data-bs-theme="light" name="passwordConfirm"></div><button class="btn btn-primary" type="submit">Modifier le mot de passe</button>
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
</html>