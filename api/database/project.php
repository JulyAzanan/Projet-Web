<?php
namespace Project;

include_once "config.php";

function add($author, $project, $private) {
    // utiliser une transaction pour créer une branche main. cf https://www.php.net/manual/fr/pdo.transactions.php
}

function remove($author, $project) {
    // retirer les branches en premier et utiliser une transaction pour supprimer la branche main
}

function fetchAll($first, $after, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour un order by date de création ou de modif, asc ou desc
}

function fetchAllFromUser($first, $after, $user, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour un order by date de création ou de modif, asc ou desc
}

function count($loggedUser) {
    //Gérer cas des projets privés et publics
}

function countFromUser($loggedUser) {
    //Gérer cas des projets privés et publics
}

function seekBranch($first, $after, $author, $project, $loggedUser) 
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics, $partitions unique pour une version fixée. Utiliser LIKE %$branch%
}