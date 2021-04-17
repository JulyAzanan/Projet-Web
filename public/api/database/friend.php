<?php
namespace Friend;

include_once "config.php";

function add($follower, $following, $loggedUser)
{
    //Adding $follower to the list of personn following $following
    // if and only if $follower = $loggedUser or $loggedUser is an admin
    check_not_null($follower,$following,$loggedUser);
    
    if (check_owner_bool($follower, $loggedUser) ){
        //Return False if we are an admin or if $follower = $loggedUser
        //So here, we are not an admin
        forbidden_error();
    }

    //We have the rights to do it
    $sql = "INSERT INTO friend VALUES (:followername , :followingname)";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':followername', $follower, \PDO::PARAM_STR);
    $stmt->bindValue(':followingname', $following, \PDO::PARAM_STR); 
    return $stmt->execute();
}

function remove($follower, $following, $loggedUser)
{
    //Removing $follower to the list of personn following $following
    // if and only if $follower = $loggedUser or $loggedUser is an admin
    check_not_null($follower,$following,$loggedUser);
    
    if (check_owner_bool($follower, $loggedUser) ){
        //Return False if we are an admin or if $follower = $loggedUser
        //So here, we are not an admin
        forbidden_error();
    }

    //We have the rights to do it
    $sql = "DELETE FROM branch WHERE followerName = :fname AND followingName = :fingname ";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':fname', $follower, \PDO::PARAM_STR);
    $stmt->bindValue('fingname', $following, \PDO::PARAM_STR); 
    return $stmt->execute();
}


/**
 * Return an objet , array-like that contains the name of all people that
 * $user is following 
 * On fail throw PDO_ERROR()
 */
function fetchAllFromUser($first, $after, $user)
{
    //first is the first we want to see
    //after is the number after first we want to see
    check_not_null($first,$after,$user);
    if ($first < 0 || $after < 0) {
        arg_error();
    }
    $sql = "SELECT followingName
        FROM friend
        WHERE followerName = :fname AND
        LIMIT :number_to_show OFFSET :offset " ;
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':fname', $user, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':number_to_show', $first, \PDO::PARAM_INT);
    if (! $stmt->execute()){
        //An error occured
        PDO_error() ;
    }
    //Executed Fine, getting all of them into an object :)
    foreach ($stmt->fetchAll() as $friend) {
        $friends[] = (object) [
            'friend' => $friend['followingName'],
        ];
    }
    return $friends ;
}

function countFromUser($user)
{
    check_not_null($user);

    $sql = "COUNT(*)
        FROM friend
        WHERE followerName = :fname AND
        LIMIT :number_to_show OFFSET :offset " ;
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':fname', $user, \PDO::PARAM_STR);
    if (! $stmt->execute()){
        //An error occured
        PDO_error() ;
    }
    //Executed Fine, getting all of them into an object :)
    foreach ($stmt->fetchAll() as $friend) {
        //Should be only one result, but idk how to do it so i'm using this for safety 
        return($friend[0]) ;
    }
}
