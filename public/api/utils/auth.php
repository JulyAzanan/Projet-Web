<?php

require_once __DIR__ . "/../database/user.php";
require_once __DIR__ . "/error.php";

function auth()
{
    if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
        if (\User\auth($_SERVER["PHP_AUTH_USER"], $_SERVER["PHP_AUTH_PW"])) {
            return $_SERVER["PHP_AUTH_USER"];
        }
        unauthorized_error();
    }
    if (isset($_SERVER["HTTP_AUTHORIZATION"]) && 0 === stripos($_SERVER["HTTP_AUTHORIZATION"], 'basic ')) {
        $exploded = explode(':', base64_decode(substr($_SERVER["HTTP_AUTHORIZATION"], 6)), 2);
        if (2 == \count($exploded)) {
            list($user, $password) = $exploded;
            if (\User\auth($user, $password)) {
                return $user;
            }
        }
        unauthorized_error();
    }
    return null;
}
