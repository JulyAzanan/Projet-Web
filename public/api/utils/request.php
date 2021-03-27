<?php

function get_DELETE()
{
    parse_str(file_get_contents('php://input'), $_DELETE);
    return $_DELETE;
}

function get_PATCH()
{
    parse_str(file_get_contents('php://input'), $_PATCH);
    return $_PATCH;
}
