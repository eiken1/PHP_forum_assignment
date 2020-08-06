<?php
    //This file establishes the session, the session user and also the database used throughout the application

	//Starts application session
	session_start();
	
	//Gets the database configuration file, that sets up the database creation variables
    require_once('db_config.php');
    
    //Establishes connection with the database and sets its parameters to the constants defined in the database configuration file
    try {
        $database = new PDO ("mysql: host=". DBHOST ."; dbname=".DBNAME."; charset=utf8", DBUSER, DBPASS, DBOPTIONS);

    //If the database connection failed, throw an error   
    } catch(PDOexception $e) {
        die ("Error: ".$e->getMessage());
    };

    include_once("../Classes/user.php");

    //Creates a pre-established user or "visitor"
    $newUser = new User($database);
?>