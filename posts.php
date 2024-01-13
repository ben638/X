<?php 
    $dir = "./";
    session_start();
    require $dir . "lib/functions.inc.php";
    redirection(isset($_SESSION["email"]), false, false, $dir);
    if (isset($_POST["idMessageToReport"]))
    {
        $idPost = htmlspecialchars($_POST["idMessageToReport"]);
        reportPost($idPost);
    }
    $posts = false;
    //$posts = getPostsFromAllUsers();
    $posts = getPostsFromAllUsers();
	$medias;
	for ($i = 0; $i < count($posts); $i++)
	{
		$medias[$i] = getMedias($posts[$i]["idPost"]);
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
                    <h2 class="text-info">Posts</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc quam urna, dignissim nec auctor in, mattis vitae leo.</p>
                </div>
                <div class="block-content">
                    <?php
                        $nbPost = count($posts);
                        for ($i = 0; $i < $nbPost; $i++)
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
                                    <div class="info"><span class="text-muted"><?php echo htmlspecialchars($posts[$i]["creationDate"]); ?> par&nbsp;<a href="profil.php?email=<?php echo htmlspecialchars($posts[$i]["email"]); ?>"><?php echo htmlspecialchars($posts[$i]["username"]); ?></a></span></div>
                                    <p><?php echo htmlspecialchars($posts[$i]["message"]); ?></p>
                                    <?php
                                        if (isset($_SESSION["email"]))
                                        {
                                    ?>
                                            <form action="./posts.php" method="post">
                                                <input type="hidden" name="idMessageToReport" value="<?php echo htmlspecialchars($posts[$i]["idPost"]); ?>">
                                                <div class="mb-3"></div><button class="btn btn-primary" type="submit">Report</button>
                                            </form>
                                    <?php
                                        }
                                    ?>
                                    </div>
                                </div>
                            </div>
                    <?php
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