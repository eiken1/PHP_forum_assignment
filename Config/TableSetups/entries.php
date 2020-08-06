<?php
//This file is for creating the entries table in the database, and getting some pre-established entries in the database, throws error if either query fails
function createEntries ($database) {
    echo "This function creates placeholder entries in database";
    $tableQuery = 
        "CREATE TABLE IF NOT EXISTS entries(
        entryID int NOT NULL AUTO_INCREMENT,
        entryDesc varchar(1000),
        uploadDate datetime NOT NULL DEFAULT NOW(),
        topicID int,
        userID int,
        PRIMARY KEY(entryID),

        CONSTRAINT FK_users FOREIGN KEY (userID)
        REFERENCES users (userID)
        ON DELETE CASCADE ON UPDATE CASCADE,

        CONSTRAINT FK_topic FOREIGN KEY (topicID)
        REFERENCES topics (topicID)
        ON UPDATE CASCADE ON DELETE CASCADE
        );
    ";

    try {
        $database->exec($tableQuery);
        echo "<br>";
        echo "Entries table was created!";
        echo "<br>";
    } catch (PDOException $e) {
        die('Query failed(1):' . $database->errorInfo()[2]);
    }

    $tableInsertQuery = 
    "INSERT INTO entries (entryDesc, topicID, userID)
    VALUES ('It is raining here', '1', '1'), ('Bright and sunny in Italy', '1', '2'), ('What a sorry match from Martial', '2', '1'), ('United should have had that', '2', '2'), ('No, gloomy show', '3', '1'), ('Excellent show!', '3', '2'), ('Clubs and bars in Trondheim and Berlin are excellent!', '4', '1'), ('Blue', '5', '2'), ('Green', '5', '1'), ('Charlie bit my finger X)', '6', '2'), ('Mercedes Benz', '7', '1'), ('Cannot choose so cats AND dogs', '8', '1');";

    try {
        $database->exec($tableInsertQuery);
        echo "Entries were created!";
        echo "<br>";
    } catch (PDOException $e) {
        die('Query failed(1):' . $database->errorInfo()[2]);
    }
}

?>