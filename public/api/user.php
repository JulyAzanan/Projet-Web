<?php

require_once "utils/cors.php";
require_once "database/user.php";
require_once "utils/auth.php";
require_once "utils/error.php";
require_once "utils/request.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (isset($_GET['q'])) {
            switch ($_GET['q']) {
                case 'login':
                    auth();
                    break;

                case 'fetchAll':
                    echo json_encode(\User\fetchAll($_GET['first'], $_GET['after']));
                    break;

                case 'count':
                    echo json_encode(\User\count());
                    break;

                case 'findByEmail':
                    echo json_encode(\User\findByEmail($_GET['email']));
                    break;

                case 'find':
                    echo json_encode(\User\find($_GET['user']));
                    break;

                case 'getUser':
                    $user = auth();
                    echo json_encode(\User\getUser($_GET['user'], $_GET['first'], $_GET['after'], $user));
                    break;

                default:
                    query_error();
            }
            break;
        }

    case "POST":
        $data = get_JSON();
        if (\User\find($data->user) != null) {
            http_response_code(400);
            echo "<h1> 400 </h1><br>";
            echo "User already exists";
            die;
        }
        if (\User\findByEmail($data->email) != null) {
            http_response_code(400);
            echo "<h1> 400 </h1><br>";
            echo "Email already exists";
            die;
        }
        $ok = \User\add($data->user, $data->password, $data->email, $data->age);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "PATCH":
        $data = get_JSON();
        $user = auth();
        $ok = \User\update($data->user, $data->password, $data->email, $data->age, $data->bio, $data->picture, $user);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "DELETE":
        $data = get_JSON();
        $user = auth();
        $ok = \User\remove($data->user, $user);
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
