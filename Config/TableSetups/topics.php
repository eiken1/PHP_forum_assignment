<?php
//This page is for creating the topics table and for inserting some pre-established topics, throws error if either query fails
function createTopics ($database) {
    echo "This function creates placeholder topics in database";
    $tableQuery = 
        "CREATE TABLE IF NOT EXISTS topics(
        topicID int NOT NULL AUTO_INCREMENT,
        topic varchar(40),
        userID int,
        creationDate datetime NOT NULL DEFAULT NOW(),
        PRIMARY KEY(topicID),

        CONSTRAINT FK_user FOREIGN KEY (userID)
        REFERENCES users (userID)
        ON DELETE CASCADE ON UPDATE CASCADE
        );
    ";

    try {
        $database->exec($tableQuery);
        echo "<br>";
        echo "Topics table was created!";
        echo "<br>";
    } catch (PDOException $e) {
        die('Query failed(1):' . $database->errorInfo()[2]);
    }

    $tableInsertQuery = 
    "INSERT INTO topics (topic, userID)
    VALUES ('Weather', '1'), ('United vs Liverpool', '1'), ('Anyone watching Black Mirror', '2'), ('Funnest times out on the town', '2'), ('Favorite colors', '1'), ('Funnest viral videos', '1'), ('Favorite car brands', '2'), ('Favorite pets', '1')";

    try {
        $database->exec($tableInsertQuery);
        echo "Topic was created!";
        echo "<br>";
    } catch (PDOException $e) {
        die('Query failed(1):' . $database->errorInfo()[2]);
    }
}

?>