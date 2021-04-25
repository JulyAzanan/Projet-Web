<?php
include_once __DIR__ . "/database/user.php";

// $res = add_user("tes", "passwd", NULL, NULL);
// echo $res;

// var_dump($_GET['q']);
// echo "|";
// var_dump($_POST['q']);

echo md5(microtime());

die;

echo json_encode(42);

die;

if (isset($_SERVER["HTTP_AUTHORIZATION"]) && 0 === stripos($_SERVER["HTTP_AUTHORIZATION"], 'basic ')) {
    $exploded = explode(':', base64_decode(substr($_SERVER["HTTP_AUTHORIZATION"], 6)), 2);
    if (2 == \count($exploded)) {
        list($user, $password) = $exploded;
    }
}

echo "end";

die;

echo json_encode(\User\find("jenexistepas"));

die;

function foo(...$vars)
{
    var_dump($vars);
}

foo("test");
foo(8, 't', 7);

die;

echo "test";

if ($_GET['test'] == "") {
    echo "true";
} else {
    echo "false";
}

die;

$user = "foobar586";

\User\add($user, "password452", "foo@bar258556", null, null);

$bool1 = \User\auth($user, "password42");
// $bool2 = User::auth($user, "password6");

var_dump($bool1);
// var_dump($bool2);
