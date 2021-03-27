<?php

$HOST="pgsql";
$PORT=5432;
$DTBS="musegit";
$USER="tpphp";
$PASS="tpphp"; 

/**
 * Retourne une connexion vers la base de données
 */
function connect()
{
	global $HOST, $DTBS, $USER, $PASS, $PORT;
	try {
		return new \PDO("pgsql:dbname=$DTBS;host=$HOST;port=$PORT", $USER, $PASS);
	} catch (\Throwable $th) {
		return new \PDO("pgsql:dbname=$DTBS;host=127.0.0.1;port=$PORT", $USER, $PASS);
	}
}
