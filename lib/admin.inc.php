<?php
    require "./lib/functions.inc.php";

    function deleteuser($email)
    {
        static $ps = null;
        $sql = "DELETE FROM users WHERE email = :EMAIL;";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }

    function updateUserActivity($email, $isActive)
    {
        static $ps = null;
        $sql = 'UPDATE users SET isActive = :IS_ACTIVE WHERE email = :EMAIL;';
        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':IS_ACTIVE', $isActive, PDO::PARAM_BOOL);
            $ps->bindParam(':EMAIL', $email, PDO::PARAM_STR);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }

    function getReportedPosts()
    {
        static $ps = null;
        $sql = 'SELECT * FROM posts JOIN users ON posts.email = users.email WHERE reportNb > :MIN_REPORT_NB ORDER BY reportNb DESC';
        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $min = 0;
            $ps->bindParam(':MIN_REPORT_NB', $min, PDO::PARAM_INT);
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

    function deletePost($idPost)
    {
        static $ps = null;
        $sql = "DELETE FROM posts WHERE idPosts = :ID_POSTS;";
        if ($ps == null) 
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':ID_POSTS', $idPost, PDO::PARAM_STR);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }

    function hidePost($idPost)
    {
        static $ps = null;
        $sql = 'UPDATE posts SET hiddenPost = 1 WHERE idPost = :ID_POST;';
        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':ID_POST', $idPost, PDO::PARAM_STR);
            $answer = $ps->execute();
        } 
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $answer;
    }

?>