<?php

require_once "utils/cors.php";
require_once "database/user.php";
require_once "utils/auth.php";
require_once "utils/error.php";
require_once "utils/request.php";

switch ($_SERVER['REQUEST_METHOD']) {
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
        return;
        $ok = \User\add($data->user, $data->password, $data->email, $data->age);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "DELETE":
        $_DELETE = get_DELETE();
        $user = auth();
        $ok = \User\remove($_DELETE['user'], $user);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "PATCH":
        $_PATCH = get_PATCH();
        $user = auth();
        $ok = \User\update($_PATCH['user'], $_PATCH['password'], $_PATCH['email'], intval($_PATCH['age']));
        if (!$ok) {
            PDO_error();
        }
        break;

    case "GET":
        if(isset($_GET['q'])) {
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
