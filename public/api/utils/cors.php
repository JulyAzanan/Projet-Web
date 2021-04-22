<?php

header('Access-Control-Allow-Origin: *');

function preflight() {
    http_response_code(204);
    header('Access-Control-Allow-Headers: authorization');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PATCH');
    header('Access-Control-Max-Age: 86400');
    die;
}
