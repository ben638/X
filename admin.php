<?php
    session_start();
    $dir = "./";
    require $dir . "lib/admin.inc.php";
    session_expiration($dir);
    if (!isset($_SESSION["email"]) || !isset($_SESSION["isAdmin"]))
    {
        error_log("Tried to access admin page without being logged in or not being admin.", 0);
        header("Location: " . $dir . "index.php");
        exit(0);
    }
    if (isset($_POST["userMail"]))
    {
        $email = filter_input(INPUT_POST, "userMail", FILTER_VALIDATE_EMAIL);
        if (isset($_POST["deleteUser"]))
        {
            deleteUser($email);
        }
        else {
            $isActive = htmlspecialchars($_POST["isActive"]);
            updateUserActivity($email, !$isActive);
        }

    }
    $profils = getAllProfils();
    $edit = false;
    if (isset($_SESSION["email"]) && isset($_GET["email"]))
    {
        if ($_SESSION["email"] == $_GET["email"])
        {
            $edit = true;
        }
    }
    if (isset($_SESSION["email"]) && isset($_SESSION["isAdmin"]))
    {
        $edit = true;
    }
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Admin page - X</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i,600,600i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/simple-line-icons.min.css">
    <link rel="stylesheet" href="assets/css/baguetteBox.min.css">
    <link rel="stylesheet" href="assets/css/vanilla-zoom.min.css">
</head>

<body>
    <?php 
        require "./headers/navbar.php";
    ?>
    <main class="page blog-post-list">
        <section class="clean-block clean-blog-list dark">
            <div class="container">
                <?php
                    for ($i = 0; $i < count($profils);$i++)
                    {
                ?>
                <div class="block-heading">
                <div class="row justify-content-center">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card text-center clean-card"><img class="card-img-top w-100 d-block" src="./img/posts/<?php echo htmlspecialchars($profils[$i]["name"]);?>" alt="avatar">
                            <div class="card-body info">
                                <h4 class="card-title"><?php echo htmlspecialchars($profils[$i]["username"]);?></h4>
                                <p class="card-text"><?php echo htmlspecialchars($profils[$i]["bio"]);?></p>
                                <?php
                                    if ($edit)
                                    {
                                        ?>
                                        <form action="editSettings.php?userMail=<?php echo htmlspecialchars($profils[$i]["email"]); ?>" method="post">
                                            <button class="btn btn-primary gamanet-btn-icon-primary-dest" type="submit">Modifier le profil</button>
                                        </form>
                                        <form action="admin.php" method="post">
                                            <input type="hidden" name="isActive" value="<?php echo htmlspecialchars($profils[$i]["isActive"]);?>">
                                            <input type="hidden" name="userMail" value="<?php echo htmlspecialchars($profils[$i]["email"]);?>">
                                            <button class="btn btn-primary gamanet-btn-icon-primary-dest" type="submit"><?php echo $profils[$i]["isActive"] ? "Désactiver" : "Activer";?></button>
                                        </form>
                                        <form action="admin.php" method="post">
                                            <input type="hidden" name="userMail" value="<?php echo htmlspecialchars($profils[$i]["email"]);?>">
                                            <input type="hidden" name="deleteUser">
                                            <button class="btn btn-primary gamanet-btn-icon-primary-dest" type="submit">Supprimer</button>
                                        </form>
                                        <?php
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <?php
                    }
                ?>
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