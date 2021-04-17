<?php

function PDO_error()
{
    header("HTTP/1.1 513 PDO Error");
    die;
}

function arg_error()
{
    http_response_code(400);
    echo "<h1> 400 Faulty Arguments </h1>";
    die;
}

function query_error()
{
    http_response_code(400);
    echo "<h1> 400 Unknown Query </h1>";
    die;
}

function request_error()
{
    http_response_code(400);
    echo "<h1> 400 Faulty Request Method </h1>";
    die;
}

function project_error()
{
    http_response_code(400);
    echo "<h1> 400 Unknown project </h1>";
    die;
}

function branch_error()
{
    http_response_code(400);
    echo "<h1> 400 Unknown branch </h1>";
    die;
}
function forbidden_error()
{
    http_response_code(403);
    echo "<h1> 400 Forbidden </h1>";
    die;
}
