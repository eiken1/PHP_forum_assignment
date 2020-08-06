<?php
//This page is the user profile page, here a user can create a new topic or add an entry to an existing one
//Users are also shown some generic user information as well as all the entries or topics they have created

require_once '../Config/connect.php';

//If visitor is not online/not a user, redirect he or she to the login page
if (!$newUser->userOnline()) {
    $newUser->redirectUser('login.php');
}

//collects usernname from current session and assigns it to a variable in uppercase
$currentUser = $_SESSION['username'];

$echoUser = strtoupper($currentUser);

//These if statements checks for delete submit form submits, if they have been set, delete the targeted topic or entry
if (isset($_POST['deleteTopic'])) {
    $deleteStmt = $database->prepare("DELETE FROM topics WHERE topicID =".intval($_POST['deleteTopic'])."");
    $deleteStmt->execute();
}

if (isset($_POST['deleteEntry'])){
    $deleteStmt = $database->prepare("DELETE FROM entries WHERE entryID =".intval($_POST['deleteEntry'])."");
    $deleteStmt->execute();
}

//Establishes an empty string variable, for use with error
$userExistsErr="";

//This if statement checks if the username edit form has been submitted, then it collects the new username
//From the new username field, then it executes a query to check for existing usernames
//If the new name is not already registered, change this users username to the new one and update the session variable, then refresh the page
if (isset($_POST['usernameEdit'])) {

    $newUsername = htmlentities($_POST['newUsername']);
    
    $editStmt = $database->prepare(
        "SELECT username FROM users WHERE username ='$newUsername'");
    $editStmt->execute();
    $existingUser = $editStmt->rowCount();

    if ($existingUser > 0) {
        $userExistsErr = "Username is taken by another user, please select another one!";
    } else {
        $editStmt = $database->prepare(
            "UPDATE users SET username ='$newUsername' WHERE username ='$currentUser'");
        $editStmt->execute();

        $_SESSION['username'] =  $newUsername;

        $newUser->redirectUser('userProfile.php');
    }
}

//This if statement checks if the password edit form has been submitted, then it collects the new password
//From the new password field, and updates the current password with the new one, after the new one has been hashed
//Finally log out the user and redirect them to the login page
if (isset($_POST['passwordEdit'])) {
    $hashedPassword = password_hash(htmlentities($_POST['newPassword']), PASSWORD_DEFAULT);
    $editStmt = $database->prepare(
        "UPDATE users SET pword = '$hashedPassword' WHERE username ='$currentUser'");
    $editStmt->execute();
    $newUser->redirectUser('logoutUser.php');
}
//This function checks the create topic post form and executes a query that has the sanitized user input, 
//from the topicname form field and sets the createrID to the current user
if (isset($_POST['createTopic'])) {
    $topicStmt = $database->prepare("
    INSERT INTO topics (topic, userID) VALUES ('".htmlentities($_POST['topicName'])."', :userid);");
    $topicStmt->bindParam(":userid", $_SESSION['userID']);
    $topicStmt->execute();
}
//This function checks the create entry post form and executes a query that has the sanitized user input, 
//from the entryname form field and sets the createrID to the current user and the topicID to the chosen topic
if (isset($_POST['createEntry'])) {
    if (isset($_POST['topicNr'])) {
        $topicStmt = $database->prepare("
        INSERT INTO entries (entrydesc, userID, topicID) VALUES ('".htmlentities($_POST['entryName'])."', :userid, :topicid);");
        $topicStmt->bindParam(":userid", $_SESSION['userID']);
        $topicStmt->bindParam(":topicid", $_POST['topicNr']);
        $topicStmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang ="en">
  <head>
      <meta charset="utf-8">
      <script src ="../js/showForm.js"></script>
      <link rel="stylesheet" href="../css/profile.css">
      <link rel="stylesheet" href="../css/navbar.css">
      <link rel="stylesheet" href="../css/main.css">
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

    <h1>Profile page for: <?php echo $echoUser; ?></h1>
    <div class ="mainContainer">
        <h2 id="createTitle">Create topics and entries here!</h2>

        <!-- Below are sections for creating new topics and new entries under existing topics,
        the forms are opened by clicking the shown buttons-->
        <section class="create">
            <label for="createTopicBut">Open topic form by clicking the topic button!</label>
            <input type="button" id="createTopicBut" onclick="showTopicForm()" value="Show topic form">
            <div id="createTopicArea">
                <form method="post">
                    <input type="text" name="topicName" maxlength="40">
                    <input type="submit" name="createTopic" value="Create Topic">
                </form>
            </div>
        </section>

        <br>
        <br>
        <br>

        <section class="create">
            <label for="createEntryBut">Open entry form by clicking the entry button!</label>
            <input type="button" id="createEntryBut" onclick="showEntryForm()" value="Show entry form">
            <div id="createEntryArea">
                <form method="post" id="entryForm">
                    <?php
                        //Executes a query to check if there are any topics, if there are, a radio button
                        //with the topicID and topicname is created for each topic
                        $topicListStmt = $database->prepare("SELECT * FROM topics");
                        $topicListStmt->execute();

                        $topicList = $topicListStmt->rowCount();

                        if ($topicList==0){
                            echo "<h4>There are no topics!</h4>";
                        }else {
                            while($rows = $topicListStmt->fetch()) {
                                echo $rows['topic']. ": <input type='radio' name='topicNr' value='".$rows['topicID']."'>";
                                echo "<br>";
                            }
                        }
                    ?>
                    <textarea name="entryName" id="textArea" maxlength="1000" form="entryForm"></textarea>
                    <input type="submit" name="createEntry" value="Create Entry">
                </form>
            </div>
        </section>

        <br>
        <br>

        <!--Below is a short section for displaying current user information-->
        <h2 id="createTitle">Your user information!</h2>
        <label for="showUsername">Your username:</label>
            <p><?php echo $_SESSION['username'];?></p>
        </label>
        <label for="showUserID">Your user ID:</label>
            <p><?php echo $_SESSION['userID'];?></p>
        </label>
        <label for="showUserType">Your usertype:</label>
        <p><?php echo $_SESSION['type'];?></p>

        <h2 id="createTitle">Edit your user information here!</h2>


        <form method="post" class="edit">
            <?php
            //If the the current user is an admin type user, then dont create a username form field, but only a password form field,
            //As only regular users are able to update both their username and password, while admin can only change its password
            if ($_SESSION['type']== 'admin') {
                echo "<p>This user is an admin, so you cannot change any details, except for the password!</p>";
            } elseif ($_SESSION['type']== 'author') {
                echo "
                <label for='newUsername'>Update your username:</label>
                <input type='text' name='newUsername' id='username' class='input'>
                <input type='submit' name='usernameEdit' value='Update'>
                <div class='error'>". $userExistsErr. "</div>

                <br>
                ";
            }
            ?>

            <label for="newPassword">Update your password</label>
            <input type="password" name="newPassword" id="pass" class="input">
            <input type="submit" name="passwordEdit" value='Update'>
        </form>

        <h2>Your topics!</h2>
        <?php
        //Below, a query is executed to check for the current users created topics,
        //If the user has any created topics, display their name and creation date and add a button for deletion,
        //for each created topic, through a while loop. If user has no created topics, simply give them feedback
        $collectStmt = $database->prepare("SELECT topicID, topic, creationDate FROM topics t INNER JOIN users u ON t.userID = u.userID WHERE t.userID = :uID");
        $collectStmt->bindParam(":uID", $_SESSION['userID']);
        $collectStmt->execute();

        $nrOfTopics = $collectStmt->rowCount();
        ?>
        <table>
            <tr>
                <td>TOPIC</td>
                <td>CREATION DATE</td>
                <td>DELETE</td>
            </tr>
            <?php
            if($nrOfTopics==0) {
                echo "<h4>You haven't created any topics!</h4>";
            } else {
                while ($rows = $collectStmt->fetch()) {
                    echo "<tr>";
                        echo "<td> ". $rows['topic'] . "</td>";
                        echo "<td> ". $rows['creationDate'] . "</td>";
                        echo "<td> 
                            <form method='post'>
                                <input type='hidden' name='deleteTopic' value='". intval($rows['topicID']) . "'>
                                <input type='submit' name='' value='DELETE'>
                            </form>
                        </td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>

        <h2>Your entries!</h2>
        <?php
        //Below, a query is executed to check for the current users created entries,
        //If the user has any created entries, display the entry content, creation date and add a button for deletion,
        //for each created entry, through a while loop. If user has no created entries, simply give them feedback
        $entryStmt = $database->prepare("SELECT entryID, entryDesc, uploadDate
        FROM entries e INNER JOIN users u 
        ON e.userID = u.userID
        WHERE u.userID = ?;");
        $entryStmt->bindParam(1, $_SESSION['userID']);
        $entryStmt->execute();

        $nrOfEntries = $entryStmt->rowCount();
        ?>
        <table>
            <tr>
                <td>DESCRIPTION</td>
                <td>UPLOAD DATE</td>
                <td>DELETE</td>
            </tr>
            <?php
            if($nrOfEntries==0) {
                echo "<h4>You haven't created written any entries!</h4>";
            } else {
                while ($rows = $entryStmt->fetch()) {
                    echo "<tr>";
                        echo "<td> ". $rows['entryDesc'] . "</td>";
                        echo "<td> ". $rows['uploadDate'] . "</td>";
                        echo "<td> 
                            <form method='post'>
                                <input type='hidden' name='deleteEntry' value='". intval($rows['entryID']) . "'>
                                <input type='submit' name='' value='DELETE'>
                            </form>
                        </td>";
                    echo "</tr>";
                }
            }
            ?>
        </table>
    </div>
  </body>
</html>
