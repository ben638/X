<?php 
    $dir = "./";
    session_start();
    require $dir . "lib/functions.inc.php";
    redirection(isset($_SESSION["email"]), false, true, $dir);
    $dest;
    if (isset($_GET["to"]))
    {
        $dest = filter_input(INPUT_GET, "to", FILTER_SANITIZE_EMAIL);
    }
    else if (isset($_POST["message"]) && isset($_POST["dest"]))
    {
        $message = $_POST['message'];
        $dest = htmlspecialchars($_POST['dest']);
        $idConv = getIdConversation($_SESSION["email"], $dest);
        newMessage($_SESSION["email"], $message, $idConv);
    }
    else {
        header('Location: inbox.php');
        exit(0);
    }
    $messages = getMessagesFrom($_SESSION["email"], $dest);
    $info = getUserInfo($dest);
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>MP - X</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i,600,600i&amp;display=swap">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/baguetteBox.min.css">
    <link rel="stylesheet" href="assets/css/Features-Minimal-icons.css">
    <link rel="stylesheet" href="assets/css/Gamanet_Btn_icon_primary_dest_bs5.css">
    <link rel="stylesheet" href="assets/css/Simple-Bootstrap-Chat.css">
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
                    <h2 class="text-info"><?php echo htmlspecialchars($info["firstname"]) . " " . htmlspecialchars($info["lastname"]) . "@" . htmlspecialchars($info["username"]); ?></h2>
                </div>
                <div class="block-content">

 
    <!-- Chat Box-->
    <div class="col-12 px-0">
      <div class="px-4 py-5 chat-box bg-white">
        <?php
            $nbMessages = count($messages);
            for ($i = 0; $i < $nbMessages; $i++)
            {
                if ($messages[$i]["auteur"] == $_SESSION["email"])
                {
                    ?>
                        <!-- Reciever Message-->
                        <div class="media w-50 ml-auto mb-3">
                            <div class="media-body">
                                <div class="bg-primary rounded py-2 px-3 mb-2">
                                    <p class="text-small mb-0 text-white"><?php echo htmlspecialchars($messages[$i]["message"]); ?></p>
                                </div>
                                <p class="small text-muted"><?php echo htmlspecialchars($messages[$i]["sendDate"]); ?></p>
                            </div>
                        </div>
                    <?php
                }
                else {
                    ?>
                        <!-- Sender Message-->
                        <div class="media w-50 mb-3"><img src="https://res.cloudinary.com/mhmd/image/upload/v1564960395/avatar_usae7z.svg" alt="user" width="50" class="rounded-circle">
                            <div class="media-body ml-3">
                                <div class="bg-light rounded py-2 px-3 mb-2">
                                    <p class="text-small mb-0 text-muted"><?php echo htmlspecialchars($messages[$i]["message"]); ?></p>
                                </div>
                                <p class="small text-muted"><?php echo htmlspecialchars($messages[$i]["sendDate"]); ?></p>
                            </div>
                        </div>
                    <?php
                }
            }
        ?>

      <!-- Typing area -->
      <form action="mp.php" method="post" class="bg-light">
        <div class="input-group">
          <input type="text" placeholder="Type a message" aria-describedby="button-addon2" class="form-control rounded-0 border-0 py-4 bg-light" name="message">
          <input type="hidden" name="dest" value="<?php echo htmlspecialchars($dest); ?>">
          <div class="input-group-append">
            <button id="button-addon2" type="submit" class="btn btn-link"> <i class="fa fa-paper-plane"></i></button>
          </div>
        </div>
      </form>

    </div>
  </div>
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