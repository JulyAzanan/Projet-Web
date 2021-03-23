<?php
include_once("config.php");

/**
 * Permet de créer un nouvel utilisateur
 */
function add_user($name, $password, $email, $age)
{
    if ($name == NULL || $password == NULL || strlen($name) <= 0 || strlen($password) <= 0) {
        return false;
    }
    $bd = connect();
    $sql = "INSERT INTO musician VALUES (:name, :passwordHash, :email, NULL, :age)";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':name', $name, \PDO::PARAM_STR);
    $stmt->bindValue(':passwordHash', $password, \PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
    $stmt->bindValue(':age', $age, \PDO::PARAM_INT);
    return $stmt->execute();
}

/**
 * Permet de retirer l'utilisateur dont le nom est entré en argument de la base de données
 */
function remove_user($name)
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
function get_all_users()
{
    $bd = connect();
    $stmt = $bd->prepare("SELECT * FROM musician");
    $stmt->execute();
    $users = [];
    foreach ($stmt->fetchAll() as $user) {
        $users[] = (object) [
            'name' => $user['name'],
        ];
    }
    return $users;
}