<?php
namespace User;

include_once "config.php";

/**
 * Permet de créer un nouvel utilisateur
 */
function add($name, $password, $email, $age)
{
    if ($name == null || $password == null || strlen($name) <= 0 || strlen($password) <= 0) {
        return false;
    }
    $passwordHash = hash("sha256", $password);
    $bd = connect();
    $sql = "INSERT INTO musician VALUES (:name, :passwordHash, :email, NULL, :age)";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':name', $name, \PDO::PARAM_STR);
    $stmt->bindValue(':passwordHash', $passwordHash, \PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
    $stmt->bindValue(':age', $age, \PDO::PARAM_INT);
    return $stmt->execute();
}

/**
 * Permet de retirer l'utilisateur dont le nom est entré en argument de la base de données
 */
function remove($name)
{
    $bd = connect();
    $sql = "DELETE FROM musician WHERE name = :name";

    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':name', $name, \PDO::PARAM_STR);
    return $stmt->execute();
}

/**
 * Return un array avec les noms d'utilisateur
 */
function fetchAll($first, $after)
{
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

function auth($name, $password)
{
    $hash = hash("sha256", $password);
    $bd = connect();
    $stmt = $bd->prepare("SELECT passwordHash FROM musician WHERE name = :name");
    $stmt->bindValue(':name', $name, \PDO::PARAM_STR);
    $stmt->execute();
    $passwordHash = $stmt->fetch()[0];
    return hash_equals($passwordHash, $hash);
}

function count()
{
    $bd = connect();
    $stmt = $bd->prepare("SELECT COUNT(*) FROM musician");
    $stmt->execute();
    $res = $stmt->fetch();
    return $res[0];
}
