<?php
namespace Commit;

include_once __DIR__ . "/config.php";
include_once __DIR__ . "/partition.php";
include_once __DIR__ . "/user.php";
include_once __DIR__ . "/../utils/args.php";
include_once __DIR__ . "/../utils/updateAt.php";

/**
 * Add all partition contained in the $partition arg to the table partion
 * . For each one, generad an unique string of 10 characters that identify it
 *
 * It handle:
 *
 * Permission : if you are an admin or a contributor, you can add, else, throw forbidden_error
 *
 *
 * It throw :
 *
 * branch_error if given project/branch does not exist
 *
 * forbidden_error if user dont have the right to add a commit
 *
 * arg_error if $partitions is empty (it is not allowed to create a commit without any partition in it)
 *
 * PDO_error if the request cannot be executed
 *
 * It return:
 *
 * True if all partition in $partitions has been added
 *
 * False if one or more failed to be added
 */
function add($author, $project, $branch, $message, $partitions, $loggedUser)
{
    /* partitions est un tableau de : {
    name: string,
    content: string
    }
     */
    check_not_null($author, $project, $branch, $partitions, $loggedUser);

    if (!check_branch_exist($author, $project, $branch)) {
        //La brnahce voule n'existe pas/le projet n'existe pas
        branch_error();
    }
    if (!admin_or_contributor($author, $project, $loggedUser)) {
        //We are not allowed to add a commit. Aborting
        forbidden_error();
    }
    //Adding commit $commit the the specified project

    //testing if we are not trying to create an empty commit ($partitions being empty)
    if (sizeof($partitions) == 0) {
        arg_error();
    }
    //We are not trying to add empty commit, let's create one commit first

    //generating a random id for this commit
    $commit = md5(microtime());

    $sql = "INSERT INTO commit
    VALUES (:id , CURRENT_TIMESTAMP , :authorname, :projectname, :branchname, :message, :publishername)";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':id', $commit, \PDO::PARAM_STR);
    $stmt->bindValue(':authorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':branchname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':message', $message, \PDO::PARAM_STR);
    $stmt->bindValue(':publishername', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //cannot execute the request. Aborting
        PDO_error();
    }
    //For each partition, we add it into the partition table in our database
    $reussite = true;
    foreach ($partitions as $partition) {
        $partitionName = $partition->name;
        $content = $partition->content;
        $reussite = $reussite && (\Partition\add($author, $project, $branch, $partitionName, $commit, $content, $loggedUser));
    }

    if (!change_updatedAt_branch($project, $author, $branch)) {
        return null;
    }
    return $reussite ? (object) ['id' => $commit] : null;
}

function getLatest($author, $project, $branch, $loggedUser) {
    check_not_null($author, $project, $branch);

    $sql = "SELECT id FROM commit c JOIN project p 
    ON p.name = c.projectName AND p.authorName = c.authorName
    WHERE c.authorName = :authorName AND c.projectName = :projectName AND c.branchName = :branchName
    AND (p.private = 'f' OR :loggedUser IN (SELECT cc.contributorName FROM contributor cc WHERE cc.projectName = c.projectName AND cc.authorName = c.authorName) OR c.authorName = :loggedUser OR :loggedUser = 'admin' )
    ORDER BY c.createdAt DESC
    LIMIT 1 OFFSET 0";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $commit, $loggedUser)
    $stmt->bindValue(':projectName', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':authorName', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':branchName', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //Request encoutered an error, aborting
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $stmt->rowCount() == 0 ? null : $res['id'];
}

function getCommit($author, $project, $branch, $commit, $loggedUser)
{
    $commitInfo = find($author, $project, $branch, $commit, $loggedUser);
    $commitInfo->files = \Partition\fetchAll($author, $project, $branch, $commit, $loggedUser);
    $commitInfo->publisher = \User\find($commitInfo->publisherName);
    return $commitInfo;
}

/**
 * Return all commit (id) from
 * project ($project) made by ($author), on branch ($branch)
 *
 * It handle:
 *
 * Permissions : private projects are only considered if the $loggedUser is a contributor of this project or an admin
 *
 * else, do it only if the project is public
 *
 * Order to show the result: Either ASC or DESC (param $order = TRUE for ASC, FALSE for DESC)
 *
 * It throw :
 *
 * branh_error if the project or the branch does not exist
 *
 * pdo_error if failed to execute the request
 *
 * It return:
 *
 * An array-like object that contain all the commit (id) of the requested project and requested branch
 */
function fetchAll($author, $project, $branch, $loggedUser)
{
    check_not_null($author, $project, $branch);
    if (!check_branch_exist($author, $project, $branch)) {
        branch_error();
    }

    $sql = "SELECT c.id, c.createdAt, c.message, c.publisherName FROM commit c JOIN project p
    ON p.name = c.projectName AND p.authorName = c.authorName
    WHERE c.projectName = :pname AND c.branchName = :bname AND c.authorName = :pauthorname
    AND ( p.private = 'f' OR :loggedUser IN (SELECT cc.contributorName FROM contributor cc WHERE cc.projectName = c.projectName AND cc.authorName = c.authorName) OR c.authorName = :loggedUser OR :loggedUser = 'admin' )";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $commit, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //Request encoutered an error, aborting
        PDO_error();
    }
    $commits = [];
    foreach ($stmt->fetchAll() as $commit) {
        $commits[] = (object) [
            'id' => $commit['id'],
            'createdAt' => $commit['createdat'],
            'message' => $commit['message'],
            'publisher' => $commit['publishername'],
        ];
    }
    return $commits;
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour order by createdAt asc ou desc
}

function find($author, $project, $branch, $commit, $loggedUser)
{
    check_not_null($author, $project, $branch, $commit);
    if (!check_branch_exist($author, $project, $branch)) {
        branch_error();
    }

    $sql = "SELECT c.id, c.createdAt, c.message, c.publisherName FROM commit c JOIN project p
    ON p.name = c.projectName AND p.authorName = c.authorName
    WHERE c.projectName = :pname AND c.branchName = :bname AND c.authorName = :pauthorname AND c.id = :commit
    AND ( p.private = 'f' OR :loggedUser IN (SELECT cc.contributorName FROM contributor cc WHERE cc.projectName = c.projectName AND cc.authorName = c.authorName) OR c.authorName = :loggedUser OR :loggedUser = 'admin' )";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $commit, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    $stmt->bindValue(':commit', $commit, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //Request encoutered an error, aborting
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($stmt->rowCount() === 0) {
        return null;
    }
    $c = (object) [
        'id' => $res['id'],
        'createdAt' => $res['createdat'],
        'message' => $res['message'],
        'publisherName' => $res['publishername'],
    ];
    return $c;
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour order by createdAt asc ou desc
}

/**
 * Count all commit (id) from
 * project ($project) made by ($author), in branch $branch
 *
 * It handle:
 *
 * Permissions : private projects are only considered if the $loggedUser is a contributor of this project or an admin
 *
 * else, do it only if the project is public
 *
 * It throw :
 *
 * branch_error if the project or the branch does not exist
 *
 * pdo_error if failed to execute the request
 *
 * It return:
 *
 * An integer that represents the number of commit in the requested project, on branch $branch
 */
function count($author, $project, $branch, $loggedUser)
{
    // Gérer cas projets privés et publics
    check_not_null($author, $project, $branch);
    if (!check_project_exist($author, $project)) {
        project_error();
    }

    $sql = "";

    $sql = "SELECT COUNT(*) FROM commit c JOIN project p
    ON p.name = c.projectName AND p.authorName = c.authorName
    WHERE c.projectName = :pname AND c.branchName = :bname AND c.authorName = :pauthorname
    AND ( p.private = 'f' OR :loggedUser IN (SELECT cc.contributorName FROM contributor cc WHERE cc.projectName = c.projectName AND cc.authorName = c.authorName) OR c.authorName = :loggedUser OR :loggedUser = 'admin' )";

    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $commit, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':loggedUser', $loggedUser, \PDO::PARAM_STR);
    if (!$stmt->execute()) {
        //Request encoutered an error, aborting
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $res['count'];

}
