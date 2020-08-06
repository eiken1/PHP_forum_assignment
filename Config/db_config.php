<?php
//Defines the database login/establishment constants
define('DBHOST','localhost');
define('DBNAME','assignment2_db');
define('DBUSER','root');
define('DBPASS','');
define('DBOPTIONS', array([
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,]));
?>