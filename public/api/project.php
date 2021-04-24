<?php

require_once "utils/cors.php";
require_once "database/project.php";
require_once "utils/auth.php";
require_once "utils/error.php";
require_once "utils/request.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (isset($_GET['q'])) {
            switch ($_GET['q']) {
                case 'fetchAll':
                    $user = auth();
                    echo json_encode(\Project\fetchAll($_GET['first'], $_GET['after'], "", $user));
                    break;

                case 'fetchAllFromUser':
                    $user = auth();
                    echo json_encode(\Project\fetchAllFromUser($_GET['first'], $_GET['after'], $_GET['user'], "", $user));
                    break;

                case 'count':
                    $user = auth();
                    echo json_encode(\Project\count($user));
                    break;

                case 'countFromUser':
                    $user = auth();
                    echo json_encode(\Project\countFromUser($_GET['user'], $user));
                    break;

                case 'find':
                    $user = auth();
                    echo json_encode(\Project\find($_GET['user'], $_GET['project'], $user));
                    break;

                case 'seek':
                    $user = auth();
                    echo json_encode(\Project\seek($_GET['first'], $_GET['after'], $_GET['project'], $user));
                    break;

                case 'seekFromAuthor':
                    $user = auth();
                    echo json_encode(\Project\seekFromAuthor($_GET['first'], $_GET['after'], $_GET['user'], $_GET['project'], $user));
                    break;

                default:
                    query_error();
            }
            break;
        }

    case "POST":
        $data = get_JSON();
        $user = auth();
        if (\Project\find($data->user, $data->project, $user) != null) {
            http_response_code(400);
            echo "<h1> 400 </h1><br>";
            echo "Project already exists";
            die;
        }
        $ok = \Project\add($data->user, $data->project, $data->private, $data->description, $user);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "PATCH":
        $data = get_JSON();
        $user = auth();
        $ok = \Project\update($data->user, $data->project, $data->private, $data->description, $data->mainBranchName, $user);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "DELETE":
        $data = get_JSON();
        $user = auth();
        $ok = \Project\remove($data->user, $data->project, $user);
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
