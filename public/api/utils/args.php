<?php

require_once __DIR__ . "/error.php";

function check_not_null(...$args)
{
    foreach ($args as $arg) {
        if ($arg === null) {
            arg_error();
        }
    }
}

function check_owner($owner, $loggedUser)
{
    if (check_owner_bool($owner, $loggedUser)) {
        forbidden_error();
    }
}

// Return false if we are the admin or the owner
function check_owner_bool($owner, $loggedUser)
{
    return !($loggedUser === $owner || $loggedUser === "admin");
}

//Return true if $User_to_check is a contributor of $Project made by $Project_Author_Name
function check_contributor($User_to_check, $Project, $Project_Author_Name)
{
    $bd = connect();
    //Select all contributor from project $Project
    $stmt = $bd->prepare("SELECT c.contributorName FROM contributor c JOIN project p ON c.projectName = p.name AND c.authorName = p.authorName WHERE p.name = :pname AND p.authorName = :pauthorname");
    //Binding args
    $stmt->bindValue(':pname', $Project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $Project_Author_Name, \PDO::PARAM_STR);
    $stmt->execute(); //Execute the request
    //We select all contributors
    foreach ($stmt->fetchAll() as $res) {
        if (strcmp($res['contributorName'], $User_to_check) === 0) //Check if $User_to_check is a contributor of the selected project
        {
            return true;
        }
    }
    //Not found, so not a contributor
    return false;

}
// Return true if we are either An admin, the creator or a contributor, else if not
function admin_or_contributor($author, $project, $loggedUser)
{
    check_not_null($author, $project, $loggedUser);

    if (check_owner_bool($author, $loggedUser)) { //Are we the creator or an admin ?
        //Nope, then
        if (!check_contributor($loggedUser, $project, $author)) //If we are not a contributor
        {
            //May replace with return false;
            return false; //Not a contributor and not an admin -> Do not have the rights
        }
    }
    return true;
}

function check_project_exist($Project_Author_Name, $project)
{
    $bd = connect();
    //Select all contributor from project $Project
    $stmt = $bd->prepare("SELECT name FROM project WHERE name = :pname AND authorName = :pauthorname");
    //Binding args
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $Project_Author_Name, \PDO::PARAM_STR);
    $stmt->execute(); //Execute the request
    return ($stmt->rowCount() === 1); //Check if we found the requested project
}
/**
 * Return true if the project of name $project made
 * by $Project_Author_Name contain the branch $branch
 * throw PDO_error if request cannot be executed
 */
function check_branch_exist($Project_Author_Name, $project, $branch)
{
    check_not_null($Project_Author_Name, $project, $branch);
    $bd = connect();
    //Selecting all project corresponding to our args
    $sql = "SELECT name
        FROM branch
        WHERE authorName = :aname
        AND projectName = :pname
        AND name = :bname ";
    $stmt = $bd->prepare($sql);
    //Binding args
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':aname', $Project_Author_Name, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    if (!$stmt->execute()) { //Execute the request
        //Something went wrong
        PDO_error();
    }
    //Ensure that we have one and only one branch of project that has the requested name
    return ($stmt->rowCount() === 1); //Check if we found the requested project
}
