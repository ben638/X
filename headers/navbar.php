<nav class="navbar navbar-expand-lg fixed-top bg-body clean-navbar navbar-light">
    <div class="container"><a class="navbar-brand logo" href="#"><img src="assets/img/canvas.png" style="width: 100px;"></a><button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navcol-1"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navcol-1">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Acceuil</a></li>
                <li class="nav-item"><a class="nav-link" href="posts.php">Posts</a></li>
                <?php
                    if (isset($dir))
                    {
                        if (isset($_SESSION["isAdmin"]))
                        {
                            echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"" . $dir . "admin.php\">Admin</a></li>";
                            echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"" . $dir . "postsReports.php\">Reports</a></li>";
                        }
                        if (!isset($_SESSION["email"]))
                        {
                            echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"" . $dir . "login.php\">Connexion</a></li>";
                            echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"" . $dir . "registration.php\">Inscription</a></li>";
                        }
                        else {
                            echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"" . $dir . "inbox.php\">Inbox</a></li>";
                            echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"" . $dir . "newPost.php\">Nouveau post</a></li>";
                            echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"" . $dir . "editSettings.php\">Settings</a></li>";
                            echo "<li class=\"nav-item\"><a class=\"nav-link\" href=\"" . $dir . "logout.php\">Déconnexion</a></li>";
                        }
                    }
                ?>
            </ul>
        </div>
    </div>
</nav>