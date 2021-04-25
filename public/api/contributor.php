<?php

require_once __DIR__ . "/utils/cors.php";
require_once __DIR__ . "/database/contributor.php";
require_once __DIR__ . "/utils/auth.php";
require_once __DIR__ . "/utils/error.php";
require_once __DIR__ . "/utils/request.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (isset($_GET['q'])) {
            switch ($_GET['q']) {
                case 'fetchAll':
                    $user = auth();
                    echo json_encode(\Contributor\fetchAll($_GET['user'], $_GET['project'], $user));
                    break;

                case 'count':
                    $user = auth();
                    echo json_encode(\Contributor\count($_GET['user'], $_GET['project'], $user));
                    break;

                case 'find':
                    $user = auth();
                    echo json_encode(\Contributor\find($_GET['contributor'], $_GET['user'], $_GET['project'], $user));
                    break;

                default:
                    query_error();
            }
            break;
        }

    case "POST":
        $data = get_JSON();
        $user = auth();
        if (\Contributor\find($data->user, $data->project, $data->contributor, $user) != null) {
            http_response_code(400);
            echo "<h1> 400 </h1><br>";
            echo "Contributor already exists";
            die;
        }
        $ok = \Contributor\add($data->contributor, $data->user, $data->project, $user);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "DELETE":
        $data = get_JSON();
        $user = auth();
        $ok = \Contributor\remove($data->contributor, $data->user, $data->project, $user);
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
