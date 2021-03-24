<?php
namespace Branch;

include_once "config.php";

function add($author, $project, $branch, $private, $loggedUser) {

}

function remove($author, $project, $branch) {
    // ne pas supprimer la branche principale
}

function rename($author, $project, $branch, $loggedUser) {
    
}

function fetchAllFromProject($first, $after, $author, $project, $loggedUser)
{
    //Gérer cas projets privés et publics
}

function fetchAllFromUser($first, $after, $user, $loggedUser)
{
    //Gérer cas projets privés et publics
}

function countFromProject($author, $project, $loggedUser) {
    
}

function countFromUser($user, $loggedUser) {
    
}
