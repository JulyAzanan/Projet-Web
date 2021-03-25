<?php
include_once "./database/user.php";

// $res = add_user("tes", "passwd", NULL, NULL);
// echo $res;

// var_dump($_GET['q']);
// echo "|";
// var_dump($_POST['q']);

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
