<?php
//This page is for the user login
require_once '../Config/connect.php';

if  ($newUser->userOnline()!="") {
    echo "something went wrong";
}
//If statements that check if submit button, username and password are set, and then uses the login
//method from the user class to check if the log in succeeded, if it did, set the session username
//and use class methods to collect userID and usertype of the current user, and then sets the
//session type and userid of that of the current user
if(isset($_POST['submit'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = htmlentities($_POST['username']);
        $password = htmlentities($_POST['password']);

        if($newUser->userLogin($username, $password)){
            $loginFeedback="LOGIN WAS SUCCESSFUL, LOGGED IN AS: ". $username;

            $userID = $_SESSION['username'];

            $userType = "";
            $currentUserID ="";

            $echoUser = strtoupper($userID);

            $currentUserID = $newUser->getUserID($userID);
            $userType = $newUser->getUserType($userID);

            $_SESSION['type'] = $userType;
            $_SESSION['userID'] = $currentUserID;

        }else {
            $loginFeedback = "Login failed!";
        }
    } else {
        echo "Both username and password must be filled to log in!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf8">
        <link rel="stylesheet" href="../css/login.css">
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
            <h1>Login here!</h1>
            
            <!-- Login form for users -->
            <form action="loginUser.php" method="post" id="login">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="input" required>

                <br>

                <label for="password">Password</label>
                <input type="password" name="password" id="pass" class="input" required>

                <br>
                
                <input id="userlogin" class="loginBut" type="submit" name="submit" value="Log in">

                <p><?php 
                    if (isset($_POST['submit'])) {
                        echo $loginFeedback;
                    }
                    ?>
                </p>

            </form>
        </div>
    </body>
</html>