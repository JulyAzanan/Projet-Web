<?php

require_once "../database/musician.php";

function auth($variables) {
    if (\User\auth($variables['login'], $variables['password']))  return $name;
    header("HTTP/1.1 401 Unauthorized");
    echo "<h1> 401 Incorrect Credentials </h1>";
    die;
}