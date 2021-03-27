<?php
namespace Contributor;

include_once "config.php";

function add($contributor, $authorName, $project, $loggedUser)
{

}

function remove($contributor, $authorName, $project, $loggedUser)
{
    //Gérer le cas de se retirer soi-même si on veut quitter, mais si on n'est pas l'auteur, alors on ne peut pas kick les autres
}

function fetchAllFromProject($first, $after, $contributor, $authorName, $project, $loggedUser)
{
    // Gérer cas projets privés et publics
}

function countFromProject($contributor, $authorName, $project, $loggedUser)
{
    // Gérer cas projets privés et publics
}
