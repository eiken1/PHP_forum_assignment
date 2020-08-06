<?php
//This page is for creating the users table and for inserting two pre-established users into the database, one of them being the only admin of the whole page
//The admin below is the only admin available through the whole application, and you can see the log-in details of the admin below
//The users insert query had to be done a little differently than the topics and entries queries, as i wanted to hash both the password of the admin and the testuser
function createUsers ($database) {
    echo "<br>";
    echo "This function creates users in database";
    echo "<br>";

    try {
        $tableQuery = 
        "CREATE TABLE IF NOT EXISTS users(
        userID int NOT NULL AUTO_INCREMENT,
        username varchar(40),
        pword varchar(200),
        created datetime NOT NULL DEFAULT NOW(),
        uType varchar (10) NOT NULL DEFAULT 'author',
        PRIMARY KEY(userID)
        );";

        $database->exec($tableQuery);
        print("Users table was created");
        echo "<br>";

    }catch (PDOException $e) {
        echo "<br>";
        echo $e->getMessage();
    }

    try {
        $tableInsertQuery = $database->prepare(
        "INSERT INTO users (username, pword, uType)
        VALUES ('admin', :password1, 'admin'), ('test', :password2, 'author')");

        $password1 = "admin";
        $password2 = "test"; 

        $hashedPw1 = password_hash($password1, PASSWORD_DEFAULT);
        $hashedPw2 = password_hash($password2, PASSWORD_DEFAULT);
        
        $tableInsertQuery->bindparam(":password1", $hashedPw1);
        $tableInsertQuery->bindparam(":password2", $hashedPw2);

        $tableInsertQuery->execute();
        print("Users were added");
        echo "<br>";
        echo "<br>";

    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
?>