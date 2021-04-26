<?php
namespace User;

include_once __DIR__ . "/config.php";
include_once __DIR__ . "/project.php";
include_once __DIR__ . "/friend.php";
include_once __DIR__ . "/../utils/error.php";
include_once __DIR__ . "/../utils/args.php";

/**
 * Permet de créer un nouvel utilisateur
 */
function add($user, $password, $email, $age)
{
    check_not_null($user, $password);
    //Changing from $name to $user
    if (strlen($user) <= 0 || strlen($password) <= 0) {
        arg_error();
    }
    $passwordHash = hash("sha256", $password);
    $bd = connect();
    $sql = "INSERT INTO musician VALUES (:name, :passwordHash, :email, NULL, :age, NULL, NULL)";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':name', $user, \PDO::PARAM_STR);
    $stmt->bindValue(':passwordHash', $passwordHash, \PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
    $stmt->bindValue(':age', $age, \PDO::PARAM_INT);
    return $stmt->execute();
}

/**
 * Permet de retirer l'utilisateur dont le nom est entré en argument de la base de données
 */
function remove($user, $loggedUser)
{
    check_not_null($user, $loggedUser);
    check_owner($user, $loggedUser);
    $bd = connect();
    $sql = "DELETE FROM musician WHERE name = :name";

    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':name', $user, \PDO::PARAM_STR);
    return $stmt->execute();
}

function update($user, $password, $email, $age, $bio, $picture, $loggedUser)
{
    check_not_null($user, $loggedUser);
    check_owner($user, $loggedUser);
    $bd = connect();
    if ($age != null) {
        $sql = "UPDATE musician SET age = :age WHERE name = :user";
        $stmt = $bd->prepare($sql);
        $stmt->bindValue(':user', $user, \PDO::PARAM_STR);
        $stmt->bindValue(':age', $age, \PDO::PARAM_INT);
        if (!$stmt->execute()) {
            return false;
        }
    }
    if ($password != null) {
        $passwordHash = hash("sha256", $password);
        $sql = "UPDATE musician SET passwordHash = :passwordHash WHERE name = :user";
        $stmt = $bd->prepare($sql);
        $stmt->bindValue(':user', $user, \PDO::PARAM_STR);
        $stmt->bindValue(':passwordHash', $passwordHash, \PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return false;
        }
    }
    if ($email != null) {
        if (findByEmail($email) != null) {
            return false;
        }
        $sql = "UPDATE musician SET email = :email WHERE name = :user";
        $stmt = $bd->prepare($sql);
        $stmt->bindValue(':user', $user, \PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return false;
        }
    }
    if ($bio != null) {
        $sql = "UPDATE musician SET bio = :bio WHERE name = :user";
        $stmt = $bd->prepare($sql);
        $stmt->bindValue(':user', $user, \PDO::PARAM_STR);
        $stmt->bindValue(':bio', $bio, \PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return false;
        }
    }
    if ($picture != null) {
        $sql = "UPDATE musician SET picture = :picture WHERE name = :user";
        $stmt = $bd->prepare($sql);
        $stmt->bindValue(':user', $user, \PDO::PARAM_STR);
        $stmt->bindValue(':picture', $picture, \PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return false;
        }
    }
    return true;
}

function getProfile($first, $after, $loggedUser)
{
    check_not_null($loggedUser);
    $userInfo = find($loggedUser);
    $userInfo->following = \Friend\fetchAll($first, $after, $loggedUser);
    $userInfo->followingCount = \Friend\count($loggedUser);
    return $userInfo;
}

function getUser($user, $first, $after, $loggedUser)
{
    check_not_null($user, $first, $after);
    $userInfo = find($user);
    if ($userInfo === null) {
        return null;
    }

    $projects = \Project\fetchAllFromUser($first, $after, $user, "", $loggedUser);
    $projectCount = \Project\countFromUser($user, $loggedUser);
    $followers = \Friend\count($user);
    $userInfo->followers = $followers;
    $userInfo->projectCount = $projectCount;
    $userInfo->projects = $projects;
    return $userInfo;
}

/**
 * Return un array avec les noms d'utilisateur
 */
function fetchAll($first, $after)
{
    check_not_null($first, $after);
    $bd = connect();
    $stmt = $bd->prepare("SELECT name, email, latestCommit, age, bio, picture,  (SELECT COUNT(followingName) FROM friend WHERE followingName = name) AS followers FROM musician GROUP BY name LIMIT :first OFFSET :after");
    $stmt->bindValue(':first', $first, \PDO::PARAM_INT);
    $stmt->bindValue(':after', $after, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $users = [];
    foreach ($stmt->fetchAll() as $user) {
        $users[] = (object) [
            'name' => $user['name'],
            'email' => $user['email'],
            'latestCommit' => $user['latestcommit'],
            'age' => $user['age'],
            'bio' => $user['bio'],
            'picture' => $user['picture'],
            'followers' => $user['followers'],
        ];
    }
    return $users;
}

function count()
{
    $bd = connect();
    $stmt = $bd->prepare("SELECT COUNT(*) FROM musician");
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $res = $stmt->fetch();
    return $res[0];
}

function findByEmail($email)
{
    $bd = connect();
    $stmt = $bd->prepare("SELECT * FROM musician WHERE email = :email");
    $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($stmt->rowCount() === 0) {
        return null;
    }

    $user = (object) [
        'name' => $res['name'],
        'email' => $res['email'],
        'latestCommit' => $res['latestcommit'],
        'age' => $res['age'],
        'bio' => $res['bio'],
        'picture' => $res['picture'],
    ];
    return $user;
}

function find($user)
{
    $bd = connect();
    $stmt = $bd->prepare("SELECT * FROM musician WHERE name = :name");
    $stmt->bindValue(':name', $user, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($stmt->rowCount() === 0) {
        return null;
    }
    $user = (object) [
        'name' => $res['name'],
        'email' => $res['email'],
        'latestCommit' => $res['latestcommit'],
        'age' => $res['age'],
        'bio' => $res['bio'],
        'picture' => $res['picture'],
    ];
    return $user;
}

function auth($user, $password)
{
    $hash = hash("sha256", $password);
    $bd = connect();
    $stmt = $bd->prepare("SELECT passwordHash FROM musician WHERE name = :name");
    $stmt->bindValue(':name', $user, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $passwordHash = $stmt->fetch()[0];
    return hash_equals($passwordHash, $hash);
}

function seek($user, $first, $after) {
    check_not_null($user);

    $stmt = $bd->prepare("SELECT COUNT(*), name, email, latestCommit, age, bio, picture, (SELECT COUNT(followingName) FROM friend WHERE followingName = name) AS followers 
    FROM musician GROUP BY name WHERE name LIKE :name LIMIT :first OFFSET :after");
    
    $stmt->bindValue(':name', "%".$user."%", \PDO::PARAM_STR);
    $stmt->bindValue(':first', $first, \PDO::PARAM_INT);
    $stmt->bindValue(':after', $after, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }

    $users = [];
    $res = $stmt->fetchAll();
    foreach ($res as $row) {
        $users[] = (object) [
            'name' => $row['name'],
            'email' => $row['email'],
            'latestCommit' => $row['latestcommit'],
            'age' => $row['age'],
            'bio' => $row['bio'],
            'picture' => $row['picture'],
            'followers' => $row['followers'],
        ];
    }

    return (object) [
        'results' => $users,
        'count' => $stmt->rowCount() > 0 ? $res[0]['count'] : 0,
    ];
}
