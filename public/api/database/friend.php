<?php
namespace Friend;

include_once "config.php";
/**
 * Add the personn $follower to the list of follower that follow $following 
 * 
 * It handle:
 * 
 * Permission: If you are an admin, you can add to the list of persons 
 * following $following anyone, even if it's not you you can
 * 
 * If not, only added if $loggedUser = $follower
 * 
 * It throw:
 * 
 * forbidden_error if you dont have the right to add ($follower != $loggeduser and != admin) 
 * 
 * It return: 
 * 
 * true on succes, false on failure
 */
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
    //Will return true on succes and false on failure
}
/**
 * Remove the personn $follower to the list of follower that follow $following
 * 
 * It handle:
 * 
 * Permission: If you are an admin, you can aremove of the list of persons 
 * following $following anyone, even if it's not you you can
 * 
 * If not, only removed if $loggedUser = $follower
 * 
 * It throw:
 * 
 * forbidden_error if you dont have the right to add ($follower != $loggeduser and != admin) 
 * 
 * It return: 
 * 
 * true on succes, false on failure
 */
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
    $sql = "DELETE FROM friends WHERE followerName = :fname AND followingName = :fingname ";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':fname', $follower, \PDO::PARAM_STR);
    $stmt->bindValue('fingname', $following, \PDO::PARAM_STR); 
    return $stmt->execute();
    //Will return true on succes and false on failure
}


/**
 * Gather all the persons that $user is following
 * 
 * It handle:
 * 
 * Limits with $first and $after. Start at $first and show $after people after that
 * 
 * It DOES NOT handle:
 * 
 * Permission: AnyOne can see the list of people that $user is following
 * 
 * It throw:
 * 
 * PDO_ERROR() if the request failed to execute
 * 
 * It return: 
 * 
 * an objet , array-like that contains the name of all people that
 * $user is following 
 */
function fetchAllFromUser($first, $after, $user)
{
    //first is the first we want to see
    //after is the number after first we want to see
    check_not_null($first,$after,$user);
    if ($first < 0 || $after < 0) {
        //Cannot use negative number here 
        arg_error();
    }
    //Creating the request
    $sql = "SELECT followingName
        FROM friend
        WHERE followerName = :fname 
        LIMIT :number_to_show OFFSET :offset " ;
        //Binding values
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':fname', $user, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $after, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $first, \PDO::PARAM_INT);
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
/**
 * Gather all the persons that $user is following
 * 
 * It DOES NOT handle:
 * 
 * Permission: AnyOne can see the count of people that $user is following
 * 
 * It throw:
 * 
 * PDO_ERROR() if the request failed to execute
 * 
 * It return: 
 * 
 * an integer ,that count the number of people that
 * $user is following 
 */
function countFromUser($user)
{
    check_not_null($user);//Required here
    //Creating the request
    $sql = "SELECT COUNT(*)
        FROM friend
        WHERE followerName = :fname AND
        LIMIT :number_to_show OFFSET :offset " ;
        //Binding the request
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':fname', $user, \PDO::PARAM_STR);
    //Executing
    if (! $stmt->execute()){
        //An error occured
        PDO_error() ;
    }
    //Executed Fine, getting the result
    foreach ($stmt->fetchAll() as $friend) {
        //Should be only one result, but idk how to do it so i'm using this for safety 
        return($friend[0]) ;
    }
}
