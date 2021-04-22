<?php

require_once __DIR__ . "/../database/user.php";

function auth()
{
    if (isset($_SERVER["HTTP_AUTHORIZATION"]) && 0 === stripos($_SERVER["HTTP_AUTHORIZATION"], 'basic ')) {
        $exploded = explode(':', base64_decode(substr($_SERVER["HTTP_AUTHORIZATION"], 6)), 2);
        if (2 == \count($exploded)) {
            list($user, $password) = $exploded;
            if (\User\auth($user, $password)) {
                return $user;
            }
        }
    }
    header("HTTP/1.1 401 Unauthorized");
    echo "<h1> 401 Incorrect Credentials </h1>";
    die;
}
