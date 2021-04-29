<?php
namespace Friend;

include_once __DIR__ . "/config.php";
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
    check_not_null($follower, $following, $loggedUser);

    if (check_owner_bool($follower, $loggedUser)) {
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
    check_not_null($follower, $following, $loggedUser);

    if (check_owner_bool($follower, $loggedUser)) {
        //Return False if we are an admin or if $follower = $loggedUser
        //So here, we are not an admin
        forbidden_error();
    }

    //We have the rights to do it
    $sql = "DELETE FROM friend WHERE followerName = :fname AND followingName = :fingname ";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':fname', $follower, \PDO::PARAM_STR);
    $stmt->bindValue(':fingname', $following, \PDO::PARAM_STR);
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
function fetchAll($first, $after, $user)
{
    check_not_null($user);
    $bd = connect();
    $stmt = $bd->prepare("SELECT name, picture, bio, email, age, latestCommit, (SELECT COUNT(ff.followingName) FROM friend ff WHERE ff.followingName = name) AS followers
    FROM musician JOIN friend f ON f.followerName = name
    WHERE f.followingName = :user
    LIMIT :number_to_show OFFSET :offset ");
    $stmt->bindValue(':user', $user, \PDO::PARAM_STR);
    $stmt->bindValue(':number_to_show', $first, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $after, \PDO::PARAM_INT);
    if (!$stmt->execute()) {
        //failed to execute query
        PDO_error();
    }
    $res = [];
    foreach ($stmt->fetchAll() as $row) {
        $res[] = (object) [
            'name' => $row['name'],
            'email' => $row['email'],
            'latestCommit' => $row['latestcommit'],
            'age' => $row['age'],
            'bio' => $row['bio'],
            'picture' => $row['picture'],
            'followers' => $row['followers'],
        ];
    }
    return $res;
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
function count($user)
{
    check_not_null($user); //Required here
    //Creating the request
    $sql = "SELECT COUNT(*)
        FROM friend
        WHERE followingName = :fname";
    //Binding the request
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':fname', $user, \PDO::PARAM_STR);
    //Executing
    if (!$stmt->execute()) {
        //An error occured
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $res['count'];
}

function find($follower, $following)
{
    check_not_null($follower, $following);

    $sql = "SELECT followingName, followerName FROM friend
    WHERE followingName = :followingName AND followerName = :followerName";
    $bd = connect();
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':followingName', $following, \PDO::PARAM_STR);
    $stmt->bindValue(':followerName', $follower, \PDO::PARAM_STR);
    //Executing
    if (!$stmt->execute()) {
        //An error occured
        PDO_error();
    }
    $res = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $stmt->rowCount() != 0;
}
