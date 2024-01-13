<?php
    if (basename(getcwd()) == "api")
    {
        require "../lib/dbConnect.inc.php";
    }
    else {
        require "./lib/dbConnect.inc.php";
    }
    //Fonctions SQL

    function createUser($email, $username, $firstname, $lastname, $birthdate, $bio, $password, $idMedia)
    {
        $answer = false;
        if (!checkUserExists($email))
        {
            static $ps = null;
            $sql = "INSERT INTO users (`email`, `username`, `firstname`, `lastname`, `birthdate`, `password`, `bio`, `idMedia`, `isActive`) VALUES (:EMAIL, :USERNAME, :FIRSTNAME, :LASTNAME, :BIRTHDATE, :PASS, :BIO, :ID_MEDIA, 1);";
            if ($ps == null) 
            {
                $ps = dbConnect()->prepare($sql);
            }
            try {
                $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
                $ps->bindParam(':USERNAME', $username, PDO::PARAM_STR);
                $ps->bindParam(':FIRSTNAME', $firstname, PDO::PARAM_STR);
                $ps->bindParam(':LASTNAME', $lastname, PDO::PARAM_STR);
                $ps->bindParam(':BIRTHDATE', $birthdate, PDO::PARAM_STR);
                $ps->bindParam(':PASS', $password, PDO::PARAM_STR);
                $ps->bindParam(':BIO', $bio, PDO::PARAM_STR);
                $ps->bindParam(':ID_MEDIA', $idMedia, PDO::PARAM_STR);
                $answer = $ps->execute();
            } 
            catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $answer;
    }

    function createAvatar($type, $name)
    {
        $answer = false;
        static $ps = null;
        $creationDate = date('Y-m-d H:i:s');
        $sql = "INSERT INTO medias (`type`, `name`, `creationDate`) VALUES (:TYPE, :NAME, :CREATION_DATE);";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        try {
            $ps->bindParam(':TYPE', $type, PDO::PARAM_STR);
            $ps->bindParam(':NAME', $name, PDO::PARAM_STR);
            $ps->bindParam(':CREATION_DATE', $creationDate, PDO::PARAM_STR);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return $answer;
    }

    function getIdMedia($name)
    {
        static $ps = null;
        $sql = "SELECT `idMedia` FROM medias WHERE name = :NAME;";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':NAME', $name, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer[0]["idMedia"];
    }

    function checkUserExists($email)
    {
        static $ps = null;
        $sql = "SELECT * FROM users WHERE email = :EMAIL;";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }

    function getUserInfo($email)
    {
        static $ps = null;
        $sql = 'SELECT * FROM users WHERE email = :EMAIL;';

        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer[0];
    }

    function updatePassword($email, $password)
    {
        static $ps = null;
        $sql = "UPDATE users SET password = :PASS WHERE email = :EMAIL;";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            $ps->bindParam(':PASS', $password, PDO::PARAM_STR);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }

    function newPost($message, $hasMedia, $files, $email, $url)
    {
        static $ps = null;
        $creationDate = date('Y-m-d H:i:s');
        $sql = "INSERT INTO `posts` (`message`, `email`, `creationDate`, `reportNb`, `hiddenPost`) VALUES (:MESSAGE, :EMAIL, :CREATION_DATE, :NB_REPORT, :HIDDEN_POST);";
        if ($ps == null) {
            $pdo = dbConnect();
            $pdo->beginTransaction();
            $ps = $pdo->prepare($sql);
        }
        $answer = false;
        try {
            $nbReport = 0;
            $hiddenPost = 0;
            $ps->bindParam(':MESSAGE', $message, PDO::PARAM_STR);
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            $ps->bindParam(':CREATION_DATE', $creationDate, PDO::PARAM_STR);
            $ps->bindParam(':NB_REPORT', $nbReport, PDO::PARAM_STR);
            $ps->bindParam(':HIDDEN_POST', $hiddenPost, PDO::PARAM_STR);
        
            $answer = $ps->execute();
            if ($hasMedia)
            {
                $idPost = getIdPost($message, $creationDate, $email)[0]["idPost"];
                for ($i=0; $i < count($files["name"]); $i++) 
                { 
                    createMedia($pdo, $files["type"][$i], $files["name"][$i], $creationDate);
                    $idMedia = getIdMedia($files["name"][$i]);
                    createLiaisonMediaPost($idPost, $idMedia);
                }
            }
            else if ($url)
            {
                $idPost = getIdPost($message, $creationDate, $email)[0]["idPost"];
                createMedia($pdo, "inconnu", $url, $creationDate);
                $idMedia = getIdMedia($url);
                createLiaisonMediaPost($idPost, $idMedia);
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo $e->getMessage();
            exit();
        }
        try {
            if (!saveMedias($files))
            {
                throw new Exception("Problème d'upload des médias");
            }
        }
        catch (Exception $e) 
        {
            $pdo->rollBack();
        }
        $pdo->commit();
        return $answer;
    }

    function createLiaisonMediaPost($idPost, $idMedia)
    {
        $answer = false;
        static $ps = null;
        $sql = "INSERT INTO mediasPost (`idPost`, `idMedia`) VALUES (:ID_POST, :ID_MEDIA);";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        try {
            $ps->bindParam(':ID_POST', $idPost, PDO::PARAM_INT);
            $ps->bindParam(':ID_MEDIA', $idMedia, PDO::PARAM_INT);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return $answer;
    }

    function saveMedias($files)
    {
        $nbMedias = count($files["name"]);
        $nbMediasMoved = 0;
        for ($i=0; $i < count($files["name"]); $i++) 
        {
            $hasMoveMedia = false;
            $hasMoveMedia = move_uploaded_file($files['tmp_name'][$i], DOSSIER . $files["name"][$i]);
            if ($hasMoveMedia)
            {
                $nbMediasMoved++;
            }
        }
        if ($nbMediasMoved != $nbMedias)
        {
            return false;
        }
        else {
            return $files;
        }
    }

    function getIdPost($message, $creationDate, $email) 
    {
        static $ps = null;
        $sql = 'SELECT idPost FROM posts WHERE message = :MESSAGE AND creationDate = :CREATION_DATE AND email = :EMAIL;';

        $answer = false;
        try {
            if ($ps == null) 
            {
                $ps = dbConnect()->prepare($sql);
            }
            
            $ps->bindParam(':MESSAGE', $message, PDO::PARAM_STR);
            $ps->bindParam(':CREATION_DATE', $creationDate, PDO::PARAM_STR);
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
      
        return $answer;
    }

    function createMedia($pdo, $type, $name, $creationDate)
    {
        static $ps = null;
        $sql = "INSERT INTO `medias` (`type`, `name`, `creationDate`) VALUES (:TYPE, :NAME, :CREATION_DATE);";
        
        if ($ps == null) {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':TYPE', $type, PDO::PARAM_STR);
            $ps->bindParam(':NAME', $name, PDO::PARAM_STR);
            $ps->bindParam(':CREATION_DATE', $creationDate, PDO::PARAM_STR);
            
            $answer = $ps->execute();
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo $e->getMessage();
            exit();
        }
        return $answer;
    }

    function renameMedias($files)
    {
        for ($i=0; $i < count($files["name"]); $i++) 
        {
            $fichier = checkFile($files["type"][$i]) . "_" . date('Y-m-d-H-i-s') . "_" . uniqid() . "." . substr($files["type"][$i], 6);
            $files["name"][$i] = $fichier;
        }
        return $files;
    }

    function checkFile($typeFile)
    {
        $str = substr($typeFile, 0, 5);
        if ($str != "image" && $str != "video" && $str!= "audio")
        {
            echo "<p>Problème lors de l'importation des fichiers</p>";
            exit(0);
        }
        return $str;
    }

    function getProfil($email)
    {
        return checkUserExists($email);
    }

    function getAllProfil($email)
    {
        static $ps = null;
        $sql = 'SELECT * FROM medias JOIN users ON medias.idMedia = users.idMedia WHERE users.email = :EMAIL;';

        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }

    function getAllProfils()
    {
        static $ps = null;
        $sql = 'SELECT * FROM medias JOIN users ON medias.idMedia = users.idMedia;';

        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }
    
    function getAllPosts($email)
    {
        static $ps = null;
        $sql = 'SELECT * FROM posts WHERE email = :EMAIL ORDER BY creationDate DESC;';
        
        if ($ps == null) {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $answer;
    }

    function getPostsFromAllUsers()
    {
        static $ps = null;
        $sql = 'SELECT * FROM posts JOIN users ON posts.email = users.email WHERE posts.hiddenPost = 0 ORDER BY creationDate DESC;';

        if ($ps == null) {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $answer;
    }

    function getMedias($idPost) 
    {
        static $ps = null;
        $sql = 'SELECT * FROM medias JOIN mediasPost ON medias.idMedia = mediasPost.idMedia WHERE mediasPost.idPost = :ID_POST;';

        if ($ps == null) {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':ID_POST', $idPost, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $answer;
    }

    function updateProfil($oldEmail, $newEmail, $username, $firstname, $lastname, $birthdate, $password, $bio, $idMedia)
    {
        $answer = false;
        if (!checkUserExists($newEmail) || $oldEmail == $newEmail)
        {
            static $ps = null;
            $sql = "UPDATE users SET email = :NEW_EMAIL, username = :USERNAME, firstname = :FIRSTNAME, lastname = :LASTNAME, birthdate = :BIRTHDATE, password = :PASS, bio = :BIO, idMedia = :ID_MEDIA WHERE email = :OLD_EMAIL;";
            if ($ps == null) 
            {
                $ps = dbConnect()->prepare($sql);
            }
            try {
                $ps->bindParam(':NEW_EMAIL', $newEmail, PDO::PARAM_STR);
                $ps->bindParam(':USERNAME', $username, PDO::PARAM_STR);
                $ps->bindParam(':FIRSTNAME', $firstname, PDO::PARAM_STR);
                $ps->bindParam(':LASTNAME', $lastname, PDO::PARAM_STR);
                $ps->bindParam(':BIRTHDATE', $birthdate, PDO::PARAM_STR);
                $ps->bindParam(':PASS', $password, PDO::PARAM_STR);
                $ps->bindParam(':BIO', $bio, PDO::PARAM_STR);
                $ps->bindParam(':ID_MEDIA', $idMedia, PDO::PARAM_STR);
                $ps->bindParam(':OLD_EMAIL', $oldEmail, PDO::PARAM_STR);

                $answer = $ps->execute();
            } 
            catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $answer;
    }

    function newConversation($userMail, $to)
    {
        $answer = false;
        static $ps = null;
        $sql = "INSERT INTO `conversations` (`from`, `to`) VALUES (:FROM, :TO);";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        try {
            $ps->bindParam(':FROM', $userMail, PDO::PARAM_STR);
            $ps->bindParam(':TO', $to, PDO::PARAM_STR);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return $answer;
    }

    function newMessage($auteur, $message, $idConversation)
    {
        $answer = false;
        static $ps = null;
        $creationDate = date('Y-m-d H:i:s');
        $sql = "INSERT INTO `messages` (`sendDate`, `message`, `idConversation`, `auteur`) VALUES (:SEND_DATE, :MESSAGE, :ID_CONV, :AUTEUR);";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        try {
            $ps->bindParam(':SEND_DATE', $creationDate, PDO::PARAM_STR);
            $ps->bindParam(':MESSAGE', $message, PDO::PARAM_STR);
            $ps->bindParam(':ID_CONV', $idConversation, PDO::PARAM_STR);
            $ps->bindParam(':AUTEUR', $auteur, PDO::PARAM_STR);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        
        return $answer;
    }

    function getIdConversation($userMail, $email)
    {
        static $ps = null;
        $sql = "SELECT `idConversation` FROM conversations WHERE (conversations.from = :USER_MAIL AND conversations.to = :EMAIL) OR (conversations.to = :USER_MAIL AND conversations.from = :EMAIL);";
        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':USER_MAIL', $userMail, PDO::PARAM_STR);
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer[0]["idConversation"];
    }

    function getConversations($userMail)
    {
        static $ps = null;
        $sql = 'SELECT * FROM conversations JOIN users ON conversations.to = users.email JOIN medias ON users.idMedia = medias.idMedia WHERE conversations.from = :EMAIL OR conversations.to = :EMAIL;';
        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':EMAIL', $userMail, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        $len = count($answer);
        for ($i = 0; $i < $len; $i++)
        {
            if ($answer[$i]["email"] == $userMail)
            {
                $idConversation = $answer[$i]["idConversation"];
                $sql = 'SELECT * FROM conversations JOIN users ON conversations.from = users.email JOIN medias ON users.idMedia = medias.idMedia WHERE conversations.idConversation = :ID;';
                $ps = dbConnect()->prepare($sql);
                $answer[$i] = false;
                try {
                    $ps->bindParam(':ID', $idConversation, PDO::PARAM_STR);
                    if ($ps->execute())
                    {
                        $answer[$i] = $ps->fetchAll(PDO::FETCH_ASSOC)[0];
                    }
                } 
                catch (PDOException $e) {
                    echo $e->getMessage();
                }   
            }
        }
        return $answer;
    }

    function searchConversation($userMail, $email)
    {
        static $ps = null;
        $sql = 'SELECT * FROM conversations WHERE (conversations.from = :USER_MAIL AND conversations.to = :EMAIL) OR (conversations.to = :USER_MAIL AND conversations.from = :EMAIL);';
        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            $ps->bindParam(':USER_MAIL', $userMail, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }

    function getMail($username)
    {
        static $ps = null;
        $sql = 'SELECT `email` FROM users WHERE username = :USERNAME;';
        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':USERNAME', $username, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer[0]["email"];
    }

    function getMessagesFrom($userMail, $email)
    {
        static $ps = null;
        $sql = 'SELECT * FROM messages JOIN conversations ON messages.idConversation = conversations.idConversation WHERE (conversations.from = :USER_MAIL AND conversations.to = :EMAIL) OR (conversations.to = :USER_MAIL AND conversations.from = :EMAIL);';
        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':USER_MAIL', $userMail, PDO::PARAM_STR);
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }

    function getToken($email)
    {
        static $ps = null;
        $sql = 'SELECT token FROM users WHERE email = :EMAIL;';
        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer[0]["token"];
    }

    function newToken($email)
    {
        static $ps = null;
        $sql = "UPDATE users SET token = :TOKEN WHERE email = :EMAIL;";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $token = bin2hex(random_bytes(200));
            $len = strlen($token);
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            $ps->bindParam(':TOKEN', $token, PDO::PARAM_STR);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        if ($answer)
        {
            return $token;
        }
        else {
            return $answer;
        }
    }

    function getNbReport($idPost)
    {
        static $ps = null;
        $sql = 'SELECT reportNb FROM posts WHERE idPost = :ID_POST';
        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':ID_POST', $idPost, PDO::PARAM_STR);
            if ($ps->execute())
            {
                $answer = $ps->fetchAll(PDO::FETCH_ASSOC);
            }
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer[0]["reportNb"];
    }

    function reportPost($idPost)
    {
        static $ps = null;
        $sql = "UPDATE posts SET reportNb = :NB_REPORT WHERE idPost = :ID_POST;";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $nbReport = getNbReport($idPost);
            $nbReport++;
            $ps->bindParam(':ID_POST', $idPost, PDO::PARAM_STR);
            $ps->bindParam(':NB_REPORT', $nbReport, PDO::PARAM_STR);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }


    //Fonctions d'accès aux pages

    function session_expiration($dir)
    {
        if (isset($_SESSION["lastLogin"]))
        {
            $duree = time() - $_SESSION["lastLogin"];
            if ($duree > 2000000)
            {
                header("Location: " . $dir . "logout.php");
                exit(0);
            }
        }
    }

    function redirection($isConnected, $hiddenPageIfConnected, $hiddenPageIfNotConnected, $dir)
    {
        if ($isConnected)
        {
            session_expiration($dir);
            if ($hiddenPageIfConnected)
            {
                error_log("Tried to access a page : user is cannot access this page.", 0);
                header("Location: " . $dir . "index.php");
                exit(0);
            }
        }
        else {
            if ($hiddenPageIfNotConnected)
            {
                error_log("Tried to access a page : user is not connected.", 0);
                header("Location: " . $dir . "index.php");
                exit(0);
            }
        }
    }
?>