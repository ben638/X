<?php
    $dir = "./";
    session_start();
    require $dir . "lib/functions.inc.php";
    redirection(isset($_SESSION["email"]), false, false, $dir);
    $profil = false;
    $posts = false;
    $medias = false;
    if (isset($_GET["email"]))
    {
        $email = filter_input(INPUT_GET, "email", FILTER_VALIDATE_EMAIL);
        //$profil = getProfil($email);
        $profil = getAllProfil($email);
        $posts = getAllPosts($email);
        for ($i = 0; $i < count($posts); $i++)
        {
            $medias[$i] = getMedias($posts[$i]["idPost"]);
        }
    }
    if (!isset($_GET["email"]) || !$profil) {
        error_log("Tried to access to profil.php without being connected or with an invalid email.", 0);
        header("Location: " . $dir . "posts.php");
        exit(0);
    }
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
    <title>Posts - X</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i,600,600i&amp;display=swap">
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
                <div class="block-heading">
                <div class="row justify-content-center">
                    <div class="col-sm-6 col-lg-4">
                        <div class="card text-center clean-card"><img class="card-img-top w-100 d-block" src="./img/posts/<?php echo htmlspecialchars($profil[0]["name"]);?>" alt="avatar">
                            <div class="card-body info">
                                <h4 class="card-title"><?php echo htmlspecialchars($profil[0]["username"]); ?></h4>
                                <p class="card-text"><?php echo htmlspecialchars($profil[0]["bio"]); ?></p>
                                <?php
                                    if ($edit)
                                    {
                                        ?>
                                        <form action="editSettings.php?userMail=<?php echo htmlspecialchars($profil[0]["email"]); ?>" method="post">
                                            <button class="btn btn-primary gamanet-btn-icon-primary-dest" type="submit">Modifier le profil</button>
                                        </form>
                                        <?php
                                    }
                                ?>
                                <div class="icons"><a href="#"><i class="icon-social-facebook"></i></a><a href="#"><i class="icon-social-instagram"></i></a><a href="#"><i class="icon-social-twitter"></i></a></div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="block-content">
                    <?php
                        if ($posts)
                        {
                            $nbPosts = count($posts);
                            for ($i = 0; $i < $nbPosts; $i++)
                            {
                            ?>
                                <div class="clean-blog-post">
                                <div class="row">
                                    <div class="col-lg-5">
                                    <?php
                                    for ($j = 0; $j < count($medias[$i]); $j++)
                                    {
                                        if ($medias[$i][$j]["type"] == "inconnu")
                                        {
                                    ?>
                                        <img class="rounded img-fluid" src="<?php echo htmlspecialchars($medias[$i][$j]["name"]); ?>">
                                    <?php
                                        }
                                        else {
                                    ?>
                                        <img class="rounded img-fluid" src="./img/posts/<?php echo htmlspecialchars($medias[$i][$j]["name"]); ?>">
                                    <?php
                                        }
                                    }
                                    ?>
                                    </div>
                                        <div class="col-lg-7">
                                            <div class="info"><span class="text-muted"><?php echo htmlspecialchars($posts[$i]["creationDate"]); ?></span></div>
                                            <p><?php echo htmlspecialchars($posts[$i]["message"]); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                        }
                    ?>
                </div>
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
