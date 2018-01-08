<?php
session_start();

require 'api.php';
require_once "dbinfo.php.inc";

$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

$loginId = $_REQUEST['loginId'];
$pwd = $_REQUEST['pwd'];

$result = $mydb->query("SELECT * FROM users WHERE 
                              login_id = '$loginId' AND password = '$pwd'");

// check if there is a user match information
if ($result->num_rows != 0) {

  $_SESSION['LoggedIn'] = 1;
  $_SESSION['loginId'] = $loginId;

  $userId = $mydb->query("SELECT user_id  FROM users WHERE login_id = '$loginId'")
                  ->fetch_row()[0];

  // update logs
  $result = $mydb->query("SELECT * FROM logs WHERE user_id = $userId");

  $row = $result->fetch_array(MYSQLI_ASSOC);


  $numLoginAttemp = $row['num_login_attempt'];
  $numLoginSuccess = $row['num_login_success'];

  $mydb->query("UPDATE logs SET 
                        num_login_attempt = $numLoginAttemp + 1,
                        num_login_success = $numLoginSuccess + 1
                        WHERE user_id = $userId");

  $_SESSION['login_type'] = 'alert-success';
  $_SESSION['login_msg'] = 'Successfully login!';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");
  exit();
} else {
  $result = $mydb->query("SELECT user_id FROM users WHERE login_id = '$loginId'");

  if ($result->num_rows != 0) {
    $userId = $result->fetch_row()[0];

    // update logs
    $result = $mydb->query("SELECT * FROM logs WHERE user_id = $userId");

    $row = $result->fetch_array(MYSQLI_ASSOC);


    $numLoginAttemp = $row['num_login_attempt'];
    $numLoginFail = $row['num_login_fail'];

    $mydb->query("UPDATE logs SET 
                        num_login_attempt = $numLoginAttemp + 1,
                        num_login_fail = $numLoginFail + 1
                        WHERE user_id = $userId");
  }

  $_SESSION['login_type'] = 'alert-danger';
  $_SESSION['login_msg'] = 'Wrong username or password!';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");
  exit();
}

$mydb->close();
?>
