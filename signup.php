<?php
require 'api.php';

session_start();

// check request
if($_REQUEST['loginId']) {
  require_once "dbinfo.php.inc";

  $mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

  test_input($_REQUEST['loginId']);
  test_input($_REQUEST['pwd']);
  test_input($_REQUEST['firstName']);
  test_input($_REQUEST['lastName']);
  test_input($_REQUEST['email']);

  $loginId = check_input($_REQUEST['loginId']);
  $pwd = check_input($_REQUEST['pwd']);
  if(strlen($pwd) < 8) {
    $_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = 'The password needs to be at least 8 characters.';
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");

    $mydb->close();
    exit();
  }
  $firstName = check_input($_REQUEST['firstName']);
  $lastName = check_input($_REQUEST['lastName']);
  $email = $_REQUEST['email'];

  if(filter_var($email,FILTER_VALIDATE_EMAIL)){
  }else{
    $_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = 'Sorry, invalid email!';
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");

    $mydb->close();

    exit();
  }

  $result = $mydb->query("SELECT * FROM users WHERE login_id = '$loginId'");

  // check if user id is exist
  if ($result->num_rows > 0) {
    $_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = 'Sorry, that login id is taken. Please go back and try again.';
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");

    $mydb->close();
    exit();
  }

  $mydb->query("INSERT INTO users (login_id, email, first_name, last_name, password, is_admin, num_add) 
VALUES ('$loginId', '$email', '$firstName', '$lastName', '$pwd', 0, 0)");

  // update logs
  $userId = $mydb->query("SELECT user_id  FROM users WHERE login_id = '$loginId'")
    ->fetch_row()[0];
  $mydb->query("INSERT INTO logs 
                       (user_id, num_sound, num_login_attempt, 
                       num_login_fail, num_login_success, num_logout) 
                       VALUES ($userId, 0, 1, 0, 1, 0)");


  $_SESSION['LoggedIn'] = 1;
  $_SESSION['loginId'] = $loginId;

  $_SESSION['login_type'] = 'alert-success';
  $_SESSION['login_msg'] = 'Successfully sign up!';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");

  $mydb->close();
} else {
  header("Location: /error_html/404.html");

  $mydb->close();
  exit();
}
?>
