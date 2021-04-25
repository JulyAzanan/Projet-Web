<?php
namespace Partition;

include_once __DIR__ . "/config.php";

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
function add($author, $project, $branch, $partition, $commit, $content, $loggedUser)
{
    check_not_null($author, $project, $branch, $partition, $commit, $content, $loggedUser);
    if (!check_branch_exist($author, $project, $branch)) {
        //Requested branch does not exist, aborting
        branch_error();
    }
    if (!admin_or_contributor($author, $project, $loggedUser)) {
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
    if (!change_updatedAt_branch($project, $author, $branch)) {
        return false;
    }
    return $stmt->execute();
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
function fetchAll($author, $project, $branch, $commit, $loggedUser)
{
    // Gérer cas projets privés et publics --Should be done
    check_not_null($author, $project, $branch, $commit);
    if (!check_branch_exist($author, $project, $branch)) {
        branch_error();
    }
    $sql = "SELECT DISTINCT ON(p.name) p.name, c.id, c.createdAt, p.content FROM partition p JOIN commit c
    ON p.commitID = c.id AND p.authorName = c.authorName AND p.branchName = c.branchName AND p.projectName = c.projectName
    JOIN project pp ON pp.name = p.projectName AND pp.authorName = p.authorName
    WHERE p.authorName = :authorName AND p.projectName = :projectName AND p.branchName = :branchName AND c.createdAt <= (SELECT cc.createdAt FROM commit cc WHERE cc.authorName = :authorName AND cc.projectName = :projectName AND cc.branchName = :branchName AND cc.id = :id)
    AND ( pp.private = 'f' OR :loggedUser IN (SELECT ccc.contributorName FROM contributor ccc WHERE ccc.projectName = p.projectName AND ccc.authorName = p.authorName) OR p.authorName = :loggedUser OR :loggedUser = 'admin' )
    ORDER BY p.name ASC, c.createdAt DESC";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $commit, $loggedUser)
    $stmt->bindValue(':projectName', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':authorName', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':branchName', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':id', $commit, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //Request encoutered an error, aborting
        PDO_error();
    }
    foreach ($stmt->fetchAll() as $partition) {
        $partitions[] = (object) [
            'name' => $partition['name'],
            'id' => $partition['id'],
            'createdAt' => $partition['createdat'],
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
function count($author, $project, $branch, $commit, $loggedUser)
{
    // Gérer cas projets privés et publics --Should be done
    check_not_null($author, $project, $branch, $commit);
    if (!check_branch_exist($author, $project, $branch)) {
        branch_error();
    }

    $sql = "SELECT COUNT(*) FROM partition p JOIN project pp
    ON pp.name = p.projectName AND pp.authorName = p.authorName
    WHERE p.authorName = :authorName AND p.projectName = :projectName AND p.branchName = :branchName AND p.commitID = :id
    AND ( pp.private = 'f' OR :loggedUser IN (SELECT ccc.contributorName FROM contributor ccc WHERE ccc.projectName = p.projectName AND ccc.authorName = p.authorName) OR p.authorName = :loggedUser OR :loggedUser = 'admin' )  ";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $commit, $loggedUser)
    $stmt->bindValue(':projectName', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':authorName', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':branchName', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':id', $commit, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //Request encoutered an error, aborting
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $res['count'];
}
