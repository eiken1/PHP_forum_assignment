<?php
//This page simply exists for being able to log out users, if users are directec to this page, it calls the logout method
//From the user class and it logs them out and redirects them through the redirecting method, to the front page
    require_once "../Config/connect.php";

    $newUser->logout_user();

    if(!$newUser->userOnline()) {
        $newUser->redirectUser("loginUser.php");
    }
?>