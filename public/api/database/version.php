<?php
namespace Version;

include_once "config.php";

function add($author, $project, $branch, $partitions, $loggedUser)
{
    /* partitions est un tableau de : {
        name: string,
        content: string
    }
    */
}

function fetchAllFromProject($first, $after, $author, $project, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour order by createdAt asc ou desc
}

function fetchAllFromBranch($first, $after, $author, $project, $branch, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics. Modifier ce qu'il faut pour order by createdAt asc ou desc
}

function countFromProject($author, $project, $loggedUser)
{
    // Gérer cas projets privés et publics
}

function countFromBranch($author, $project, $branch, $loggedUser)
{
    // Gérer cas projets privés et publics
}

function seekPartition($first, $after, $author, $project, $branch, $version, $partition, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics, $partitions unique pour une version fixée. Utiliser LIKE %$partition%
}
