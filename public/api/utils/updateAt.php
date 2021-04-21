<?php

require_once "./error.php";

use \Exception;
use \PDOException;

function change_updatedAt_project($project, $authorname)
{
    check_not_null($project, $authorname);
    //This test shouldn't be required since it's already verified when calling the function 
    // Uncomment if you may call it without checking before calling


    // if (! check_branch_exist($authorname, $project,$branch)){
    //     //Cannot change it if it does not exist
    //     branch_error();
    // }

    try {
        //Establish connection
        $bd = connect();
    } catch (Exception $e) {
        echo "Failed: Cannot establish connection to the database" . $e->getMessage();
        PDO_error();
    }

    $sql = "UPDATE project
    SET updatedAt = :timestamp 
    WHERE name = :pname
    AND authorName = :pauthorname ";
    try {
        //Not sure that it throw something but more prudent
        $stmt = $bd->prepare($sql);
    } catch (PDOException $e) {
        echo "Failed: Cannot prepare request" . $e->getMessage();
        PDO_error();
    }
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $authorname, \PDO::PARAM_STR);
    $stmt->bindValue(':timestamp', "CURRENT_TIMESTAMP", \PDO::PARAM_STR);

    return $stmt->execute();
}

/**
 * Udate the updatedAt attribute on a selected branch with CURRENT_TIMESTAMP
 * 
 * It handle: Nothing particular
 */
function change_updatedAt_branch($project, $authorname, $branch)
{


    check_not_null($project, $authorname, $branch);
    //This test shouldn't be required since it's already verified when calling the function 
    // Uncomment if you may call it without checking before calling

    // if (!check_branch_exist($authorname, $project, $branch)) {
    //     //Cannot change it if it does not exist
    //     branch_error();
    // }

    try {
        //Establish connection
        $bd = connect();
    } catch (Exception $e) {
        echo "Failed: Cannot establish connection to the database" . $e->getMessage();
        PDO_error();
    }

    $sql = "UPDATE branch
    SET updatedAt = :timestamp 
    WHERE name = :bname
    AND projectName = :projectname
    AND authorName = :pauthorname ";
    try {
        //Not sure that it throw something but more prudent
        $stmt = $bd->prepare($sql);
    } catch (PDOException $e) {
        echo "Failed: Cannot prepare request" . $e->getMessage();
        PDO_error();
    }
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $authorname, \PDO::PARAM_STR);
    $stmt->bindValue(':timestamp', "CURRENT_TIMESTAMP", \PDO::PARAM_STR);

    return $stmt->execute();
}
