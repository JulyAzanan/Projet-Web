<?php

namespace Contributor;

include_once "config.php";

function add($contributor, $authorName, $project, $loggedUser)
{
    check_not_null($contributor, $authorName, $project, $loggedUser);
    if (check_project_exist($authorName, $project)) {
        project_error();
    }

    if (!admin_or_contributor($authorName, $project, $loggedUser)) {
        /**
         * We are not an admin and we are not a contributor
         * So we have to throw an error
         */
        arg_error();
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

function remove($contributor, $authorName, $project, $loggedUser)
{
    //Gérer le cas de se retirer soi-même si on veut quitter, mais si on n'est pas l'auteur, 
    //alors on ne peut pas kick les autres --> Should be done 
    check_not_null($contributor, $authorName, $project, $loggedUser);
    if (check_project_exist($authorName, $project)) {
        project_error();
    }

    if (!admin_or_contributor($authorName, $project, $loggedUser)) {
        /**
         * We are not an admin and we are not a contributor
         * So we have to throw an error
         */
        arg_error();
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
        //Shouldn't go here but for safety reason let's
        forbidden_error();
    }


    $sql = "DELETE FROM contributor
    WHERE contributor = :contributoname 
    AND pojectname = :projectname 
    AND authorname = :authorname";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':contributoname', $contributor, \PDO::PARAM_STR);
    $stmt->bindValue(':authorname', $authorName, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_INT);
    return $stmt->execute();
}

function fetchAllFromProject($first, $after, /*$contributor, Not needed right ?*/ $authorName, $project, $loggedUser)
{
    // Gérer cas projets privés et publics
    check_not_null($first, $after, $authorName, $project, $loggedUser);

    if (check_project_exist($authorName, $project)) {
        project_error();
    }
    if (admin_or_contributor($authorName, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "SELECT contributorName
        FROM contributor c
        JOIN Projet p ON
        c.projectName = p.name and c.authorName = p.name
        WHERE b.projectName = :pname AND b.authorname = :pauthorname
        LIMIT :number_to_show OFFSET :offset ";
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "SELECT contributorName
        FROM contributor c
        JOIN Projet p ON
        c.projectName = p.name and c.authorName = p.authorName
        WHERE b.projectname = :pname AND b.authorname = :pauthorname AND p.private = 'f'
        LIMIT :number_to_show OFFSET :offset ";
    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $authorName, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':number_to_show', $first, \PDO::PARAM_INT);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $contributor) {
        $$contributors[] = (object) [
            'contributorName' => $contributor['contributorName'],
        ];
    }
    return $branchs;
}

function countFromProject(/*$contributor -> Not needed ?!?,*/ $authorName, $project, $loggedUser)
{
    // Gérer cas projets privés et publics
    check_not_null($authorName, $project, $loggedUser);

    if (check_project_exist($authorName, $project)) {
        project_error();
    }
    if (admin_or_contributor($authorName, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "COUNT(*)
        FROM contributor c
        JOIN Projet p ON
        c.projectName = p.name and c.authorName = p.name
        WHERE b.projectName = :pname AND b.authorname = :pauthorname ";
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "COUNT(*)
        FROM contributor c
        JOIN Projet p ON
        c.projectName = p.name and c.authorName = p.authorName
        WHERE b.projectname = :pname AND b.authorname = :pauthorname AND p.private = 'f' ";
    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $authorName, \PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->execute()) {
        foreach ($stmt->fetchAll() as $res) { //Should be only one result, but anyway i'm looping for safety reasons
            return $res[0];
        }
    } else return -1 ;
}
