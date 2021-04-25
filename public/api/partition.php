<?php

require_once __DIR__ . "/utils/cors.php";
require_once __DIR__ . "/database/partition.php";
require_once __DIR__ . "/utils/auth.php";
require_once __DIR__ . "/utils/error.php";
require_once __DIR__ . "/utils/request.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (isset($_GET['q'])) {
            switch ($_GET['q']) {
                case 'count':
                    $user = auth();
                    echo json_encode(\Partition\count($_GET['user'], $_GET['project'], $_GET['branch'], $_GET['commit'], $user));
                    break;

                default:
                    query_error();
            }
            break;
        }

    case "OPTIONS":
        preflight();
        break;

    default:
        request_error();
}
