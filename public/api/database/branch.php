<?php

namespace Branch;

include_once __DIR__ . "/config.php";
include_once __DIR__ . "/commit.php";
include_once __DIR__ . "/partition.php";
include_once __DIR__ . "/../utils/args.php";
include_once __DIR__ . "/../utils/updateAt.php";
/**
 * Add a branch for a selected project
 *
 * If the project does not exist, throw an project_error
 *
 * If the $loggedUser does not have the right, throw a forbidden_error
 *
 * If succesfully added, return true
 *
 * If not, return false
 */

function add($author, $project, $branch, $loggedUser)
{
    check_not_null($author, $project, $branch, $loggedUser);
    if (!check_project_exist($author, $project)) {
        arg_error();
    }
    if (!admin_or_contributor($author, $project, $loggedUser)) {
        forbidden_error(); //We do not have the right --> FORBIDDEN
    }
    if (check_branch_exist($author, $project, $branch)) {
        //La brnahce voule existe 
        branch_error();
    }
    //We are an either an admin, the creator or a contributor. We can add a branch

    //Always executed
    $sql = "INSERT INTO branch VALUES (:name, CURRENT_TIMESTAMP, :authorname, :projectname)";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':name', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':authorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_INT);

    if (!change_updatedAt_project($project, $author)) {
        return false;
    }
    ;

    return $stmt->execute();
    //Will return true on succes and false on failure

}

function merge($author, $project, $source, $dest, $loggedUser) {
    check_not_null($author, $project, $source, $dest, $loggedUser);
    if (!check_project_exist($author, $project)) {
        arg_error();
    }
    if (!admin_or_contributor($author, $project, $loggedUser)) {
        forbidden_error(); //We do not have the right --> FORBIDDEN
    }
    if (!check_branch_exist($author, $project, $source) || !check_branch_exist($author, $project, $dest)) {
        //La brnahce voule n'existe pas/le projet n'existe pas
        branch_error();
    }

    $partitions = \Partition\download($author, $project, $source, \Commit\getLatest($author, $project, $source, $loggedUser), $loggedUser);
    return \Commit\add($author, $project, $dest, "Merged from ".$source, $partitions, $loggedUser);
}

/**
 * Remove a branch from a project
 *
 * If the project does not exist, throw project_error()
 *
 * If we do not have the rights, we throw an forbidden_error
 *
 * If we are trying to remove the main branch, we throw a request_error
 *
 * If project is not found, return a
 *
 * If succesfully removed, return true
 *
 * If faild to remove, return false
 */
function remove($author, $project, $branch, $loggedUser)
{
    check_not_null($author, $project, $branch, $loggedUser);
    // ne pas supprimer la branche principale
    if (!check_project_exist($author, $project)) {
        project_error();
    }

    if (!admin_or_contributor($author, $project, $loggedUser)) {
        forbidden_error(); //We do not have the right to remove a branch --> FORBIDDEN
    }
    //Checking the name of the main branch. If we are trying to delete it, throw an request_error
    $sql = "SELECT mainBranchName FROM project WHERE name = :pname AND authorName = :pauthorname";
    //getting the main branch name of our project

    /**
     * Binding values
     */
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    //Executing request
    $i_got_a_result = $stmt->execute(); //Does the request was executed succesfully
    if (!$i_got_a_result) {
        /**
         * Something went wrong, but idk what to do
         */
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (strcmp($res['mainbranchname'], $branch) == 0) //Check if strings are equal
    {
        //Yes ? We are trying to delete the main branch. We should really not do taht
        request_error();
    }

    /**
     * If we are here, we should be able to remove the branch
     */
    //So creating the request and executing it
    $sql = "DELETE FROM branch WHERE name = :bname AND projectName = :pname AND authorName = :pauthorname";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);

    if (!change_updatedAt_project($project, $author)) {
        return false;
    }
    ;

    return $stmt->execute();

    //Will return true on succes and false on failure

}

/**
 * Rename a branch from a project
 *
 * If project does not exist, throw project_error
 *
 * If you do not have the rights to do so -> throw a forbidden error
 *
 * If the branch you are trying to rename does not exist -> Throw an arg_error
 *
 * Return true if succesfully removed, false if not
 */
function rename($author, $project, $branch, $new_branch_name, $loggedUser)
{
    check_not_null($author, $project, $branch, $new_branch_name, $loggedUser);
    if (!check_project_exist($author, $project)) {
        project_error();
    }
    if (!admin_or_contributor($author, $project, $loggedUser)) {
        forbidden_error(); //We do not have the right to rename a branch --> FORBIDDEN
    }
    /**
     * Checking if a branch with name $branch exist in the project $project made by $author
     */

    if (!check_branch_exist($author, $project, $branch) || check_branch_exist($author, $project, $new_branch_name)) {
        //No match (Like me in Tinder) for the requested branch -> Throw an arg_error
        arg_error();
    }
    //If we are here, the branch exist. Let's rename it now
    //Creating the request
    $bd = connect();
    $sql = "UPDATE branch
    SET name = :new_branch_name
    WHERE name = :bname AND projectName = :pname
    AND authorName = :pauthorname ";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':new_branch_name', $new_branch_name, \PDO::PARAM_STR);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);

    return $stmt->execute() && change_updatedAt_branch($project, $author, $new_branch_name);
}

function find($author, $project, $branch, $loggedUser)
{
    check_not_null($author, $project, $branch);

    $sql = "SELECT b.name, b.updatedAt
    FROM branch b JOIN project p
    ON p.name = b.projectName AND p.authorName = b.authorName
    WHERE b.authorName = :pauthorname AND b.projectName = :pname AND b.name = :bname
    AND ( p.private = 'f' OR :loggedUser IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = b.projectName AND c.authorName = b.authorName) OR b.authorName = :loggedUser OR :loggedUser = 'admin' )";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($stmt->rowCount() === 0) {
        return null;
    }
    $br = (object) [
        'name' => $res['name'],
        'updatedAt' => $res['updatedat'],
    ];
    return $br;
}

/**
 *  Gather all branches from a defined project
 *
 *  If you request a project that does not exist, throw a project_error
 *
 *  If you dont have the rights, show only public projects
 *
 *  If you have the rights, show private projects
 *
 *  if argument are not valid, throw
 */
function fetchAll($author, $project, $loggedUser)
{
    check_not_null($author, $project);

    if (!check_project_exist($author, $project)) {
        //Project does not exist. Aborting
        project_error();
    }

    $sql = "SELECT b.name, b.updatedAt
    FROM branch b JOIN project p
    ON p.name = b.projectName AND p.authorName = b.authorName
    WHERE b.authorName = :pauthorname AND b.projectName = :pname
    AND ( p.private = 'f' OR :loggedUser IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = b.projectName AND c.authorName = b.authorName) OR b.authorName = :loggedUser OR :loggedUser = 'admin' )";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $branchs = [];
    foreach ($stmt->fetchAll() as $branch) {
        $branchs[] = (object) [
            'name' => $branch['name'],
            'updatedAt' => $branch['updatedat'],
        ];
    }
    return $branchs;
}

function getBranch($author, $project, $branch, $loggedUser) {
    check_not_null($author, $project, $branch);
    $branchInfo = find($author, $project, $branch, $loggedUser);
    $branchInfo->commitsCount = \Commit\count($author, $project, $branch, $loggedUser);
    $branchInfo->lastCommit = \Commit\getLatest($author, $project, $branch, $loggedUser);
    $branchInfo->commits = \Commit\fetchAll($author, $project, $branch, $loggedUser);
    return $branchInfo;
}
/**
 * Count the number of branches for a given project
 *
 * Throw project_error if the specified project does not exist
 *
 * If you have the rights for a given project(eg: you are an admin or
 * you are a contributor of this project), count even if project is private
 *
 * If you do not have them however, should return 0 if private and the actual
 * number of branches if public (= !private)
 */
function count($author, $project, $loggedUser)
{
    check_not_null($author, $project);

    if (!check_project_exist($author, $project)) {
        project_error();
    }

    $sql = "SELECT COUNT(*)
    FROM branch b JOIN project p
    ON p.name = b.projectName AND p.authorName = b.authorName
    WHERE b.authorName = :pauthorname AND b.projectName = :pname
    AND ( p.private = 'f' OR :loggedUser IN (SELECT c.contributorName FROM contributor c WHERE c.projectName = b.projectName AND c.authorName = b.authorName) OR b.authorName = :loggedUser OR :loggedUser = 'admin' )";

    //Binding and executing
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //Failed to execute query
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $res['count'];
    // Gérer cas projets privés et publics --> Done
}
