<?php

namespace Contributor;

use PDO;

include_once "config.php";
/**
 * Add a contributor to a project, identified by $authorName and $project
 * 
 * It handle: 
 *
 * Permissions : If you are a contributor or an admin, you can add a contributor
 * 
 * If not, throw an forbidden_error
 * 
 * It throw:
 * 
 * project_error if the selected project does not exist
 * 
 * It return :
 * 
 * true on succes, false on failure
 */
function add(string $contributor,string $authorName,string $project,string $loggedUser){
    check_not_null($contributor, $authorName, $project, $loggedUser);
    if (! check_project_exist($authorName, $project)) {
        project_error();
    }

    if (!admin_or_contributor($authorName, $project, $loggedUser)) {
        /**
         * We are not an admin and we are not a contributor
         * So we have to throw an error
         */
        forbidden_error();
    }
    //We are an admin or a contributor. Let's add the selected contributor
    // To the database
    $sql = "INSERT INTO contributor
    VALUES (:contributorname, :authorname, :projectname)";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':contributoname', $contributor, \PDO::PARAM_STR);
    $stmt->bindValue(':authorname', $authorName, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_INT);
    return $stmt->execute();
}
/**
 * Remove a contributor to a project, identified by $authorName and $project
 * 
 * It handle: 
 *
 * Permissions : If you are a contributor or an admin, you can remove any contributor of teh project
 * 
 * If you are an contributor, you can only kick yourself 
 * 
 * If you are neither, throw a forbidden error
 * 
 * It throw:
 * 
 * project_error if the selected project does not exist
 * 
 * forbidden_error if you are not an admin or a contributor of the project
 * 
 * It return :
 * 
 * true on succes, false on failure
 */
function remove(string $contributor,string $authorName,string $project,string $loggedUser)
{
    //Gérer le cas de se retirer soi-même si on veut quitter, mais si on n'est pas l'auteur, 
    //alors on ne peut pas kick les autres --> Should be done 
    check_not_null($contributor, $authorName, $project, $loggedUser);
    if (! check_project_exist($authorName, $project)) {
        project_error();
    }

    if (!admin_or_contributor($authorName, $project, $loggedUser)) {
        /**
         * We are not an admin and we are not a contributor
         * So we have to throw an error
         */
        forbidden_error();
    }
    //We are an admin or a contributor. Let's add the selected contributor
    // To the database
    if (!check_owner_bool($authorName, $loggedUser)) {
        //We are an admin or the owner. We have all the rights

    } else if (check_contributor($loggedUser, $project, $authorName)) {
        //We are a contributor. (This test might be unecessary)
        if (strcmp($contributor, $loggedUser) == 0) {
            //We are leaving our contributor status
        } else {
            //We are trying to move someone else. No allowed so throw 
            forbidden_error();
        }
    } else {
        //Shouldn't go here but for safety reason let's throw an error
        forbidden_error();
    }


    $sql = "DELETE FROM contributor
    WHERE contributorName = :contributorname 
    AND pojectName = :projectname 
    AND authorName = :authorname";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':contributorname', $contributor, \PDO::PARAM_STR);
    $stmt->bindValue(':authorname', $authorName, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_INT);
    return $stmt->execute();
}
/**
 * Gather all contributors from a project, identified with $authorName and $project
 * 
 * It handle: 
 * 
 * Permissions : If you are an admin or a contributor, let you fetch them all
 * 
 * If not, it let you do it only if the project is public
 * 
 * It throw
 * 
 * project_error if the project does not exist
 * 
 * PDO_error if the request could not be Executed
 *  
 * It return:
 * 
 * An array-like oject that contain all the contributors of a selected project
 */


function fetchAllFromProject(int $first,int $after,string $authorName,string $project,string $loggedUser)
{
    // Gérer cas projets privés et publics
    check_not_null($first, $after, $authorName, $project, $loggedUser);

    if (! check_project_exist($authorName, $project)) {
        project_error();
    }
    $sql = "";
    if (admin_or_contributor($authorName, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "SELECT contributorName
        FROM contributor c
        WHERE c.projectName = :pname AND c.authorname = :pauthorname
        LIMIT :number_to_show OFFSET :offset ";
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "SELECT contributorName
        FROM contributor c
        JOIN projet p ON
        c.projectName = p.name 
        AND c.authorName = p.authorName
        WHERE c.projectName = :pname 
        AND c.authorName = :pauthorname 
        AND p.private = 'f'
        LIMIT :number_to_show OFFSET :offset ";
    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $authorName, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $first, \PDO::PARAM_INT);
    if (! $stmt->execute() ){
        PDO_error();
    };
    $contributors = [];
    foreach ($stmt->fetchAll() as $contributor) {
        $contributors[] = (object) [
            'contributorName' => $contributor['contributorName'],
        ];
    }
    return $contributors;
}
/**
 * Count the number of contributors from a project, identified with $authorName and $project
 * 
 * It handle: 
 * 
 * Permissions : If you are an admin or a contributor, let you count
 * 
 * If not, it let you do it only if the project is public
 * 
 * It throw
 * 
 * project_error if the project does not exist
 * 
 * PDO_error if the request could not be Executed
 *  
 * It return:
 * 
 * An positive integer (0 included) on succes, and may return -1 if result are incoherent
 */
function countFromProject(string $authorName,string $project,string $loggedUser)
{
    // Gérer cas projets privés et publics
    check_not_null($authorName, $project, $loggedUser);

    if (! check_project_exist($authorName, $project)) {
        project_error();
    }
    $sql = "";
    if (admin_or_contributor($authorName, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "SELECT COUNT(*)
        FROM contributor c
        WHERE c.projectName = :pname 
        AND c.authorName = :pauthorname ";
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "SELECT COUNT(*)
        FROM contributor c
        JOIN projet p ON
        c.projectName = p.name 
        AND c.authorName = p.authorName
        WHERE c.projectName = :pname 
        AND b.authorName = :pauthorname 
        AND p.private = 'f' ";
    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $authorName, \PDO::PARAM_STR);
    if (!$stmt->execute()){
        PDO_error();
    }
    if ($stmt->execute()) {
        foreach ($stmt->fetchAll() as $res) { //Should be only one result, but anyway i'm looping for safety reasons
            return $res[0];
        }
    } else return -1 ;
}
