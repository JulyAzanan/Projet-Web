<?php
namespace Branch;

include_once "config.php";

function add($author, $project, $branch, $loggedUser)
{
    check_not_null($author, $project, $branch, $private, $loggedUser);

    if (!admin_or_contributor($author, $project, $loggedUser) ){
        forbidden_error(); //We do not have the right --> FORBIDDEN
    }
    //We are an either an admin, the creator or a contributor. We can add a branch 

    //Always executed
    $sql = $sql = "INSERT INTO branch VALUES (:name, updatedat, :authorname, :projectname)";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':name', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':apdatedat', "CURRENT_TIMESTAMP", \PDO::PARAM_STR); //Let see !
    $stmt->bindValue(':authorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_INT);
    return $stmt->execute();

}
/**
 * Remove a branch from a project
 * If we do not have the rights, we throw an forbidden_error
 * If we are trying to remove the main branch, we throw a request_error
 * If succesfully removed, return true
 * If faild to remove, return false
 */
function remove($author, $project, $branch,$loggedUser)

{
    // ne pas supprimer la branche principale
    
    if (!admin_or_contributor($author, $project, $loggedUser) ){
        forbidden_error(); //We do not have the right to remove a branch --> FORBIDDEN
    }
    //Checking the name of the main branch. If we are trying to delete it, throw an request_error 
    $sql = $sql = "SELECT mainbranchname FROM Project WHERE name = :pname AND authorname = :pauthorname";
    //getting the main branch name of our project 
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $res) { //Should be only one result, but anyway i'm looping for safety reasons
        if (strcmp($res['mainbranchname'],$branch) == 0 )//Check if strings are equal
        {
            //Yes ? We are trying to delete the main branch
            request_error() ;
        }
    }

    
}

function rename($author, $project, $branch, $loggedUser)
{

}

function fetchAllFromProject($first, $after, $author, $project, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    //Gérer cas projets privés et publics
}

function fetchAllFromUser($first, $after, $user, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    //Gérer cas projets privés et publics
}

function countFromProject($author, $project, $loggedUser)
{
    // Gérer cas projets privés et publics
}

function countFromUser($user, $loggedUser)
{
    // Gérer cas projets privés et publics
}

function seekVersion($first, $after, $author, $project, $branch, $version, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics, $partitions unique pour une version fixée. Utiliser LIKE %$version%
}
