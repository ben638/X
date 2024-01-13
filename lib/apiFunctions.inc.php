<?php
    require "../lib/functions.inc.php";

    function getReceivedMessages($userMail)
    {
        $conversations = getConversations($userMail);
        $idReceivedMessages = false;
        $nbConv = count($conversations);
        $cptId = 0;
        for ($i = 0; $i < $nbConv; $i++)
        {
            $messages = getMessagesFrom($userMail, $conversations[$i]["email"]);
            for ($j = 0; $j < count($messages); $j++)
            {
                if ($messages[$j]["auteur"] != $userMail)
                {
                    $idReceivedMessages[$cptId] = $messages[$j]["idMessage"];
                    $cptId++;
                }
            }
        }
        return $idReceivedMessages;
    }

    function getMessage($idMessage)
    {
        static $ps = null;
        $sql = 'SELECT * FROM messages WHERE idMessage = :ID_MESSAGE;';

        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':ID_MESSAGE', $idMessage, PDO::PARAM_STR);
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

    function sendMessage($userMail, $to, $message)
    {
        $idConv = getIdConversation($userMail, $to);
        if (!$idConv)
        {
            newConversation($userMail, $to);
            $idConv = getIdConversation($userMail, $to);
        }
        $isSend = newMessage($userMail, $message, $idConv);
        return $isSend;
    }

    function getAllMessages()
    {
        static $ps = null;
        $sql = 'SELECT * FROM messages;';

        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':ID_MESSAGE', $idMessage, PDO::PARAM_STR);
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

    function getAllMessagesIds()
    {
        $ids = getAllMessages();
        $len = count($ids);
        for ($i = 0; $i < $len; $i++)
        {
            $ids[$i] = $ids[$i]["idMessage"];
        }
        return $ids;
    }

    function getEmailFromToken($token)
    {
        static $ps = null;
        $sql = 'SELECT email FROM users WHERE token = :TOKEN;';

        if ($ps == null)
        {
            $ps = dbConnect()->prepare($sql);
        }
        $answer = false;
        try {
            $ps->bindParam(':TOKEN', $token, PDO::PARAM_STR);
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

    function checkIsAdmin($email)
    {
        static $ps = null;
        $sql = 'SELECT isAdmin FROM users WHERE email = :EMAIL;';

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
        return $answer[0]["isAdmin"];
    }
?>