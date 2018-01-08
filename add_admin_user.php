<?php
require 'api.php';
session_start();

// check if user is login
if (!isset($_SESSION['loginId'])) {
  header("Location: /error_html/404.html");

  exit();
}

//require('template/main_template.html');
$loginId = $_SESSION['loginId'];
/**require 'template/login_outter_nav_template.html';
echo "<div class=\"container mt-3 pl-5\">";
require('template/inner_nav_template.html');

echo "<div class=\" container row mt-2\">";*/
require_once "dbinfo.php.inc";

$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

test_input($_REQUEST['first_name'] );
test_input( $_REQUEST['last_name']);
test_input($_REQUEST['is_admin'] );
test_input( $_REQUEST['email']);
test_input( $_REQUEST['login_id']);
test_input( $_REQUEST['password']);

$firstname = check_input($_REQUEST['first_name']);
$lastname = check_input($_REQUEST['last_name']);
$isadmin = $_REQUEST['is_admin'];

if($isadmin == "yes") {
  $isadmin = 1;
}
else {
  $isadmin = 0;
}

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

$email = check_input($_REQUEST['email']);
$loginID = check_input($_REQUEST['login_id']);
$numadd = 0;
$password = check_input($_REQUEST['password']);
$result = $mydb->query("SELECT * FROM users WHERE login_id = '$loginID'");

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
    VALUES ('$loginID', '$email', '$firstname', '$lastname', '$password', $isadmin, $numadd)");

// update logs
$userId = $mydb->query("SELECT user_id  FROM users WHERE login_id = '$loginID'")
  ->fetch_row()[0];
$mydb->query("INSERT INTO logs 
      (user_id, num_sound, num_login_attempt, 
       num_login_fail, num_login_success, num_logout) 
      VALUES ($userId, 0, 0, 0, 0, 0)");

$_SESSION['login_type'] = 'alert-success';
$_SESSION['login_msg'] = 'Successfully sign up!';
$previousUrl = $_SERVER['HTTP_REFERER'];
header("Location: $previousUrl");

$mydb->close();
?>
