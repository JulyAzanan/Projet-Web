<?php

require_once __DIR__ . "/utils/cors.php";
require_once __DIR__ . "/database/friend.php";
require_once __DIR__ . "/utils/auth.php";
require_once __DIR__ . "/utils/error.php";
require_once __DIR__ . "/utils/request.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if (isset($_GET['q'])) {
            switch ($_GET['q']) {
                case 'fetchAll':
                    echo json_encode(\Friend\fetchAll($_GET['first'], $_GET['after'], $_GET['user']));
                    break;

                case 'count':
                    echo json_encode(\Friend\count($_GET['user']));
                    break;

                case 'find':
                    echo json_encode(\Friend\find($_GET['follower'], $_GET['following']));
                    break;

                default:
                    query_error();
            }
            break;
        }

    case "POST":
        $data = get_JSON();
        $user = auth();
        if (\Friend\find($data->follower, $data->following)) {
            http_response_code(400);
            echo "<h1> 400 </h1><br>";
            echo "Friend already added";
            die;
        }
        $ok = \Friend\add($data->follower, $data->following, $user);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "DELETE":
        $data = get_JSON();
        $user = auth();
        $ok = \Friend\remove($data->follower, $data->following, $user);
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
