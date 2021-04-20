<?php

namespace Project;

include_once "config.php";

use \PDO;
use \Exception;

function add($author, $project, $private)
{
    // utiliser une transaction pour créer une branche main. cf https://www.php.net/manual/fr/pdo.transactions.php

    check_not_null($author, $project, $private);

    try {
        $bd = connect();
        echo "Connecté\n";
    } catch (Exception $e) {
        die("Impossible de se connecter : " . $e->getMessage());
    }

    try {
        $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bd->beginTransaction();
        //Creating the 1 request
        $sql = "INSERT INTO project 
        VALUES (:name, :updatedAt, :createdAt, :authorName, :mainBranchName,:private)";
        $stmt = $bd->prepare($sql);
        //Binding values
        $stmt->bindValue(':name', $project, \PDO::PARAM_STR);
        $stmt->bindValue(':updatedAt', "CURRENT_TIMESTAMP", \PDO::PARAM_STR);
        $stmt->bindValue(':createdAt', "CURRENT_TIMESTAMP", \PDO::PARAM_STR);
        $stmt->bindValue(':authorName', $author, \PDO::PARAM_STR);
        $stmt->bindValue(':mainBranchName', "main", \PDO::PARAM_STR);
        $stmt->bindValue(':private', $private, \PDO::PARAM_STR);
        //Placing in queue
        $stmt->execute();


        $sql = "INSERT INTO branch 
        VALUES (:name, :updatedat,:authorname, :projectName)";
        $stmt = $bd->prepare($sql);
        //Binding values
        $stmt->bindValue(':name', "main", \PDO::PARAM_STR);
        $stmt->bindValue(':updatedAt', "CURRENT_TIMESTAMP", \PDO::PARAM_STR);
        $stmt->bindValue(':authorName', $author, \PDO::PARAM_STR);
        $stmt->bindValue(':projectName', $project, \PDO::PARAM_STR);
        //Placing in queue
        $stmt->execute();

        //Executing the queue
        $bd->commit();
    } catch (Exception $e) {
        $bd->rollBack();
        echo "Failed: " . $e->getMessage();
    }
}

function remove($author, $project)
{
    // retirer les branches en premier et utiliser une transaction pour supprimer la branche main
}

function fetchAll($first, $after, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour un order by date de création ou de modif, asc ou desc
}

function fetchAllFromUser($first, $after, $user, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour un order by date de création ou de modif, asc ou desc
}

function count($loggedUser)
{
    //Gérer cas des projets privés et publics
}

function countFromUser($loggedUser)
{
    //Gérer cas des projets privés et publics
}

function seekProject($first, $after, $author, $project, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics, $partitions unique pour une version fixée. Utiliser LIKE %$branch%
}
