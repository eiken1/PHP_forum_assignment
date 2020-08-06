<?php
//This page is the index page/The frontpage of the application
//A user or a visitor is able to view all topics and its entries
//Are shown a random topic on arrival, along with its entries
//And are also able to perform a boolean search against existing entries and topics

require_once '../Config/connect.php';

//Checks if a user is online, if it is set the username to a variable, that is set to another variable that always showcases the username in uppercase
if($newUser->userOnline()) {

  $userID = $_SESSION['username'];

  $echoUser = strtoupper($userID);
}

//If statement that collects the sorting choice of the current visitor, sets it to a cookie that lasts for 30 days
//Then refresh the page
if(isset($_POST['sort'])){

  $cName = 'sortPreference';

  $cValue = $_POST['sort'];

  setcookie($cName, $cValue, time() + (60*60*24*30));

  $newUser->redirectUser('index.php');
}
?>

<!DOCTYPE html>
<html lang ="en">
  <head>
      <meta charset="utf-8">

      <link rel="stylesheet" href="../css/index.css">
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
        
        <h1>URBAN DICTONARY</h1>
        <div class="wrapper">
        <div class="searchAndFilter">
          <section id ="sortSection" class="sortSection">
            <span id ="sortCategories">Choose how to sort topic:</span>
            <form method="post" class="indexForm">
              <input type="submit" name="sort" value="Chronological">
              <input type="submit" name="sort" value="Popularity">
            </form>
            <p>You are currently sorting by: <?php
            //Above is the "form" or section where a visitor selects its sorting preference 
            //Show current sorting choice
            if (isset($_COOKIE['sortPreference'])){
              echo $_COOKIE['sortPreference'];}
              ?></p>
          </section>

          <!-- This section is for the search form -->
          <section id ="searchSection" class="searchSection">
            <span id ="searchCategories">Search for topics (and entries?) here:</span>
            <form method="post" class="indexForm">
              <input type="search" name="search" results="4">
            </form>
          </section>
        </div>
        <div class="container">
        
          <ul class="topic-list">
           
          <?php
            $sortChoice = '';
  
              //If sorting choice has been set
              if(isset($_COOKIE['sortPreference'])) {
                //If visitor wants to sort by topic popularity, e.g nr of entries per topic
                if ($_COOKIE['sortPreference'] === "Popularity") {
                  $sortChoice = $_COOKIE['sortPreference'];
                  
                  //query to collect every column from the topics database, along with nr of entries per topic
                  $sortStmt = $database->prepare("
                    SELECT t.*, count(e.entryID) count
                    FROM topics t
                    LEFT JOIN entries e
                    ON t.topicID = e.topicID
                    GROUP BY t.topicID
                    ORDER BY count DESC");

                    $sortStmt->execute();
                    
                    $sortRows = $sortStmt->rowCount();

                }else{
                  $sortChoice = 'Chronological';
                  //query to collect every column from the topics database, along with nr of entries per topic
                  $sortStmt = $database->prepare("
                    SELECT t.*, count(e.entryID) count
                    FROM topics t
                    LEFT JOIN entries e
                    ON t.topicID = e.topicID
                    GROUP BY t.topicID
                    ORDER BY t.creationDate DESC");

                    $sortStmt->execute();
                    
                    $sortRows = $sortStmt->rowCount();
                }
              } 
            
              //For each loop that shows all topics and entries per topic
            foreach($sortStmt as $row){
              echo "
              <li>
                <a href='?topic=".$row['topicID']."'>".$row['topic']." (".$row['count'].")</a>
              </li>";
            }
            ?>

          </ul>

          
          <div class="topic-info">
            <?php 
            //If search form value has been set, continue
            //Use enter after typing to search
            if(isset($_POST['search'])){
              //splits the search string on spaces and fills the array
              $searchSplit = explode(" ", htmlentities($_POST['search']));

              //Intialize empty string variables
              $topicWHERE = '';
              $entryWHERE = '';

              //For loop to create as many SQL askings we need
              for ($i = 0; $i < count($searchSplit); $i++) {
                //If the variable $i == 0 use WHERE instead of AND in queries
                if($i == 0){
                  $topicWHERE = $topicWHERE . "
                  WHERE topic LIKE '%".$searchSplit[$i]."%'";

                  $entryWHERE = $entryWHERE . "
                  WHERE entryDesc LIKE '%".$searchSplit[$i]."%'";
                }else{
                  $topicWHERE = $topicWHERE . "
                    AND topic LIKE '%".$searchSplit[$i]."%'";
                  
                  $entryWHERE = $entryWHERE . "
                    AND entryDesc LIKE '%".$searchSplit[$i]."%'";
                }
              }

              //Database queries that uses the resulting strings from the above for loop
              $topicStmt = $database->prepare("
                SELECT *
                FROM topics 
                ".$topicWHERE."
              ");
              $topicStmt->execute();
              $nrOfTopics=$topicStmt->rowCount();

              $entryStmt = $database->prepare("
                SELECT *
                FROM entries
                ".$entryWHERE."
              ");
              $entryStmt->execute();
              $nrOfEntries=$entryStmt->rowCount();
              echo "<h4>We found <i>".$nrOfTopics." topics</i> and <i>".$nrOfEntries." entries</i> matching your search!</h4>";
              echo '<div class="entries">';
              //While loop that shows search results of topic query
              while($topic = $topicStmt->fetch()){
                echo '
                  <div class="entry">
                    <span>'.$topic['topic'].'</span>
                    <span class="entryInfo ">TOPIC</span>
                    <span class="entryInfo entryName"><a href="?topic='.$topic["topicID"].'">Click to read entries!</a></span>
                  </div>';
              }
              //While loop that shows search results of entry query
              while($entry = $entryStmt->fetch()){
                echo '
                  <div class="entry">
                    <span>'.$entry['entryDesc'].'</span>
                    <span class="entryInfo ">ENTRY</span>
                    <span class="entryInfo entryName"><a href="?topic='.$entry["topicID"].'">Click to read the topic!</a></span>
                  </div>';
              }
              echo "</div>";
            }else{
              //Use get method to get clicked/chosen topic
              if(isset($_GET['topic'])){
                $selectedTopic = $_GET['topic'];
                $topicStmt = $database->prepare("
                  SELECT *
                  FROM topics t
                  INNER JOIN users u
                  ON t.userID = u.userID
                  WHERE t.topicID = :selectedTopic
                ");
                $topicStmt->bindParam(":selectedTopic", $selectedTopic);
                
              } else {
                //If visitor has not selected a topic, showcase a random one
                $topicStmt = $database->prepare("
                  SELECT *
                  FROM topics t
                  INNER JOIN users u
                  ON t.userID = u.userID
                  GROUP BY t.topicID
                  ORDER BY RAND()
                  LIMIT 1
                "); 
              }
              $topicStmt->execute(); 
              $topic = $topicStmt->fetch();
              
              //Query that collects entries from chosen topic 
              $entryStmt = $database->prepare("
                SELECT *
                FROM entries e
                INNER JOIN users u
                ON e.userID = u.userID
                WHERE e.topicID = :topicID
                ORDER BY e.uploadDate DESC
              ");
              $entryStmt->bindParam(":topicID", $topic['topicID']);
              $entryStmt->execute();
              $entryCount = $entryStmt->rowCount();


              echo '
              <h2>'.$topic['topic'].'</h2>
              <div class="topic-owner-box">
                <span>Created at: '.$topic['created'].'</span>
                <span>Created by: '.$topic['username'].'</span>
              </div>
              <hr>';
              //If statement to check if a topic has any entries, if it does not, show feedback, if it does, show all entries
              if($entryCount == 0){
                echo "No entries yet";
              }else{
                echo '<div class="entries">';

                while($entry = $entryStmt->fetch()){
                  $ownEntry = "";
                  if (isset($_SESSION['userID'])){
                    if($entry["userID"] == $_SESSION['userID']){
                      $ownEntry = "own-entry";
                    }
                  }
                  echo '
                  <div class="entry '.$ownEntry.'">
                    <span>'.$entry['entryDesc'].'</span>
                    <span class="entryInfo">'.$entry["uploadDate"].'</span>
                    <span class="entryInfo entryName">'.$entry["username"].'</span>
                  </div>';
                }

                echo '</div>';
              }
            }
           ?>
            
          </div>
        </div>
      </div>
  </body>
</html>
