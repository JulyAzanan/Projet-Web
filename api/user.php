<?php

require_once "./database/musician.php";
require_once "./utils/auth.php";
require_once "./utils/error.php";
require_once "./utils/request.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case "POST":
        if (\User\find($_POST['username']) != null) {
            http_response_code(400);
            echo "<h1> 400 </h1><br>";
            echo "User already exists";
            die;
        }
        if (\User\findByEmail($_POST['email']) != null) {
            http_response_code(400);
            echo "<h1> 400 </h1><br>";
            echo "Email already exists";
            die;
        }

        $ok = \User\add($_POST['username'], $_POST['password'], $_POST['email'], $_POST['age']);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "DELETE":
        $_DELETE = get_DELETE();
        $user = auth($_DELETE);
        $ok = \User\remove($_DELETE['user'], $user);
        if (!$ok) {
            PDO_error();
        }
        break;

    case "PATCH":
        $_PATCH = get_PATCH();
        $user = auth($_DELETE);
        $ok = \User\update($_DELETE['user'], $_DELETE['password'], $_DELETE['email'], intval($_DELETE['age']));
        if (!$ok) {
            PDO_error();
        }
        break;

    case "GET":
        switch ($_GET['query']) {
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

    default:
        request_error();
}
