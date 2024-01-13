<?php
    $dir = "./";
    session_start();
    require $dir . "lib/functions.inc.php";
    redirection(isset($_SESSION["email"]), false, true, $dir);
    if (isset($_POST["message"]))
    {
        $message = $_POST["message"];
		$hasMedia = false;
		$files = true;
        if (isset($_FILES["medias"]))
		{
			if ($_FILES["medias"]["name"][0] != "")
			{
				$files = $_FILES["medias"];
				$hasMedia = true;
				$files = renameMedias($files);
			}
		}
        $url = false;
        if (isset($_POST["mediaFromURL"]))
        {
            $url = htmlspecialchars($_POST["mediaFromURL"]);
        }
        newPost($message, $hasMedia, $files, $_SESSION["email"], $url);
    }
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>New post - X</title>
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
                    <h2 class="text-info">Nouveau post</h2>
                </div>
                <form method="post" action="newPost.php" enctype="multipart/form-data">
                    <div class="mb-3"><label class="form-label" for="message">Message</label><textarea class="form-control item" type="textarea" id="message" data-bs-theme="light" name="message"></textarea></div>
                    <div class="mb-3"><label class="form-label" for="medias">Fichier</label><input class="form-control" type="file" id="medias" data-bs-theme="light" name="medias[]" multiple></div>
                    <div class="mb-3"><label class="form-label" for="mediaFromURL">URL</label><input class="form-control item" type="text" id="mediaFromURL" data-bs-theme="light" name="mediaFromURL"></div>
                    <div class="mb-3"></div><button class="btn btn-primary" type="submit">Poster</button>
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