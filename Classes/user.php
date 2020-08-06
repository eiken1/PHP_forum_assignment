<?php

class User {
    private $db;

    //Set database variable to constructor, to allow for easier functionality, such as registering or logging in
    function __construct($db) {
        $this->db = $db;
    }
    //Function to register users, take username and password as parameters, binds them to the query values and executes the database statement, throws error if query failed
    public function registerUser ($username, $password) {

        try{
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

			$regStmt = $this->db->prepare(
                "INSERT INTO users (username, pword)
                VALUES(:uname, :upass)");
                
            $regStmt->bindparam(":uname", $username);
            $regStmt->bindparam(":upass", $hashedPassword);            
            $regStmt->execute(); 

            return $regStmt; 
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    //Function to log in as user, takes username and password as parameters, binds user to query to check for existing username, 
    //if username is found in database, checks parameter password up against database password, throws error if failed
    public function userLogin($username, $password) {
        try{
            $logStmt = $this->db->prepare("SELECT * FROM users WHERE username=:uname");
            $logStmt->bindParam(":uname", $username);
			$logStmt->execute();

            $userResult = $logStmt->fetch();
            if ($logStmt->rowCount() > 0) {

                if(password_verify($password, $userResult['pword'])) {
                    $_SESSION['username'] = $userResult['username'];
                    return true;
                }else {
                    return false;
                }
            }
        }catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Simple function to check if is user is logged in/online by checking the value of the username of the current session
    public function userOnline () {
        if (isset($_SESSION['username'])) {
            return true;
        }
    }

    //Function that retrieves username from database, collects userID of the resulting username and returns it, throws error if query failed
    function getUserID ($username) {
        try {
            $IDstmt = $this->db->prepare("SELECT * FROM users WHERE username = :uname");
            $IDstmt->bindParam(":uname", $username);
            $IDstmt->execute();

            $IDresult = $IDstmt->fetch();

            $ID = $IDresult['userID'];

            return $ID;

        }catch(PDOException $e) {
            echo $e->getMessage();
        }

    }
    //Function that retrives username from database, collects usertype from the resulting username and returns it, throws error if query failed
    function getUserType ($username) {

        try{

            $typeStmt = $this->db->prepare("SELECT * FROM users WHERE username = :uname");
            $typeStmt->bindParam(":uname", $username);
            $typeStmt->execute();

            $typeResult = $typeStmt->fetch();

            $type = $typeResult['uType'];

            return $type;
        }catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    //Function to redirect the user to different web pages, often used in the form of refreshing or redirecting visitors
    public function redirectUser ($direction) {
        header("Location: $direction");
    }
    //Function to destroy the current session and unset the current user of the session
    public function logout_user () {
        session_destroy();
        unset($_SESSION['username']);
        return true;
    }
}
?>