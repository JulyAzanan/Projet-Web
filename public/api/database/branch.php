<?php
namespace Branch;

include_once "config.php";
/**
 * Add a branch for a selected project
 * If the project does not exist, throw an project_error
 * If the $loggedUser does not have the right, throw a forbidden_error
 * If succesfully added, return true
 * If not, return false
 */

function add($author, $project, $branch, $loggedUser)
{
    check_not_null($author, $project, $branch, $loggedUser);
    if (! check_project_exist($author, $project)) {
        arg_error();
    }
    if (!admin_or_contributor($author, $project, $loggedUser)) {
        forbidden_error(); //We do not have the right --> FORBIDDEN
    }
    //We are an either an admin, the creator or a contributor. We can add a branch

    //Always executed
    $sql = "INSERT INTO branch VALUES (:name, updatedat, :authorname, :projectname)";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':name', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':apdatedat', "CURRENT_TIMESTAMP", \PDO::PARAM_STR); //Let see !
    $stmt->bindValue(':authorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_INT);
    return $stmt->execute();

}
/**
 * Remove a branch from a project
 * If the project does not exist, throw project_error()
 * If we do not have the rights, we throw an forbidden_error
 * If we are trying to remove the main branch, we throw a request_error
 * If project is not found, return a
 * If succesfully removed, return true
 * If faild to remove, return false
 */
function remove($author, $project, $branch, $loggedUser)
{
    // ne pas supprimer la branche principale
    if (! check_project_exist($author, $project)) {
        project_error();
    }

    if (!admin_or_contributor($author, $project, $loggedUser)) {
        forbidden_error(); //We do not have the right to remove a branch --> FORBIDDEN
    }
    //Checking the name of the main branch. If we are trying to delete it, throw an request_error
    $sql = "SELECT mainbranchname FROM Project WHERE name = :pname AND authorname = :pauthorname";
    //getting the main branch name of our project
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $i_got_a_result = $stmt->execute();
    if (!$i_got_a_result) {
        /**
         * Something went wrong, but idk what to do
         */

    }
    $i = 0;
    foreach ($stmt->fetchAll() as $res) { //Should be only one result, but anyway i'm looping for safety reasons
        $i = $i + 1; //Counter
        if (strcmp($res['mainbranchname'], $branch) == 0) //Check if strings are equal
        {
            //Yes ? We are trying to delete the main branch
            request_error();
        }
    }
    if ($i == 0) {
        /**
         * We found no project with our args (SQL returned no results)-> Throw an error
         */
        arg_error();
    }

    /**
     * If we are here, we should be able to remove the branch
     */
    $sql = "DELETE FROM branch WHERE name = :bname AND pojectname = :pname AND authorname = :pauthorname";
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    return $stmt->execute();

}

/**
 * Rename a branch from a project
 * If project does not exist, throw project_error
 * If you do not have the rights to do so -> throw a forbidden error
 * If the branch you are trying to rename does not exist -> Throw an arg_error
 * Return true if succesfully removed, false if not
 */
function rename($author, $project, $branch, $loggedUser, $new_branch_name)
{
    if (! check_project_exist($author, $project)) {
        project_error();
    }
    if (!admin_or_contributor($author, $project, $loggedUser)) {
        forbidden_error(); //We do not have the right to rename a branch --> FORBIDDEN
    }
    /**
     * Checking if a branch with name $branch exist in the project $project made by $author
     */
    $sql = "SELECT name FROM branch WHERE projectname = :pname AND authorname = :pauthorname";
    //getting the main branch name of our project
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->execute();
    $i_found_the_branch = false;
    foreach ($stmt->fetchAll() as $res) {
        if (strcmp($res['name'], $branch) == 0) //Check if strings are equal
        {
            //Yes ? We found the branch we are trying to rename
            $i_found_the_branch = true;
        }
    }
    if (!$i_found_the_branch) {
        //No match (Like me in Tinder) for the requested branch -> Throw an arg_error
        arg_error();
    }

    $sql = "UPDATE branch SET name = :new_branch_name WHERE name = :bname AND projectname = :pname AND authorname = :pauthorname ";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':new_branch_name', $new_branch_name, \PDO::PARAM_STR);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    return $stmt->execute();

}
/**
 *  gather all branches from a defined project
 *  If you request a project that does not exist, throw a project_error
 *  If you dont have the rights, show only public projects
 *  If you have the rights, show private projects
 *  if argument are not valid, throw
 */
function fetchAllFromProject($first, $after, $author, $project, $loggedUser)
{
    check_not_null($first, $after, $author, $project, $loggedUser);

    if (! check_project_exist($author, $project)) {
        project_error();
    }
    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "SELECT name
        FROM branch b
        JOIN Projet p ON
        b.projectname = p.name and b.authorname = p.name
        WHERE b.projectname = :pname AND b.authorname = :pauthorname
        LIMIT :number_to_show OFFSET :offset ";
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "SELECT name
        FROM branch b
        JOIN Projet p
        ON b.projectname = p.name and b.authorname = p.name
        WHERE b.projectname = :pname AND b.authorname = :pauthorname AND p.private = 'f'
        LIMIT :number_to_show OFFSET :offset ";
    }
    //first is the first we want to see
    //after is the last we want to see

    if ($first < 0 || $after < 0) {
        arg_error();
    }

    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':number_to_show', $first, \PDO::PARAM_INT);
    $stmt->execute();
    foreach ($stmt->fetchAll() as $branch) {
        $branchs[] = (object) [
            'name' => $branch['name'],
        ];
    }
    return $branchs;

    //Gérer cas projets privés et publics --> Should be done
}
/**
 * Count the number of branches for a given project
 * Throw project_error if the specified project does not exist
 * If you have the rights for a given project(eg: you are an admin or you are a contributor of this project), count even if project is private
 * If you do not have them however, should return 0 if private and the actual number of branches if public (= !private)
 * Return -1 if not able to count
 */
function countFromProject($author, $project, $loggedUser)
{
    check_not_null($author, $project, $loggedUser);

    if (! check_project_exist($author, $project)) {
        project_error();
    }
    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "COUNT(*)
        FROM branch b
        JOIN Projet p ON
        b.projectname = p.name and b.authorname = p.name
        WHERE b.projectname = :pname AND b.authorname = :pauthorname";
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "COUNT(*)
        FROM branch b
        JOIN Projet p
        ON b.projectname = p.name and b.authorname = p.name
        WHERE b.projectname = :pname AND b.authorname = :pauthorname AND p.private = 'f' ";
    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    if ($stmt->execute()) {
        foreach ($stmt->fetchAll() as $res) { //Should be only one result, but anyway i'm looping for safety reasons
            return $res[0];
        }
    } else return -1 ;
    // Gérer cas projets privés et publics
}
/**
 * Cherche toutes les versions d'un certain projet qui ressemblent à $version
 * 
 */
function seekVersion($first, $after, $author, $project, $branch, $version, $loggedUser)
{
    check_not_null($first, $after, $author, $project, $branch, $version, $loggedUser);
    if (! check_project_exist($author, $project)) {
        project_error();
    }
    if (!admin_or_contributor($author, $project, $loggedUser)) {
        forbidden_error(); //We do not have the right to seek for Version a branch --> FORBIDDEN
    }


    // Gérer cas projets privés et publics, $partitions unique pour une version fixée. Utiliser LIKE %$version%
}
