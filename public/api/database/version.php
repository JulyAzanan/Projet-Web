<?php
namespace Version;

include_once "config.php";
include_once "partition.php"; //Useless or not ? 

/**
 * Ajoute les partitons contenues dans l'argument $partitions à la table partition
 * S'occupe de creer une nouvelle version, avec un identifiant aléatoire de 10 charactères
 * Return tue if ALL artiton have been added, else False
 * Throw :
 * branch_error if given project/branch does not exist
 * forbidden_error if user dont have the right to add a version 
 * arg_error if $partitions is empty (it is not allowed to create a version without any partition in it)
 */
function add($author, $project, $branch, $partitions, $loggedUser)
{
    /* partitions est un tableau de : {
        name: string,
        content: string
    } 
    */
    check_not_null($author,$project,$branch,$partitions,$loggedUser);

    if (! check_branch_exist($author, $project,$branch)){
        //La brnahce voule n'existe pas/le projet n'existe pas
        branch_error();
    }
    if (! admin_or_contributor($author,$project,$loggedUser)){
        //We are not allowed to add a version. Aborting
        forbidden_error();
    }
    //Adding version $version the the specified project


    //testing if we are not trying to create an empty version ($partitions being empty)
    $counter = 0;
    foreach($partitions as $partition){ //Absolutely disgusting but should work :/ 
        $counter += 1 ;
    }
    if ($counter <= 0){
        arg_error();
    }
    //We are not trying to add empty version, let's create one version first

    //generating a random id for this version
    $length = 10;    
    $version = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,$length); //Should be enough

    /**
     * Else we can use this:
     * $version = md5(microtime())
     */
    $sql = "INSERT INTO version
    VALUES (:id , :createdAt , :authorname, :projectname, :branchname)";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':id', $version, \PDO::PARAM_STR);
    $stmt->bindValue(':createdAt', "CURRENT_TIMESTAMP", \PDO::PARAM_STR); 
    $stmt->bindValue(':authorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':projectname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':branchname', $branch, \PDO::PARAM_STR);
    if (! $stmt->execute()){
        //cannot execute the request. Aborting
        PDO_error();
    }
    //For each partition, we add it into the partition table in our database
    $reussite = true;
    foreach($partitions as $partition){
        $partitionName = $partition['name'];
        $content = $partition['content'] ;
        $reussite = $reussite & (\Partition\add($author, $project, $branch,$partitionName,$version,$content,$loggedUser) );
    }
    return($reussite);
}

function fetchAllFromProject($first, $after, $author, $project,$order, $loggedUser)
{
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour order by createdAt asc ou desc
    check_not_null($first, $after, $author, $project,$order, $loggedUser);
    if (! check_project_exist($author, $project)){
        project_error();
    }

    if ($order){
        $realOrder = "ASC" ;
    } else {
        $realOrder = "DESC";
    } 

    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "SELECT id,branchName
        FROM version v
        WHERE v.projectName = :pname 
        AND v.authorName = :pauthorname
        ORDER BY branchName :order
        LIMIT :number_to_show OFFSET :offset ";


    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "SELECT id,branchName
        FROM version v
        JOIN project p 
        ON
        v.projectName = p.name AND v.authorName = p.name
        WHERE v.projectName = :pname 
        AND v.authorName = :pauthorname
        AND p.private = 'f'
        ORDER BY branchName :order 
        LIMIT :number_to_show OFFSET :offset "; //Here, only public project are listed

    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $version, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $first, \PDO::PARAM_INT);
    $stmt->bindValue(':order', $realOrder, \PDO::PARAM_STR);
    if (! $stmt->execute()){
        //Request encoutered an error, aborting
        PDO_error();
    }
    foreach ($stmt->fetchAll() as $version) {
        $versions[] = (object) [
            'name' => $version['name'],
            'content' => $version['branchName'],
        ];
    }
    return $versions;
    
}

function fetchAllFromBranch($first, $after, $author, $project, $branch,$order, $loggedUser)
{
    check_not_null($first, $after, $author, $project,$branch,$order, $loggedUser);
    if (! check_branch_exist($author, $project,$branch)){
        branch_error();
    }
    if ($order){
        $realOrder = "ASC" ;
    } else {
        $realOrder = "DESC";
    } 

    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "SELECT id,branchName
        FROM version v
        WHERE v.projectName = :pname 
        AND v.authorName = :pauthorname
        AND v.branchName = :bname
        ORDER BY branchName :order
        LIMIT :number_to_show OFFSET :offset ";

        //Fuck off @July for putting so long SQL Requests 
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "SELECT id,branchName
        FROM version v
        JOIN project p 
        ON
        pa.projectName = p.name AND pa.authorName = p.name
        WHERE v.projectName = :pname 
        AND v.authorName = :pauthorname
        AND v.branchName = :bname
        AND p.private = 'f'
        ORDER BY branchName :order
        LIMIT :number_to_show OFFSET :offset ";//Here, only public project are listed

    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $version, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $first, \PDO::PARAM_INT);
    $stmt->bindValue(':order', $realOrder, \PDO::PARAM_STR);
    if (! $stmt->execute()){
        //Request encoutered an error, aborting
        PDO_error();
    }
    foreach ($stmt->fetchAll() as $version) {
        $versions[] = (object) [
            'name' => $version['name'],
            'content' => $version['branchName'],
        ];
    }
    return $versions;
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour order by createdAt asc ou desc
}

function countFromProject($author, $project, $loggedUser)
{
    // Gérer cas projets privés et publics
    check_not_null($author, $project, $loggedUser);
    if (! check_project_exist($author, $project)){
        project_error();
    }



    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "COUNT(*)
        FROM version v
        WHERE v.projectName = :pname 
        AND v.authorName = :pauthorname
        ORDER BY branchName :order
        LIMIT :number_to_show OFFSET :offset ";

        //Fuck off @July for putting so long SQL Requests 
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "COUNT (*)
        FROM version v
        JOIN project p 
        ON
        pa.projectName = p.name AND pa.authorName = p.name
        WHERE v.projectName = :pname 
        AND v.authorName = :pauthorname
        AND p.private = 'f'
        ORDER BY branchName :order 
        LIMIT :number_to_show OFFSET :offset "; //Here, only public project are listed

    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $version, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    
    if (! $stmt->execute()){
        //Request encoutered an error, aborting
        PDO_error();
    }
    foreach ($stmt->fetchAll() as $res) {
        return $res[0]; //The count requested
    }
    
}

function countFromBranch($author, $project, $branch, $loggedUser)
{
    // Gérer cas projets privés et publics
    check_not_null($author, $project, $loggedUser);
    if (! check_project_exist($author, $project)){
        project_error();
    }



    if (admin_or_contributor($author, $project, $loggedUser)) {
        /**
         * We are an admin and/or a contributor -> W
         */
        $sql = "COUNT(*)
        FROM version v
        WHERE v.projectName = :pname 
        AND v.authorName = :pauthorname
        AND v.branchName = bname
        ORDER BY branchName :order
        LIMIT :number_to_show OFFSET :offset ";

        //Fuck off @July for putting so long SQL Requests 
    } else {
        /**
         * We are not an admin and we are not a contributor
         * So we
         */
        $sql = "COUNT (*)
        FROM version v
        JOIN project p
        ON v.projectName = p.name 
        AND v.authorName = p.authorName
        WHERE v.projectName = :pname 
        AND v.authorName = :pauthorname
        AND v.branchName = bname
        AND p.private = 'f'
        ORDER BY branchName :order 
        LIMIT :number_to_show OFFSET :offset "; //Here, only public project are listed

    }
    $bd = connect();
    $stmt = $bd->prepare($sql);
    // ($first, $after, $author, $project, $version, $loggedUser)
    $stmt->bindValue(':pname', $project, \PDO::PARAM_STR);
    $stmt->bindValue(':pauthorname', $author, \PDO::PARAM_STR);
    $stmt->bindValue(':bname', $branch, \PDO::PARAM_STR);
    if (! $stmt->execute()){
        //Request encoutered an error, aborting
        PDO_error();
    }
    foreach ($stmt->fetchAll() as $res) {
        return $res[0]; //The count requested
    }
}

/**
 * Search for all Version that look like $version
 * On succes, return an a object array-like taht contains all requestedversions
 * It handle:
 * Permission. If we are an admin of the selected project, then we can seek all versions
 * regardless if the project is public or private
 * If we are not, we can only seek if the project is public
 * It throw:
 * branch_error if the request branch does not exist
 * PDO_error if the request cannot be executed
 * 
 */
function seekVersion($first, $after, $author, $project, $branch, $version, $loggedUser)
{
    check_not_null($first, $after, $author, $project, $branch, $version, $loggedUser);
    if (!check_project_exist($author, $project)) {
        project_error();
    }
    if (!admin_or_contributor($author, $project, $loggedUser)) {
        forbidden_error(); //We do not have the right to seek for Version a branch --> FORBIDDEN
    }


    // Gérer cas projets privés et publics, $partitions unique pour une version fixée. Utiliser LIKE %$version%
}

