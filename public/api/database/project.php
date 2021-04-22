<?php

namespace Project;

include_once "config.php";
//include_once "args.php";

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
        VALUES (:name, :updatedAt,:authorName, :projectName)";
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

function remove($author, $project, $loggedUser)
{
    // retirer les branches en premier et utiliser une transaction pour supprimer la branche main
    check_not_null($author, $project, $loggedUser);

    //No right to emove if we are not the admin or the creator of the project
    check_owner($author, $loggedUser);



    try {
        $bd = connect();
        echo "Connecté\n";
    } catch (Exception $e) {
        die("Impossible de se connecter : " . $e->getMessage());
    }
    /**
     * Removing all branches except and the project at the same time, 
     * Using a transaction
     */
    try {
        $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bd->beginTransaction();
        //Creating the 1 request
        $sql = "DELETE FROM branch 
        WHERE pojectName = :pname AND authorName = :pauthorname";
        $stmt = $bd->prepare($sql);
        //Binding values
        $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
        $stmt->bindValue(':pauthorName', $author, \PDO::PARAM_STR);
        //Placing in queue
        $stmt->execute();


        $sql = "DELETE FROM project
        WHERE pojectName = :pname AND authorName = :pauthorname";
        $stmt = $bd->prepare($sql);
        //Binding values
        $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
        $stmt->bindValue(':pauthorName', $author, \PDO::PARAM_STR);
        //Placing in queue
        $stmt->execute();

        //Executing the queue
        $bd->commit();
    } catch (Exception $e) {
        $bd->rollBack();
        echo "Failed: Cannot remove project, rollback" . $e->getMessage();
    }
}

function fetchAll(int $first, int $after, string $order, $loggedUser)
{


    check_not_null($first, $after, $order, $loggedUser);
    if ($first < 0 || $after < 0) {
        //Cannot use negative values
        arg_error();
    }

    $real_order = get_real_order($order);

    $sql = "SELECT name
        FROM project p
        WHERE p.authorName = :pauthorname
        AND ( p.private = 'f' OR c.contributorName = :contributorname )
        ORDER BY :order
        LIMIT :number_to_show OFFSET :offset ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pauthorname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':order', $real_order, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $first, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $projects = [];
    foreach ($stmt->fetchAll() as $project) {
        $projects[] = (object) [
            'name' => $project['name'],
            'authorName' => $project['authorName'],
        ];
    }
    return $projects;
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour un order by date de création ou de modif, asc ou desc
}

function fetchAllFromUser($first, $after, $user, $order, $loggedUser)
{

    check_not_null($first, $after, $user, $order, $loggedUser);
    if ($first < 0 || $after < 0) {
        //Cannot use negative values
        arg_error();
    }


    $real_order = get_real_order($order);


    $sql = "SELECT name
    FROM project p
    JOIN contributor c
    ON p.authorName = c.authorName
    AND p.name = c.projectName
    WHERE p.authorName = :pauthorname
    AND ( p.private = 'f' OR c.contributorName = :contributorname )
    ORDER BY :order
    LIMIT :number_to_show OFFSET :offset ";


    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pauthorname', $user, \PDO::PARAM_STR);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':order', $real_order, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $first, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $projects = [];
    foreach ($stmt->fetchAll() as $project) {
        $projects[] = (object) [
            'name' => $project['name'],
            'authorName' => $project['authorName'],
        ];
    }
    return $projects;
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour un order by date de création ou de modif, asc ou desc
}

function count($loggedUser)
{


    $sql = "SELECT COUNT(*)
        FROM project p
        WHERE  p.private = 'f' 
        OR c.contributorName = :contributorname ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    //succesfully executed request.
    foreach ($stmt->fetchAll() as $res) { //Should be only one result, but anyway i'm looping for safety reasons
        return $res[0];
    }

    //Gérer cas des projets privés et publics
}

function countFromUser($user, $loggedUser)
{

    check_not_null($user,$loggedUser);


    $sql = "SELECT COUNT(*)
        FROM project p
        JOIN contributor c
        ON p.authorName = c.authorName
        AND p.name = c.projectName
        WHERE  P.authorName = :authorName
        AND (p.private = 'f' OR c.contributorName = :contributorname) ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':authorName', $user, \PDO::PARAM_STR);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    //succesfully executed request.
    foreach ($stmt->fetchAll() as $res) { //Should be only one result, but anyway i'm looping for safety reasons
        return $res[0];
    }
    //Gérer cas des projets privés et publics
}

function seekProjectFromAuthor($first, $after, $author, $project, $loggedUser)
{
    check_not_null($first, $after, $author, $project, $loggedUser);
    if ($first < 0 || $after < 0) {
        //Cannot use negative values
        arg_error();
    }
    

    $sql = "SELECT name
    FROM project p
    JOIN contributor c
    ON p.authorName = c.authorName
    AND p.name = c.projectName
    WHERE p.authorName = :pauthorname
    AND p.name LIKE %:projectname%
    AND ( p.private = 'f' OR c.contributorName = :contributorname )
    LIMIT :number_to_show OFFSET :offset ";


    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $first, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $projects = [];
    foreach ($stmt->fetchAll() as $project) {
        $projects[] = (object) [
            'name' => $project['name'],
        ];
    }
    return $projects;

    // Gérer cas projets privés et publics
}


function seekProject($first, $after, $project, $loggedUser)
{
    check_not_null($first, $after, $project, $loggedUser);
    if ($first < 0 || $after < 0) {
        //Cannot use negative values
        arg_error();
    }
    

    $sql = "SELECT name
    FROM project p
    JOIN contributor c
    ON p.authorName = c.authorName
    AND p.name = c.projectName
    WHERE p.name LIKE %:projectname%
    AND ( p.private = 'f' OR c.contributorName = :contributorname )
    LIMIT :number_to_show OFFSET :offset ";


    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':projectname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $first, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $projects = [];
    foreach ($stmt->fetchAll() as $project) {
        $projects[] = (object) [
            'name' => $project['name'],
            'authorName' => $project['authorName'],
        ];
    }
    return $projects;

    // Gérer cas projets privés et publics
}