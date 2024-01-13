<?php
    //bTBRh259DzXsYtC6ZkfQ
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    //Load Composer's autoloader
    require 'vendor/autoload.php';
    $dir = "./";
    session_start();
    require $dir . "lib/functions.inc.php";
    redirection(isset($_SESSION["email"]), true, false, $dir);
    if (isset($_POST["email"]))
    {
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $userInfo = checkUserExists($email)[0];
        if (!$userInfo)
        {  
            error_log("Tried to reset password : account doesn't exist.", 0);
            $accountErrorMessage = "Votre compte n'existe pas";
        }
        else if ($email == $userInfo['email'])
        {
            try {
                $phpmailer = new PHPMailer();
                $phpmailer->isSMTP();
                $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
                $phpmailer->SMTPAuth = true;
                $phpmailer->Port = 2525;
                $phpmailer->Username = '01e85ef9b4a71f';
                $phpmailer->Password = '98da2caef1c2b4';

                $phpmailer->setFrom('no-reply@x2.com', 'X2');
                $phpmailer->addAddress('2f6d5a1ba6-b211d8@inbox.mailtrap.io');
                $phpmailer->isHTML(true);
                $phpmailer->Subject = 'Reinitialiser votre mot de passe';
                $phpmailer->Body    = "<a href=\"http://localhost/x/x2/reset.php?email=$email\">Cliquer ici pour reinitialiser votre mot de passe</a>";
                $phpmailer->AltBody = "Voici le lien : http://localhost/x/x2/reset.php?email=$email"; //texte si le html ne marche pas dans le mail

                $phpmailer->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                error_log("Message could not be sent. Mailer Error: {$phpmailer->ErrorInfo}.", 0);
                echo "Message could not be sent. Mailer Error: {$phpmailer->ErrorInfo}";
            }
        }
    }
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Oubli du mot de passe - X</title>
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
                    <h2 class="text-info">Récupération du mot de passe</h2>
                </div>
                <form method="post" action="oubli.php">
                    <div class="mb-3"><label class="form-label" for="email">Email de votre compte</label><input class="form-control item" type="email" id="email" data-bs-theme="light" name="email"></div>
                    <div class="mb-3"></div><button class="btn btn-primary" type="submit">Récupérer le mot de passe</button>
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