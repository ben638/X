<?php
    require "../lib/apiFunctions.inc.php";
    if (isset($_SERVER["HTTP_XAPITOKEN"])) {
        $token = $_SERVER["HTTP_XAPITOKEN"];
    }
    else {
        responseCode(401);
    }
    $email = getEmailFromToken($token);
    if (!$email)
    {
        responseCode(401);
    }
    $isAdmin = false;
    if (checkIsAdmin($email))
    {
        $isAdmin = true;
    }
    $method = filter_var($_SERVER['REQUEST_METHOD']);
    //Requètes GET de l'api
    if ($method == 'GET')
    {
        if (isset($_GET['messages']))
        {
            if ($isAdmin)
            {
                $ids = getAllMessagesIds();
            }
            else {
                $ids = getReceivedMessages($email);
            }
            returnResponse($ids);
        }
        else if (isset($_GET['message']))
        {
            if ($isAdmin)
            {
                $message = getAllMessages();
            }
            else {
                if (isset($_GET['id']))
                {
                    $idMessage = htmlspecialchars($_GET["id"]);
                    $message = getMessage($idMessage);
                }
            }
            if ($message)
            {
                returnResponse($message);
            }
            else {
                responseCode(404);
            }
        }
        else {
            responseCode(400);
        }
    }
    //Requète POST de l'api
    else if ($method == 'POST') {
        $params = getParams();
        if (isset($params["to"]) && isset($params["message"]))
        {
            $isSend = sendMessage($email, $params["to"], $params["message"]);
            if (!$isSend)
            {
                responseCode(500);
            }
        }
        else {
            responseCode(400);
        }
    }
    else {
        responseCode(405);
    }

    function responseCode($code)
    {
        if ($code == 400)
        {
            header('HTTP/1.0 400 Bad Request');
        }
        else if ($code == 401)
        {
            header('HTTP/1.0 401 Unauthorized');
        }
        else if ($code == 403)
        {
            header('HTTP/1.0 403 Forbidden');
        }
        else if ($code == 404)
        {
            header('HTTP/1.0 404 Not Found');
        }
        else if ($code == 405)
        {
            header('HTTP/1.0 405 Method Not Allowed');
        }
        else {
            header('HTTP/1.0 500 Internal Server Error');
        }
        exit;
    }

    //Retourne une réponse au format JSON
    function returnResponse($response) {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json");
        echo json_encode($response);
    }

    //Récupère les paramètre du body
    function getParams() {
        $file = file_get_contents("php://input", true);
        return json_decode($file, true);
    }
?>