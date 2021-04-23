<?php

// See : https://www.youtube.com/watch?v=BJcpajX7EdU


/**
 * Convert order arg to the SQL query format
 * 
 * Valid args are made like this:
 * C for creation, M for modification (/update) . A for ascending, D for descending. 
 * Each arg should have the format XO+YO or XO, where X,Y € {C,M} and O € {A,D}.
 * 
 * X is the fist arg that we want to order by, and Y the second one (if we want to, it's optionnal).
 * So CA+MD will order by CreatedAt Ascending and then updateAt Descending ;
 * MD+CD will order by updatedAt Descending and then createdAt ascending.
 * 
 * MA will order by updatedAt Ascending
 * 
 * Default if MD, so updatedAt Descending
 * 
 * Default will be used if the arg is not in the correct format, etc
 * 
 * 
 */
function get_real_order($order){

    $real_order = "";
    switch ($order) {

        case "CA+MA":
            $real_order = "createdAt ASC , updatedAt ASC" ;
            break;

        case "CD+MA":
            $real_order = "createdAt ASC , updatedAt DESC" ;
            break;

        case "CA+MD":
            $real_order = "createdAt DESC , updatedAt ASC" ;
            break;
        case "CD+MD":
            $real_order = "createdAt DESC , updatedAt DESC" ;
            break;

        case "MA+CA":
            $real_order = "updatedAt ASC ,createdAt ASC" ;
            break;

        case "MA+CD":
            $real_order = "updatedAt ASC ,createdAt DESC" ;
            break;

        case "MD+CA":
            $real_order = "updatedAt DESC ,createdAt ASC" ;
            break;

        case "MD+CD":
            $real_order = "updatedAt DESC ,createdAt DESC" ;
            break;

        case "CA":
            $real_order = "createdAt ASC" ;
            break;

        case "CD":
            $real_order = "createdAt DESC" ;
            break;
        
        case "MA":
            $real_order = "updatedAt DESC" ;
            break;

        case "MD":
            $real_order = "updatedAt DESC" ;
            break;

        default:
            $real_order = "updatedAt DESC" ;
            break;
            
            
    }
    return $real_order ;


}