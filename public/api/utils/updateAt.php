<?php

require_once __DIR__ . "/error.php";

function change_updatedAt_project($project, $authorname)
{
    check_not_null($project, $authorname);
    //This test shouldn't be required since it's already verified when calling the function
    // Uncomment if you may call it without checking before calling

    // if (! check_branch_exist($authorname, $project,$branch)){
    //     //Cannot change it if it does not exist
    //     branch_error();
    // }

    $bd = connect();

    $sql = "UPDATE project
    SET updatedAt = CURRENT_TIMESTAMP
    WHERE name = :pname
    AND authorName = :pauthorname ";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $authorname, \PDO::PARAM_STR);

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

    /* if (!check_branch_exist($authorname, $project, $branch)) {
    //Cannot change it if it does not exist
    branch_error();
    } */
    $bd = connect();

    $sql = "UPDATE branch
    SET updatedAt = CURRENT_TIMESTAMP
    WHERE name = :bname
    AND projectName = :projectname
    AND authorName = :pauthorname ";
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $authorname, \PDO::PARAM_STR);

    return $stmt->execute() && change_updatedAt_project($project, $authorname);
}
