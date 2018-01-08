<?php
session_start();

require_once "dbinfo.php.inc";

$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

// get user id by login_id
$loginId = $_SESSION['loginId'];
$result = $mydb->query("SELECT user_id from users WHERE login_id = '$loginId'");

// check if user is removed by administration
if ($result->num_rows != 0) {
  $userId = $result->fetch_row()[0];

  // update logs
  $result = $mydb->query("SELECT * FROM logs WHERE user_id = $userId");

  $row = $result->fetch_array(MYSQLI_ASSOC);

  $numLogout = $row['num_logout'];

  $mydb->query("UPDATE logs SET 
                        num_logout = $numLogout + 1
                        WHERE user_id = $userId");
}

$mydb->close();

$_SESSION = array();
session_destroy();

header("Location: /mp");
?>
