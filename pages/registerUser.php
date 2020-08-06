<?php
//This page is for registering users
require_once '../Config/connect.php';

$registerError = '';
$userFeedback = '';

//If statement that first checks if the submit form has been set, then it sanitizes the username and password form fields
//The next if statement checks if any of the inpud fields are empty, when submitting the form
//The next if statement checks if the values from the two password fiels match
//If all validation is passed, a query is prepared using the values from the username and password fields
//A query is then run to check for usernames that matches the value given in the username form
//If username already exists, throw an error, if it does not create a new user through the user class method registerUser
if(isset($_POST['submit'])) {
    $trimmedUname = trim($_POST['regUsername']);
    $trimmedPword = trim($_POST['regPassword']);
    $trimmedCPword = trim($_POST['regConfirmPassword']);
    if ((empty($trimmedUname)) || (empty($trimmedPword)) || (empty($trimmedCPword))){      
        $registerError = "None of the fields can be empty!";
    }else {
        if ($trimmedPword === $trimmedCPword){
            $username = htmlentities($_POST['regUsername']);
            $password = htmlentities($_POST['regPassword']);

            $userStatement = $database->prepare(
                "SELECT username FROM users WHERE username = :uname;"
            );
            $userStatement->bindParam(":uname", $username);
            $userStatement->execute();

            if ($userStatement->rowCount() > 0) {    

                $registerError = "A user with the same username is already registered, please try another!";
            } else {

                $newUser->registerUser($username, $password);
                $userFeedback = "A user with the username ". $username . " was created!";
            }
        } else {
            $registerError = "The passwords need to match, try again!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf8">
        <link rel="stylesheet" href="../css/register.css">
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
        <div class="mainContainer">
            <h1>Register User</h1>

            <!-- Form for registering a new user -->
            <form action="registerUser.php" name="register" method="post">
                <label>Username</label>
                <input type="text" name="regUsername">
                <div id="userError"><?php echo $registerError; ?></div>

                <br>

                <label>Password</label>
                <input type="password" name="regPassword">
                <div id="passwordError"></div>


                <br>

                <label>Confirm Password</label>
                <input type="password" name="regConfirmPassword">
                <div id="cPasswordError"></div>

                <br>

                <input type="submit" name="submit" value="Create user">
                <div id="userFeedback"><?php echo $userFeedback ?></div>
            </form>
        </div>
    </body>
</html>