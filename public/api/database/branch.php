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
    if ($first == null || $after == null) {
        arg_error();
    }
    //Gérer cas projets privés et publics
}

function fetchAllFromUser($first, $after, $user, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    //Gérer cas projets privés et publics
}

function countFromProject($author, $project, $loggedUser) {
     // Gérer cas projets privés et publics
}

function countFromUser($user, $loggedUser) {
     // Gérer cas projets privés et publics
}

function seekVersion($first, $after, $author, $project, $branch, $version, $loggedUser) 
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics, $partitions unique pour une version fixée. Utiliser LIKE %$version%
}