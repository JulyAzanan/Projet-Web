<?php
namespace Partition;

include_once "config.php";

/**
 * Add a partition ($partition,$content) to a commit of a branch of a project, 
 * identified by $authorName, $project, $branch and $commit
 * 
 * It handle: 
 *
 * Permissions : If you are a contributor or an admin, you can add a partition
 * 
 * If not, throw an forbidden_error
 * 
 * It throw:
 * 
 * branch if the selected branch does not exist
 * 
 * It return :
 * 
 * true on succes, false on failure
 */
function add(string $author,string $project,string $branch, $partition,string $commit ,string $content,string $loggedUser)
{
    check_not_null($author, $project, $branch, $partition,$commit, $content, $loggedUser);
    if (! check_branch_exist($author,$project,$branch)){
        //Requested branch does not exist, aborting 
        branch_error();
    }
    if(! admin_or_contributor($author,$project,$loggedUser)){
        //We are not an admin, we dont have the rights to add the partition 
        forbidden_error();
    }
    /** 
     * Creating a pseudo random commitID that relate to github's commit ID
    */
    
    $sql = "INSERT INTO partition
    VALUES (:partname , :content , :authorname, :projectname, :branchname, :commitID)";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':partname', $partition, \PDO::PARAM_STR);
    $stmt->bindValue(':content', $content, \PDO::PARAM_STR);
    $stmt->bindValue(':authorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':branchname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':commitID', $commit, \PDO::PARAM_STR);
    if (!change_updatedAt_project($project,$author)){
        return false;
    };
    if (!change_updatedAt_branch($project,$author,$branch)){
        return false;
    };
    return($stmt->execute());
}


/**
 * Return all partition (name+data) in an object of a given 
 * project ($project) made by ($author), on branch ($branch) and with commit ($commit)
 * 
 * It handle:
 * 
 * Permissions : private projects are only considered if the $loggedUser is a contributor of this project or an admin
 * 
 * else, only public ones are considered
 * 
 * It throw :
 * 
 * branch_error if the project or the branch does not exist
 * 
 * pdo_error if failed to execute the request
 * 
 * It return:
 * 
 * An array-like object that contain all the partition(name + content) of the requested commit
 */
function fetchAllFromCommit(int $first,int $after,string $author,string $project,string $branch,string $commit,string $loggedUser)
{
    // Gérer cas projets privés et publics --Should be done
    check_not_null($first, $after, $author, $project,$branch, $commit, $loggedUser);
    if (! check_branch_exist($author, $project,$branch)){
        branch_error();
    }
    $sql = "";
    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "SELECT name,content
        FROM partition pa
        WHERE pa.projectName = :pname 
        AND pa.authorName = :pauthorname
        AND pa.branchName = :bname
        AND pa.commitID = :branchid
        LIMIT :number_to_show OFFSET :offset ";


    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "SELECT name,content
        FROM partition pa
        JOIN project p 
        ON
        pa.projectName = p.name AND pa.authorName = p.authorName
        WHERE pa.projectName = :pname 
        AND pa.authorName = :pauthorname
        AND pa.branchName = :bname
        AND pa.commitID = :branchid
        AND p.private = 'f' 
        LIMIT :number_to_show OFFSET :offset ";

    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $commit, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':branchid', $commit, \PDO::PARAM_STR);
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
/**
 * Count the number of partitions of
 * project ($project) made by ($author), on branch ($branch) and with commit ($commit)
 * 
 * It handle:
 * 
 * Permissions : number of partitions of private projects are only counted if the $loggedUser is a contributor of this project or an admin
 * 
 * else, only partition from public project are counted
 * 
 * It throw :
 * 
 * branch_error if the project or the branch does not exist
 * 
 * pdo_error if failed to execute the request
 * 
 * It return:
 * 
 * An array-like object that contain all the partition(name + content) of the requested commit
 */
function countFromCommit(int $first,int $after,string $author,string $project,string $branch,string $commit,string $loggedUser)
{
    // Gérer cas projets privés et publics --Should be done
    check_not_null($first, $after, $author, $project,$branch, $commit, $loggedUser);
    if (! check_branch_exist($author, $project,$branch)){
        branch_error();
    }
    $sql = "";
    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "SELECT COUNT(*)
        FROM partition pa
        WHERE pa.projectName = :pname 
        AND pa.authorName = :pauthorname
        AND pa.branchName = :bname
        AND pa.commitId = :branchid
        LIMIT :number_to_show OFFSET :offset ";


    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "SELECT COUNT(*)
        FROM partition pa
        JOIN project p 
        ON
        pa.projectName = p.name AND pa.authorName = p.authorName
        WHERE pa.projectName = :pname 
        AND pa.authorName = :pauthorname
        AND pa.branchName = :bname
        AND pa.commitId = :branchid
        AND p.private = 'f'
        LIMIT :number_to_show OFFSET :offset "; //Here, only public project are listed

    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $commit, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':branchid', $commit, \PDO::PARAM_STR);
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
/**
 * Return all partition (name+data) in an object of a given that contain $partition in their name from 
 * project ($project) made by ($author), on branch ($branch) and with commit ($commit)
 * 
 * 
 * /!\ $partition should only contain the name of teh partition, not the content
 * 
 * It handle:
 * 
 * Permissions : private projects are only considered if the $loggedUser is a contributor of this project or an admin
 * 
 * else, only public ones are considered
 * 
 * It throw :
 * 
 * branch_error if the project or the branch does not exist
 * 
 * pdo_error if failed to execute the request
 * 
 * It return:
 * 
 * An array-like object that contain all the partition(name + content) 
 * of the requested commit that have their name like $partition
 */
function seekPartition(int $first,int $after,string $author,string $project,string $branch,string $commit,string $partition,string $loggedUser)
{
    // Gérer cas projets privés et publics, $partitions unique pour une commit fixée. Utiliser LIKE %$partition%
    check_not_null($first, $after, $author, $project, $branch, $commit, $partition, $loggedUser);
    if (! check_branch_exist($author, $project,$branch)){
        branch_error();
    }
    $sql = "";
    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "SELECT name
        FROM partition pa
        WHERE pa.projectName = :pname 
        AND pa.authorName = :pauthorname
        AND pa.branchName = bname
        AND pa.commitID = :commitID
        AND pa.name LIKE %:partitionname% ";
        //Let see if it works


    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we select only public projects
         */
        $sql = "SELECT name
        FROM partition pa
        JOIN project p 
        ON
        pa.projectName = p.name AND pa.authorName = p.authorName
        WHERE pa.projectName = :pname 
        AND pa.authorName = :pauthorname
        AND pa.branchName = bname
        AND pa.commitID = :commitID
        AND pa.name LIKE %:partitionName%
        AND p.private = 'f' "; //Here, only public project are listed

    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $commit, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':commitID', $commit, \PDO::PARAM_STR);
    $stmt->bindValue(':partitionName', $partition, \PDO::PARAM_STR);
    if (! $stmt->execute()){
        //Request encoutered an error, aborting
        PDO_error();
    }
    foreach ($stmt->fetchAll() as $res) { //Ading them all into one object
        $rescommits[] = (object) [
            'name' => $res['name'],
        ];
    }
    return $rescommits;
}

