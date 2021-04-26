<?php

require_once __DIR__ . "/utils/cors.php";
require_once __DIR__ . "/database/branch.php";
require_once __DIR__ . "/utils/auth.php";
require_once __DIR__ . "/utils/error.php";
require_once __DIR__ . "/utils/request.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (isset($_GET['q'])) {
            switch ($_GET['q']) {
                case 'fetchAll':
                    $user = auth();
                    echo json_encode(\Branch\fetchAll($_GET['user'], $_GET['project'], $user));
                    break;

                case 'count':
                    $user = auth();
                    echo json_encode(\Branch\count($_GET['user'], $_GET['project'], $user));
                    break;

                case 'find':
                    $user = auth();
                    echo json_encode(\Branch\find($_GET['user'], $_GET['project'], $_GET['branch'], $user) !== null);
                    break;

                case 'getBranch':
                    $user = auth();
                    echo json_encode(\Branch\getBranch($_GET['user'], $_GET['project'], $_GET['branch'], $user));
                    break;

                default:
                    query_error();
            }
            break;
        }

    case "POST":
        $data = get_JSON();
        $user = auth();
        if (\Branch\find($data->user, $data->project, $data->branch, $user) != null) {
            http_response_code(400);
            echo "<h1> 400 </h1><br>";
            echo "Branch already exists";
            die;
        }
        $ok = \Branch\add($data->user, $data->project, $data->branch, $user);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "PATCH":
        $data = get_JSON();
        $user = auth();
        $ok = \Branch\rename($data->user, $data->project, $data->branch, $data->new_name, $user);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "DELETE":
        $data = get_JSON();
        $user = auth();
        $ok = \Branch\remove($data->user, $data->project, $data->branch, $user);
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
