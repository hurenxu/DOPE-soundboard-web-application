<?php
$pageTitle = 'Modify User';

session_start();

//get boardID from request url.
$userID = intval(substr($_SERVER['REQUEST_URI'], 4));
require_once "dbinfo.php.inc";
$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
// check if user is login
if (!isset($_SESSION['loginId'])) {
  $alterType = "alert-warning";
  $errMsg = "Error.";
  require "/var/www/html/template/alter_template.php";

  exit();
}
if(isset($_REQUEST['email'])) {

test_input($_REQUEST['loginid']);
test_input($_REQUEST['email']);
test_input($_REQUEST['isadmin']);
test_input($_REQUEST['firstname']);
test_input($_REQUEST['lastname']);
test_input($_REQUEST['password']);
$loginid = check_input($_REQUEST['loginid']);
$email = $_REQUEST['email'];
if(filter_var($email,FILTER_VALIDATE_EMAIL)){
}else{
  echo"<script>window.alert(\"Sorry, invalid email.\")</script>";
  header("Location: #");
  exit();
}
$isadmin = intval($_REQUEST['isadmin']);
$firstname = check_input($_REQUEST['firstname']);
$lastname = check_input($_REQUEST['lastname']);
$password = check_input($_REQUEST['password']);

$mydb->query("UPDATE users SET
		login_id = '$loginid',
		email = '$email',
		is_admin = '$isadmin',
		first_name = '$firstname',
		last_name = '$lastname',
		password = '$password'
		WHERE user_id= '$userID'");

header("Location: /905038544");
}
else {
$result = $mydb->query("SELECT * FROM users WHERE user_id = $userID");
// check if board is exist
if ($result->num_rows == 0) {
  header("HTTP/1.0 404 Not Found");
  $mydb->close();
  exit();
}

require 'template/main_template.html';
$pageTitle = 'Modify User';
if (isset($_SESSION['loginId'])) {
  require '/var/www/html/template/login_outter_nav_template.html';
} else {
  require '/var/www/html/template/login_outter_nav_template.html';
}

//obtain board information for tempalte
$rows = $result->fetch_array(MYSQLI_ASSOC);

$loginid = $rows['login_id'];
$email = $rows['email'];
$isadmin = $rows['is_admin'];
$firstname = $rows['first_name'];
$lastname = $rows['last_name'];
$password = $rows['password'];

require 'template/modify_user_form_template.html';
echo '</div></html>';

$mydb->close();
}
  function test_input($data) {
    if(!isset($data)) {
      $_SESSION['login_type'] = 'alert-danger';
      $_SESSION['login_msg'] = 'Cannot leave blank(s)!';
      $previousUrl = $_SERVER['HTTP_REFERER'];
      header("Location: $previousUrl");
      exit();
    }
    elseif(strpos($data, "script")) {
      $_SESSION['login_type'] = 'alert-danger';
      $_SESSION['login_msg'] = 'You cannot input script, are you hacker?';
      $previousUrl = $_SERVER['HTTP_REFERER'];
      header("Location: $previousUrl");
      exit();
   
    }
  }

function check_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
