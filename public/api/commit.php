<?php

require_once __DIR__ . "/utils/cors.php";
require_once __DIR__ . "/database/commit.php";
require_once __DIR__ . "/utils/auth.php";
require_once __DIR__ . "/utils/error.php";
require_once __DIR__ . "/utils/request.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (isset($_GET['q'])) {
            switch ($_GET['q']) {
                case 'fetchAll':
                    $user = auth();
                    echo json_encode(\Commit\fetchAll($_GET['user'], $_GET['project'], $_GET['branch'], $user));
                    break;

                case 'count':
                    $user = auth();
                    echo json_encode(\Commit\count($_GET['user'], $_GET['project'], $_GET['branch'], $user));
                    break;

                case 'find':
                    $user = auth();
                    echo json_encode(\Commit\find($_GET['user'], $_GET['project'], $_GET['branch'], $_GET['commit'], $user));
                    break;

                case 'getCommit':
                    $user = auth();
                    echo json_encode(\Commit\getCommit($_GET['user'], $_GET['project'], $_GET['branch'], $_GET['commit'], $user));
                    break;

                default:
                    query_error();
            }
            break;
        }

    case "POST":
        $data = get_JSON();
        $user = auth();
        $ok = \Commit\add($data->user, $data->project, $data->branch, $data->message, $data->partitions, $user);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "OPTIONS":
        preflight();
        break;

    default:
        request_error();
}
