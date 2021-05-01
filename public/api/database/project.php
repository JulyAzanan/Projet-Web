<?php

namespace Project;

include_once __DIR__ . "/config.php";
include_once __DIR__ . "/contributor.php";
include_once __DIR__ . "/branch.php";
include_once __DIR__ . "/../utils/order.php";
include_once __DIR__ . "/../utils/args.php";

use \PDO;

function add($author, $project, $private, $description, $loggedUser)
{
    check_not_null($author, $project, $private, $loggedUser);

    check_owner($author, $loggedUser);

    $bd = connect();

    try {
        $bd->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $bd->beginTransaction();

        //Creating the 1 request
        $sql = "INSERT INTO project
        VALUES (:name, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, :authorName, 'main', :description, :private)";
        $stmt = $bd->prepare($sql);
        //Binding values
        $stmt->bindValue(':name', $project, \PDO::PARAM_STR);
        $stmt->bindValue(':authorName', $author, \PDO::PARAM_STR);
        // $stmt->bindValue(':mainBranchName', "main", \PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, \PDO::PARAM_STR);
        $stmt->bindValue(':private', $private, \PDO::PARAM_BOOL);
        //Placing in queue
        $stmt->execute();

        $sql = "INSERT INTO branch
        VALUES (:name, CURRENT_TIMESTAMP,:authorName, :projectName)";
        $stmt = $bd->prepare($sql);
        //Binding values
        $stmt->bindValue(':name', "main", \PDO::PARAM_STR);
        $stmt->bindValue(':authorName', $author, \PDO::PARAM_STR);
        $stmt->bindValue(':projectName', $project, \PDO::PARAM_STR);
        //Placing in queue
        $stmt->execute();

        //Executing the queue
        $bd->commit();
    } catch (\Exception $e) {
        $bd->rollBack();
        echo "Failed: " . $e->getMessage();
        return false;
    }
    return true;
}

function update($author, $project, $private, $description, $mainBranchName, $loggedUser)
{
    check_not_null($author, $project, $loggedUser);
    if (!admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are not an admin and we are not a contributor
         * So we have to throw an error
         */
        forbidden_error();
    }
    $bd = connect();
    if ($private !== null) {
        $sql = "UPDATE project SET private = :private WHERE name = :project AND authorName = :author";
        $stmt = $bd->prepare($sql);
        $p = $private ? 't' : 'f';
        var_dump($private);
        var_dump($p);
        $stmt->bindValue(':author', $author, \PDO::PARAM_STR);
        $stmt->bindValue(':project', $project, \PDO::PARAM_STR);
        $stmt->bindValue(':private', $p, \PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return false;
        }
    }
    if ($description !== null) {
        $sql = "UPDATE project SET description = :description WHERE name = :project AND authorName = :author";
        $stmt = $bd->prepare($sql);
        $stmt->bindValue(':author', $author, \PDO::PARAM_STR);
        $stmt->bindValue(':project', $project, \PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, \PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return false;
        }
    }
    if ($mainBranchName !== null) {
        // TODO: find branch
        $sql = "UPDATE project SET mainBranchName = :mainBranchName WHERE name = :project AND authorName = :author";
        $stmt = $bd->prepare($sql);
        $stmt->bindValue(':author', $author, \PDO::PARAM_STR);
        $stmt->bindValue(':project', $project, \PDO::PARAM_STR);
        $stmt->bindValue(':mainBranchName', $mainBranchName, \PDO::PARAM_STR);
        if (!$stmt->execute()) {
            return false;
        }
    }
    return true;
}

function remove($author, $project, $loggedUser)
{
    // retirer les branches en premier et utiliser une transaction pour supprimer la branche main
    check_not_null($author, $project, $loggedUser);

    //No right to emove if we are not the admin or the creator of the project
    check_owner($author, $loggedUser);

    $bd = connect();

    /**
     * Removing all branches except and the project at the same time,
     * Using a transaction
     */
    try {
        $bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $bd->beginTransaction();
        //Creating the 1 request
        $sql = "DELETE FROM branch
        WHERE projectName = :pname AND authorName = :pauthorName";
        $stmt = $bd->prepare($sql);
        //Binding values
        $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
        $stmt->bindValue(':pauthorName', $author, \PDO::PARAM_STR);
        //Placing in queue
        $stmt->execute();

        $sql = "DELETE FROM project
        WHERE name = :pname AND authorName = :pauthorName";
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
        return false;
    }
    return true;
}

function getProject($user, $project, $loggedUser)
{
    check_not_null($user, $project);
    $projectInfo = find($user, $project, $loggedUser);
    if ($projectInfo === null) return null; 
    $projectInfo->contributors = \Contributor\fetchAll($user, $project, $loggedUser);
    $projectInfo->branches = \Branch\fetchAll($user, $project, $loggedUser);
    return $projectInfo;
}

function fetchAll($first, $after, $order, $loggedUser)
{
    check_not_null($first, $after, $order);
    if ($first < 0 || $after < 0) {
        //Cannot use negative values
        arg_error();
    }

    $real_order = get_real_order($order);

    $sql = "SELECT name, updatedAt, createdAt, description, p.authorName, private
        FROM project p
        WHERE p.private = 'f'
        OR :contributorname IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = name AND c.authorName = p.authorName)
        OR :contributorname = 'admin'
        OR :contributorname = p.authorName
        ORDER BY " . $real_order . "
        LIMIT :number_to_show OFFSET :offset ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $first, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $after, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        PDO_error();
    }
    $projects = [];
    foreach ($stmt->fetchAll() as $project) {
        $projects[] = (object) [
            'name' => $project['name'],
            'authorName' => $project['authorname'],
            'updatedAt' => $project['updatedat'],
            'createdAt' => $project['createdat'],
            'description' => $project['description'],
            'private' => $project['private'],
        ];
    }
    return $projects;
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour un order by date de création ou de modif, asc ou desc
}

function fetchAllFromUser($first, $after, $user, $order, $loggedUser)
{
    check_not_null($first, $after, $user, $order);
    if ($first < 0 || $after < 0) {
        //Cannot use negative values
        arg_error();
    }

    $real_order = get_real_order($order);

    $sql = "SELECT name, updatedAt, createdAt, description, p.authorName, private
    FROM project p
    WHERE p.authorName = :pauthorname
    AND ( p.private = 'f' OR :contributorname IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = name AND c.authorName = p.authorName) OR p.authorName = :contributorname OR :contributorname = 'admin' )
    ORDER BY " . $real_order . "
    LIMIT :number_to_show OFFSET :offset ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pauthorname', $user, \PDO::PARAM_STR);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $first, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $after, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $projects = [];
    foreach ($stmt->fetchAll() as $project) {
        $projects[] = (object) [
            'name' => $project['name'],
            'authorName' => $project['authorname'],
            'updatedAt' => $project['updatedat'],
            'createdAt' => $project['createdat'],
            'description' => $project['description'],
            'private' => $project['private'],
        ];
    }
    return $projects;
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour un order by date de création ou de modif, asc ou desc
}

function count($loggedUser)
{
    $sql = "SELECT COUNT(*)
        FROM project p
        WHERE p.private = 'f'
        OR :contributorname IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = name AND c.authorName = p.authorName)
        OR p.authorName = :contributorname
        OR :contributorname = 'admin'";

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

    check_not_null($user);

    $sql = "SELECT COUNT(*)
        FROM project p
        WHERE p.authorName = :authorName
        AND (p.private = 'f' OR :contributorname IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = name AND c.authorName = p.authorName) OR p.authorName = :contributorname OR :contributorname = 'admin') ";

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

function seekFromAuthor($first, $after, $author, $project, $loggedUser)
{
    check_not_null($first, $after, $author, $project);
    if ($first < 0 || $after < 0) {
        //Cannot use negative values
        arg_error();
    }

    $sql = "SELECT name, updatedAt, createdAt, description, p.authorName, private
    FROM project p
    WHERE p.authorName = :pauthorname
    AND p.name LIKE :projectname
    AND ( p.private = 'f' OR :contributorname IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = name AND c.authorName = p.authorName) OR p.authorName = :contributorname OR :contributorname = 'admin' )
    LIMIT :number_to_show OFFSET :offset ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', "%" . $project . "%", \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $first, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $after, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $projects = [];
    foreach ($stmt->fetchAll() as $project) {
        $projects[] = (object) [
            'name' => $project['name'],
            'authorName' => $project['authorName'],
            'updatedAt' => $project['updatedat'],
            'createdAt' => $project['createdat'],
            'description' => $project['description'],
            'private' => $project['private'],
        ];
    }
    return $projects;

    // Gérer cas projets privés et publics
}

function seek($first, $after, $project, $loggedUser)
{
    check_not_null($first, $after, $project);
    if ($first < 0 || $after < 0) {
        //Cannot use negative values
        arg_error();
    }

    $sql = "SELECT name, updatedAt, createdAt, description, p.authorName, private
    FROM project p
    WHERE p.name LIKE :projectname
    AND ( p.private = 'f' OR :contributorname IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = name AND c.authorName = p.authorName) OR p.authorName = :contributorname OR :contributorname = 'admin' )
    ORDER BY updatedAt DESC
    LIMIT :number_to_show OFFSET :offset ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':projectname', "%" . $project . "%", \PDO::PARAM_STR);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $first, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $after, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $projects = [];
    $res = $stmt->fetchAll();
    foreach ($res as $row) {
        $projects[] = (object) [
            'name' => $row['name'],
            'authorName' => $row['authorname'],
            'updatedAt' => $row['updatedat'],
            'createdAt' => $row['createdat'],
            'description' => $row['description'],
            'private' => $row['private'],
        ];
    }
    $sql = "SELECT COUNT(*)
    FROM project p
    WHERE p.name LIKE :projectname
    AND ( p.private = 'f' OR :contributorname IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = name AND c.authorName = p.authorName) OR p.authorName = :contributorname OR :contributorname = 'admin' )";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':projectname', "%" . $project . "%", \PDO::PARAM_STR);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);

    return (object) [
        'results' => $projects,
        'count' => $stmt->rowCount() > 0 ? $res['count'] : 0,
    ];
}

function find($user, $project, $loggedUser)
{
    check_not_null($user, $project);

    $sql = "SELECT name, updatedAt, createdAt, description, p.authorName, private, mainBranchName
    FROM project p
    WHERE p.name = :projectname
    AND ( p.private = 'f' OR :contributorname IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = name AND c.authorName = p.authorName) OR p.authorName = :contributorname OR :contributorname = 'admin' )";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':contributorname', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($stmt->rowCount() === 0) {
        return null;
    }
    $proj = (object) [
        'name' => $res['name'],
        'authorName' => $res['authorname'],
        'updatedAt' => $res['updatedat'],
        'createdAt' => $res['createdat'],
        'description' => $res['description'],
        'private' => $res['private'],
        'mainBranch' => $res['mainbranchname'],
    ];
    return $proj;

    // Gérer cas projets privés et publics
}
