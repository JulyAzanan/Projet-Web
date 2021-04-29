<?php

namespace Contributor;

use PDO;

include_once __DIR__ . "/config.php";
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
function add($contributor, $authorName, $project, $loggedUser)
{
    check_not_null($contributor, $authorName, $project, $loggedUser);
    if (!check_project_exist($authorName, $project)) {
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
    $stmt->bindValue(':contributorname', $contributor, \PDO::PARAM_STR);
    $stmt->bindValue(':authorname', $authorName, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_STR);
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
function remove($contributor, $authorName, $project, $loggedUser)
{
    //Gérer le cas de se retirer soi-même si on veut quitter, mais si on n'est pas l'auteur,
    //alors on ne peut pas kick les autres --> Should be done
    check_not_null($contributor, $authorName, $project, $loggedUser);
    if (!check_project_exist($authorName, $project)) {
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
        if (strcmp($contributor, $loggedUser) != 0) {
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
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_STR);
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

function fetchAll($authorName, $project, $loggedUser)
{
    // Gérer cas projets privés et publics
    check_not_null($authorName, $project);

    if (!check_project_exist($authorName, $project)) {
        project_error();
    }

    $sql = "SELECT m.name, m.email, m.latestCommit, m.age, m.bio, m.picture FROM musician m JOIN contributor c
    ON m.name = c.contributorName
    JOIN project p
    ON p.name = c.projectName AND p.authorName = c.authorName
    WHERE c.projectName = :projectName AND c.authorName = :authorName
    AND ( p.private = 'f' OR :loggedUser IN (SELECT ccc.contributorName FROM contributor ccc WHERE ccc.projectName = c.projectName AND ccc.authorName = c.authorName) OR c.authorName = :loggedUser OR :loggedUser = 'admin' )  ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':projectName', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':authorName', $authorName, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        PDO_error();
    }
    $contributors = [];
    foreach ($stmt->fetchAll() as $contributor) {
        $contributors[] = (object) [
            'name' => $contributor['name'],
            'email' => $contributor['email'],
            'latestCommit' => $contributor['latestcommit'],
            'age' => $contributor['age'],
            'bio' => $contributor['bio'],
            'picture' => $contributor['picture'],
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
function count($authorName, $project, $loggedUser)
{
    // Gérer cas projets privés et publics
    check_not_null($authorName, $project);

    if (!check_project_exist($authorName, $project)) {
        project_error();
    }

    $sql = "SELECT COUNT(*) FROM contributor c JOIN project p
    ON p.name = c.projectName AND p.authorName = c.authorName
    WHERE c.projectName = :projectName AND c.authorName = :authorName
    AND ( p.private = 'f' OR :loggedUser IN (SELECT ccc.contributorName FROM contributor ccc WHERE ccc.projectName = c.projectName AND ccc.authorName = c.authorName) OR c.authorName = :loggedUser OR :loggedUser = 'admin' )  ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':projectName', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':authorName', $authorName, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $res['count'];
}

function find($author, $project, $contributor, $loggedUser)
{
    check_not_null($author, $project, $user, $loggedUser);

    $sql = "SELECT c.contributorName FROM contributor c JOIN project p
    ON p.name = c.projectName AND p.authorName = c.authorName
    WHERE c.projectName = :projectName AND c.authorName = :authorName AND c.contributorName = :contributorName
    AND ( p.private = 'f' OR :loggedUser IN (SELECT ccc.contributorName FROM contributor ccc WHERE ccc.projectName = c.projectName AND ccc.authorName = c.authorName) OR c.authorName = :loggedUser OR :loggedUser = 'admin' )  ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':projectName', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':authorName', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':contributorName', $user, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $stmt->rowCount() == 0 ? null : $res['contributorname'];
}
