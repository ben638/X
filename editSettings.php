<?php
    $dir = "./";
    session_start();
    require $dir . "lib/functions.inc.php";
    redirection(isset($_SESSION["email"]), false, true, $dir);
    $token = false;
    if (isset($_POST["lastname"]) && isset($_POST["firstname"]) && isset($_POST["username"]) && isset($_POST["birthdate"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["passwordConfirm"]))
    {
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
        if ($email)
        {
            $firstname = htmlspecialchars($_POST["firstname"]);
            $lastname = htmlspecialchars($_POST["lastname"]);
            $username = htmlspecialchars($_POST["username"]);
            $birthdate = htmlspecialchars($_POST["birthdate"]);
            $bio = htmlspecialchars($_POST["bio"]);
            $password = htmlspecialchars($_POST['password']);
            $passwordConfirm = htmlspecialchars($_POST['passwordConfirm']);
            $idMedia = htmlspecialchars($_POST['idMedia']);
            $success = false;
            if ($password == $passwordConfirm)
            {
                if ($_FILES["medias"]["error"][0] == 0)
                {
                    if ($_FILES["medias"]["name"][0] != "")
                    {
                        $files = $_FILES["medias"];
                        $hasMedia = true;
                        $files = renameMedias($files);
                        createAvatar($files["type"][0], $files["name"][0]);
                        saveMedias($files);
                        $idMedia = getIdMedia($files["name"][0]);
                    }
                }
                $success = updateProfil($_SESSION["email"], $email, $username, $firstname, $lastname, $birthdate, $password, $bio, $idMedia);
                if ($success)
                {
                    $_SESSION["email"] = $email;
                }
                else {
                    error_log("Tried to update profil : email already used.", 0);
                }
            }
            else
            {
                error_log("Tried to update profil : passwords don't match.", 0);
            }
        }
        else
        {
            error_log("Tried to update profil : email not valid.", 0);
        }
    }
    if (isset($_POST["newToken"]))
    {
        $token = newToken($_SESSION["email"]);
    }
    else {
        $token = getToken($_SESSION["email"]);
    }
    if (isset($_GET["userMail"]) && isset($_SESSION["isAdmin"]))
    {
        $profil = getAllProfil(htmlspecialchars($_GET["userMail"]));
    }
    else {
        $profil = getAllProfil($_SESSION["email"]);
    }

?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Modifier les données - X</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i,600,600i&amp;display=swap">
    <link rel="stylesheet" href="assets/css/baguetteBox.min.css">
    <link rel="stylesheet" href="assets/css/vanilla-zoom.min.css">
</head>

<body>
    <?php 
        require "headers/navbar.php";
    ?>
    <main class="page registration-page">
        <section class="clean-block clean-form dark">
            <div class="container">
                <div class="block-heading">
                    <h2 class="text-info">Modifier les données</h2>
                </div>
                <form method="post" action="editSettings.php" enctype="multipart/form-data">
                    <div class="mb-3"><label class="form-label" for="lastname">Nom</label><input class="form-control item" type="text" id="lastname" data-bs-theme="light" name="lastname" value="<?php echo htmlspecialchars($profil[0]["lastname"]); ?>" required></div>
                    <div class="mb-3"><label class="form-label" for="firstname">Prénom</label><input class="form-control item" type="text" id="firstname" data-bs-theme="light" name="firstname" value="<?php echo htmlspecialchars($profil[0]["firstname"]); ?>" required></div>
                    <div class="mb-3"><label class="form-label" for="username">Pseudo</label><input class="form-control item" type="text" id="username" data-bs-theme="light" name="username" value="<?php echo htmlspecialchars($profil[0]["username"]); ?>" required></div>
                    <div class="mb-3"><label class="form-label" for="birthdate">Date de naissance</label><input class="form-control" type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($profil[0]["birthdate"]); ?>" required></div>
                    <div class="mb-3"><label class="form-label" for="bio">Bio</label><textarea class="form-control item" type="textarea" id="bio" data-bs-theme="light" name="bio" required><?php echo htmlspecialchars($profil[0]["bio"]); ?></textarea></div>
                    <div class="mb-3"><label class="form-label" for="medias">Fichier</label><input class="form-control" type="file" id="medias" data-bs-theme="light" name="medias[]"></div>
                    <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control item" type="email" id="email" data-bs-theme="light" name="email" value="<?php echo htmlspecialchars($profil[0]["email"]); ?>" required></div>
                    <div class="mb-3"><label class="form-label" for="password">Mot de passe</label><input class="form-control item" type="password" id="password" data-bs-theme="light" name="password" required></div>
                    <div class="mb-3"><label class="form-label" for="passwordConfirm">Confirmation du mot de passe</label><input class="form-control item" type="password" id="passwordConfirm" data-bs-theme="light" name="passwordConfirm" required></div>
                    <input type="hidden" name="idMedia" value="<?php echo htmlspecialchars($profil[0]["idMedia"]); ?>">
                    <button class="btn btn-primary" type="submit">Mettre à jour</button>
                </form>
            </div>
            <div class="container">
                <div class="block-heading">
                    <h2 class="text-info">Générer un token API</h2>
                </div>
                <form method="post" action="editSettings.php" >
                    <button class="btn btn-primary" type="submit" name="newToken">Générer le token</button>
                    <br>
                    <br>
                    <?php
                        if ($token)
                        {
                            ?>
                                <div class="mb-3"><label class="form-label" for="token">Votre token</label><textarea class="form-control item" type="textarea" id="token" data-bs-theme="light" name="token" rows="11"><?php echo htmlspecialchars($token); ?></textarea></div>
                            <?php 
                        }
                    ?>
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