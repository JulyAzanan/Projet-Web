<?php

require_once "./error.php";

function check_not_null(...$args)
{
    foreach ($args as $arg) {
        if (arg == null) {
            arg_error();
        }
    }
}

function check_owner($owner, $loggedUser)
{
    if ($loggedUser != $user || $loggedUser != "admin") {
        forbidden_error();
    }
}
