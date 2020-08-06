<?php
//This page is the admin page, only useable by the admin, and is where the admin can use his or her admin privileges

//Require connect file to get connection to database and current session
require_once '../Config/connect.php';

//If the user is not an admin or if the visitor is not online, redirect the user to the log in page
if (!$newUser->userOnline()) {
    $newUser->redirectUser('loginUser.php');
} elseif ($_SESSION['type'] !== "admin") {
    $newUser->redirectUser('loginUser.php');
}

$username = $_SESSION['username'];

$echoUser = strtoupper($username);

//Below are the if statements that are used to delete users, entries and topics. 
//First checks if the deletion submit button has been set, and then performs a deletion query
if (isset($_POST['deleteTopic'])) {
    $deleteStmt = $database->prepare("DELETE FROM topics WHERE topicID =".intval($_POST['deleteTopic'])."");
    $deleteStmt->execute();
}

if (isset($_POST['deleteEntry'])){
    $deleteStmt = $database->prepare("DELETE FROM entries WHERE entryID =".intval($_POST['deleteEntry'])."");
    $deleteStmt->execute();
}
if (isset($_POST['deleteUser'])) {
    $deleteStmt = $database->prepare("DELETE FROM users WHERE userID =".intval($_POST['deleteUser'])."");
    $deleteStmt->execute();
}
?>

<!DOCTYPE html>
<html lang ="en">
  <head>
      <meta charset="utf-8">
      <link rel="stylesheet" href="../css/admin.css">
      <link rel="stylesheet" href="../css/main.css">
      <link rel="stylesheet" href="../css/navbar.css">
      <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
      
  </head>

    <body>
        <header class="navbar">
            <?php
            //Situational navigation bar, depending on who the user is show different links
            if (!$newUser->userOnline()) {
                echo"
                    <a id='linkMain' href='index.php'>Frontpage</a>
                    <a id='linkLogin' href='loginUser.php'>SIGN IN</a>
                    <a id='linkRegister' href='registerUser.php'>REGISTER</a>
                ";
            }elseif ($_SESSION['type'] == 'admin') {
                echo "
                    <a id='linkMain' href='index.php'>Frontpage</a>
                    <a id='linkLogout' href='logoutUser.php'>SIGN OUT</a>
                    <a id='linkProfile' href='userProfile.php'>WELCOME: ". $echoUser . "</a>
                    <a id='linkAdmin' href='admin.php'>ADMIN PAGE</a>
                ";
            }elseif ($_SESSION['type'] == 'author') {
                echo "
                    <a id='linkMain' href='index.php'>Frontpage</a>
                    <a id='linkLogout' href='logoutUser.php'>SIGN OUT</a>
                    <a id='linkProfile' href='userProfile.php'>PROFILE PAGE FOR: ". $echoUser . "</a>
                ";
            }
            ?>
        </header>
        <h1>ADMIN PAGE</h1>
        <div class="mainContainer">
            <h2>Welcome, <?php echo $echoUser;?></h2>
            
            <h3>As an admin you can view and delete all entries, users and topics on this page!</h3>

            <section id="usersSection">
                <h3>All users:</h3>
                <?php
                //Query to collect userdata from database
                    $userStmt = $database->prepare("SELECT userID, username, uType FROM users");
                    $userStmt->execute();

                    $users = $userStmt->rowCount();
                ?>
                <table>
                    <tr>
                        <td>UserID</td>
                        <td>Usernames</td>
                        <td>User type</td>
                        <td>DELETE</td>
                    </tr>
                    <?php
                    if($users==0) {
                        echo "<h4>No users exist!</h4>";
                    } else {
                        //As long as there are rows to collect from database, make a table row for each database row,
                        //That includes the userID, username, usertype and delete button for each user
                        //If the usertype is of admin, set the deletion button to be useless
                        while ($rows = $userStmt->fetch()) {
                            echo "<tr>";
                                echo "<td> ". $rows['userID'] . "</td>";
                                echo "<td> ". $rows['username'] . "</td>";
                                echo "<td> ". $rows['uType'] . "</td>";
                                if ($rows['uType'] === 'admin') {
                                    echo '<td>
									    <input type="button" value="CANT DELETE">
									</td>';
                                } elseif ($rows['uType']=== 'author'){
                                echo "<td>
                                    <form method='post'>
                                        <input type='hidden' name='deleteUser' value='". intval($rows['userID']) . "'>
                                        <input type='submit' name='' value='DELETE'>
                                    </form>
                                 </td>";
                                }
                            echo "</tr>";
                        }
                    }
                    ?>
                </table>
            </section>

            <section id="topicsSection">
                <h3>All topics:</h3>
                <?php
                    $topicStmt = $database->prepare("
                    SELECT t.topicID, t.topic, t.userID, count(e.entryID) entryCount, u.username
                    FROM topics t
                    LEFT JOIN entries e
                    ON e.topicID = t.topicID
                    INNER JOIN users u
                    ON t.userID = u.userID
                    GROUP BY t.topicID");
                    $topicStmt->execute();

                    $topics = $topicStmt->rowCount();
                ?>
                <table>
                    <tr>
                        <td>TopicID</td>
                        <td>Topic</td>
                        <td>Created by</td>
                        <td>Number of entries</td>
                        <td>DELETE</td>
                    </tr>
                    <?php
                    if($topics==0) {
                        echo "<h4>No topics exist!</h4>";
                    } else {
                        //As long as there are rows to collect from database, make a table row for each database row,
                        //That includes the topicID, topic, username of topic creator, entry count per topic,
                        //and a delete button for each topic
                        while ($rows = $topicStmt->fetch()) {
                            echo "<tr>";
                                echo "<td> ". $rows['topicID'] . "</td>";
                                echo "<td> ". $rows['topic'] . "</td>";
                                echo "<td> ". $rows['username'] . "</td>";
                                echo "<td> ". $rows['entryCount'] . "</td>";
                                echo "<td> 
                                    <form method='post'>
                                        <input type='hidden' name='deleteTopic' value='".$rows['topicID'] ."'>
                                        <input type='submit' name='' value='DELETE'>
                                    </form>
                                </td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </table>
            </section>

            <section id="entriesSection">
                <h3>All entries:</h3>
                <?php
                    $entryStmt = $database->prepare("
                    SELECT e.entryID, e.entryDesc, e.userID, e.uploadDate, u.username 
                    FROM entries e 
                    INNER JOIN users u
                    ON u.userID = e.userID");
                    $entryStmt->execute();

                    $entries = $entryStmt->rowCount();
                ?>
                <table>
                    <tr>
                        <td>EntryID</td>
                        <td>Entry text</td>
                        <td>Created by</td>
                        <td>Upload date</td>
                        <td>DELETE</td>
                    </tr>
                    <?php
                    if($entries==0) {
                        echo "<h4>No entries exist!</h4>";
                    } else {
                        //As long as there are rows to collect from database, make a table row for each database row,
                        //That includes the entryID, entry text, username of entry creator, creation date for the entry and a delete button for each entry
                        while ($rows = $entryStmt->fetch()) {
                            echo "<tr>";
                                echo "<td> ". $rows['entryID'] . "</td>";
                                echo "<td> ". $rows['entryDesc'] . "</td>";
                                echo "<td> ". $rows['username'] . "</td>";
                                echo "<td> ". $rows['uploadDate'] . "</td>";
                                echo "<td> 
                                    <form method='post'>
                                        <input type='hidden' name='deleteEntry' value='".intval($rows['entryID'])."'>
                                        <input type='submit' name='' value='DELETE'>
                                    </form>
                                </td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </table>
            </section>
        </div>
    </body>
</html>