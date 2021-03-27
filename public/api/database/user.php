<?php
namespace User;

include_once "config.php";
include_once "../utils/error.php";
include_once "../utils/args.php";

/**
 * Permet de créer un nouvel utilisateur
 */
function add($user, $password, $email, $age)
{
    check_not_null($user, $password);
    if (strlen($name) <= 0 || strlen($password) <= 0) {
        arg_error();
    }
    $passwordHash = hash("sha256", $password);
    $bd = connect();
    $sql = "INSERT INTO musician VALUES (:name, :passwordHash, :email, NULL, :age)";
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

function update($user, $password, $email, $age, $loggedUser)
{
    check_not_null($user, $loggedUser);
    check_owner($user, $loggedUser);
    $bd = connect();
    if ($age != null) {
        $sql = "UPDATE musician SET age = :age WHERE name = :user";
        $stmt->bindValue(':user', $user, \PDO::PARAM_STR);
        $stmt->bindValue(':age', $age, \PDO::PARAM_INT);
        $stmt = $bd->prepare($sql);
        if (!$stmt->execute()) {
            return false;
        }
    }
    if ($password != null) {
        $passwordHash = hash("sha256", $password);
        $sql = "UPDATE musician SET passwordHash = :passwordHash WHERE name = :user";
        $stmt->bindValue(':user', $user, \PDO::PARAM_STR);
        $stmt->bindValue(':passwordHash', $passwordHash, \PDO::PARAM_STR);
        $stmt = $bd->prepare($sql);
        if (!$stmt->execute()) {
            return false;
        }
    }
    if ($email != null) {
        if (findByEmail($email) != null) {
            return false;
        }

        $sql = "UPDATE musician SET email = :email WHERE name = :user";
        $stmt->bindValue(':user', $user, \PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt = $bd->prepare($sql);
        if (!$stmt->execute()) {
            return false;
        }
    }
    return true;
}

/**
 * Return un array avec les noms d'utilisateur
 */
function fetchAll($first, $after)
{
    check_not_null($first, $after);
    $bd = connect();
    $stmt = $bd->prepare("SELECT name, email, latestCommit, age FROM musician LIMIT :first OFFSET :after");
    $stmt->bindValue(':first', $first, \PDO::PARAM_INT);
    $stmt->bindValue(':after', $after, \PDO::PARAM_INT);
    $stmt->execute();
    $users = [];
    foreach ($stmt->fetchAll() as $user) {
        $users[] = (object) [
            'name' => $user['name'],
            'email' => $user['email'],
            'latestCommit' => $user['latestCommit'],
            'age' => $user['age'],
        ];
    }
    return $users;
}

function count()
{
    $bd = connect();
    $stmt = $bd->prepare("SELECT COUNT(*) FROM musician");
    $stmt->execute();
    $res = $stmt->fetch();
    return $res[0];
}

function findByEmail($email)
{
    $bd = connect();
    $stmt = $bd->prepare("SELECT * FROM musician WHERE email = :email");
    $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
    $stmt->execute();
    $user = null;
    foreach ($stmt->fetchAll() as $res) {
        $user = (object) [
            'name' => $res['name'],
            'email' => $res['email'],
            'latestCommit' => $res['latestCommit'],
            'age' => $res['age'],
        ];
    }
    return $user;
}

function find($user)
{
    $bd = connect();
    $stmt = $bd->prepare("SELECT * FROM musician WHERE name = :name");
    $stmt->bindValue(':name', $user, \PDO::PARAM_STR);
    $stmt->execute();
    $user = null;
    foreach ($stmt->fetchAll() as $res) {
        $user = (object) [
            'name' => $res['name'],
            'email' => $res['email'],
            'latestCommit' => $res['latestCommit'],
            'age' => $res['age'],
        ];
    }
    return $user;
}

function auth($user, $password)
{
    $hash = hash("sha256", $password);
    $bd = connect();
    $stmt = $bd->prepare("SELECT passwordHash FROM musician WHERE name = :name");
    $stmt->bindValue(':name', $user, \PDO::PARAM_STR);
    $stmt->execute();
    $passwordHash = $stmt->fetch()[0];
    return hash_equals($passwordHash, $hash);
}
