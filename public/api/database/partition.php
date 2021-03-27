<?php
namespace Partition;

include_once "config.php";

function add($author, $project, $branch, $partition, $content, $loggedUser)
{

}

function fetchAllFromVersion($first, $after, $author, $project, $version, $loggedUser)
{
    if ($first == null || $after == null) {
        arg_error();
    }
    // Gérer cas projets privés et publics
}

function countFromVersion($author, $project, $branch, $version, $loggedUser)
{
    // Gérer cas projets privés et publics
}
