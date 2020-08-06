<?php
//This page is for setting up the database, dropping the application database if it exists, create a new one if it doesn't exist, putting the database in use
//This page also includes a function that creates the database tables and inserts some content into them
require_once ("db_config.php");
//Sets intial database creation variables
$host = "localhost";
$username = "root";
$password = "";

//Connect to database for intial contact and establishment of database and tables, throws error if query failed
try {
    $database = new PDO ("mysql: host=".$host.";charset=utf8", $username, $password);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    print("Database connection established");
    echo "<br>";
} catch(PDOException $e) {
    die ("Error(Could not connect): ". $e->getMessage());
};

//Drop database, if a database with the DBNAME constant exists
$dropQuery = 'DROP DATABASE IF EXISTS '. DBNAME;
if ($database->exec($dropQuery)===false) {
    die('Query failed(1):' . $database->errorInfo()[2]);
    echo "<br>";
} else {
    echo "Query was successful and database was dropped!";
    echo "<br>";
};

//Create the database to be used, if it doesn't exist
$createQuery = 'CREATE DATABASE IF NOT EXISTS '. DBNAME;
if ($database->exec($createQuery)===false) {
    die('Query failed(1):' .$database->errorInfo()[2]);
    echo "<br>";
} else {
    echo "Query was successful and database was created";
    echo "<br>";
}

//Sets the DBNAME constant to be used, if query succeeds, it also calls the function that creates the database's tables and content
$useQuery = 'USE '. DBNAME.'';
if ($database->exec($useQuery)===false) {
    die('Can not select database: '.$database->errorInfo()[2]);
    echo "<br>";
} else {
    echo "Query was successful and database " . DBNAME ." is now in use!";
    echo "<br>";
    createDatabaseTables($database);
};

function createDatabaseTables ($database) {
    include('TableSetups/users.php');
    createUsers($database);
    echo "<br>";
    include('TableSetups/topics.php');
    createTopics($database);
    echo "<br>";
    include('TableSetups/entries.php');
    createEntries($database);
    echo "<br>";
}
?>