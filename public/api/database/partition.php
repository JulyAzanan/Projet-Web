<?php
namespace Partition;

include_once "config.php";

function add($author, $project, $branch, $partition, $content, $loggedUser)
{
    check_not_null($author, $project, $branch, $partition, $content, $loggedUser);
    if (! check_branch_exist($author,$project,$branch)){
        //Requested branch does not exist, aborting 
        arg_error();
    }
    if(! admin_or_contributor($author,$project,$loggedUser)){
        //We are not an admin, we dont have the rights to add the partition 
        forbidden_error();
    }
    /** 
     * Creating a pseudo random versionID that relate to github's commit ID
    */
    $length = 10;    
    $commit_id = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,$length); //Should be enough

    /**
     * Else we can use this:
     * $commit_id = md5(microtime())
     */
    $sql = "INSERT INTO partition
    VALUES (:partname , :content , :authorname, :projectname, :branchname, :versionID)";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':partname', $partition, \PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, \PDO::PARAM_STR);
    $stmt->bindValue(':authorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':branchname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':versionID', $commit_id, \PDO::PARAM_STR);
    return($stmt->execute());
}


/**
 * Return all partition (name+data) in an object of a given 
 * project ($project) made by ($author), on branch ($branch) and with version ($version)
 * It handle private and public project :
 * private projects are only listed if the $loggedUser is a contributor of this project or an admin
 * else, only public ones are listed
 * It throw :
 * branch_error if the project or the branch does not exist
 * pdo_error if failed to execute the request
 */
function fetchAllFromVersion($first, $after, $author, $project,$branch, $version, $loggedUser)
{
    // Gérer cas projets privés et publics --Should be done
    check_not_null($first, $after, $author, $project,$branch, $version, $loggedUser);
    if (! check_branch_exist($author, $project,$branch)){
        branch_error();
    }

    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "SELECT name,content
        FROM version v
        JOIN project p 
        ON
        v.projectName = p.name AND v.authorName = p.name
        JOIN branch b
        ON v.projectName = b.projectName AND v.authorName = b.authorName AND v.branchName = b.name
        WHERE v.projectname = :pname 
        AND v.authorname = :pauthorname
        AND v.branchName = :bname
        AND v.id = :branchid
        LIMIT :number_to_show OFFSET :offset ";

        //Fuck off @July for putting so long SQL Requests 
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "SELECT name,content
        FROM version v
        JOIN project p 
        ON
        v.projectName = p.name AND v.authorName = p.name
        JOIN branch b
        ON v.projectName = b.projectName AND v.authorName = b.authorName AND v.branchName = b.name
        WHERE v.projectname = :pname 
        AND v.authorname = :pauthorname
        AND v.branchName = :bname
        AND v.id = :branchid
        AND p.private = 'f' 
        LIMIT :number_to_show OFFSET :offset "; //Here, only public project are listed

    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $version, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':branchid', $version, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $first, \PDO::PARAM_INT);
    if (! $stmt->execute()){
        //Request encoutered an error, aborting
        PDO_error();
    }
    foreach ($stmt->fetchAll() as $partition) {
        $partitions[] = (object) [
            'name' => $partition['name'],
            'content' => $partition['content'],
        ];
    }
    return $partitions;

}

function countFromVersion($first, $after, $author, $project, $branch, $version, $loggedUser)
{
    // Gérer cas projets privés et publics --Should be done
    check_not_null($first, $after, $author, $project,$branch, $version, $loggedUser);
    if (! check_branch_exist($author, $project,$branch)){
        branch_error();
    }

    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "COUNT(*)
        FROM version v
        JOIN project p 
        ON
        v.projectName = p.name AND v.authorName = p.name
        JOIN branch b
        ON v.projectName = b.projectName AND v.authorName = b.authorName AND v.branchName = b.name
        WHERE v.projectname = :pname 
        AND v.authorname = :pauthorname
        AND v.branchName = :bname
        AND v.id = :branchid
        LIMIT :number_to_show OFFSET :offset ";

        //Fuck off @July for putting so long SQL Requests 
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "COUNT(*)
        FROM version v
        JOIN project p 
        ON
        v.projectName = p.name AND v.authorName = p.name
        JOIN branch b
        ON v.projectName = b.projectName AND v.authorName = b.authorName AND v.branchName = b.name
        WHERE v.projectname = :pname 
        AND v.authorname = :pauthorname
        AND v.branchName = :bname
        AND v.id = :branchid
        AND p.private = 'f' 
        LIMIT :number_to_show OFFSET :offset "; //Here, only public project are listed

    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $version, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':branchid', $version, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $first, \PDO::PARAM_INT);
    if (! $stmt->execute()){
        //Request encoutered an error, aborting
        PDO_error();
    }
    foreach ($stmt->fetchAll() as $partition) {
        return $partition[0]; //should work
    }
    
}
