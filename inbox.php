<?php
    $dir = "./";
    session_start();
    require $dir . "lib/functions.inc.php";
    redirection(isset($_SESSION["email"]), false, true, $dir);
    if (isset($_POST["username"]))
    {
        $username = htmlspecialchars($_POST["username"]);
        $email = getMail($username);
        if ($email)
        {
            $conv = searchConversation($_SESSION["email"], $email);
            if ($conv)
            {
                if ($conv[0]["from"] == $_SESSION["email"])
                {
                    header("Location: " . $dir . "mp.php?to=" . $conv[0]["to"]);
                }
                else {
                    header("Location: " . $dir . "mp.php?to=" . $conv[0]["from"]);
                }
                exit(0);
            }
            else {
                newConversation($_SESSION["email"], $email);
                header("Location: " . $dir . "mp.php?to=" . $email);
                exit(0);
            }
        }
    }
    $conversations = getConversations($_SESSION["email"]);
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Blog - X</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i,600,600i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/baguetteBox.min.css">
    <link rel="stylesheet" href="assets/css/Features-Minimal-icons.css">
    <link rel="stylesheet" href="assets/css/Gamanet_Btn_icon_primary_dest_bs5.css">
    <link rel="stylesheet" href="assets/css/some-message.css">
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
                    <h2 class="text-info">Vos discussions</h2>
                </div>
                <div class="row" style="padding-bottom: 50px;">
                    <div class="col-md-10 offset-md-1">
                        <div class="card m-auto" style="max-width:850px">
                            <div class="card-body">
                                <form class="d-flex align-items-center" method="post" action="inbox.php">
                                    <!--<i class="fas fa-search d-none d-sm-block h4 text-body m-0"></i>-->
                                    <input class="form-control form-control-lg flex-shrink-1 form-control-borderless" type="search" placeholder="Chercher un utilisateur" name="username">
                                    <button class="btn btn-success btn-lg" type="submit" style="--bs-text-opacity: 1;background-color: rgba(var(--bs-info-rgb), var(--bs-text-opacity)) !important;">
                                        Rechercher
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block-content">
                    <?php
                        $nbConv = count($conversations);
                        for ($i = 0; $i < $nbConv; $i++)
                        {
                            
                    ?>
                        <a href="mp.php?to=<?php echo htmlspecialchars($conversations[$i]["email"]); ?>">
                            <div class="clean-blog-post">
                                <div class="row">
                                    <div class="col">
                                        <div class="d-flex" id="some-message">
                                            <div class="profile"><img class="rounded-circle" src="./img/posts/<?php echo $conversations[$i]["name"]; ?>"></div>
                                            <div class="content">
                                                <h3><?php echo htmlspecialchars($conversations[$i]["firstname"]) . " " . htmlspecialchars($conversations[$i]["lastname"]); ?>&nbsp;<span>@<?php echo htmlspecialchars($conversations[$i]["username"]); ?><!-- - 3h ago--></span></h3>
                                                <p></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </section>
    </main>
    <?php 
        require "./headers/footer.php";
    ?>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/baguetteBox.min.js"></script>
    <script src="assets/js/vanilla-zoom.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>