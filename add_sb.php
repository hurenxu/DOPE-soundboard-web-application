<?php
require 'api.php';
session_start();

// connect db
require_once "dbinfo.php.inc";

$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

$userId = checkLoginState($mydb);

if (isset($_REQUEST['boardTitle'])) {

  test_input($_REQUEST['boardTitle']);
  $boardTitle = check_input($_REQUEST['boardTitle']);
  // get sound board info
  $isPublic = $_REQUEST['isPublic'] ? 1 : 0;
  $boardCover = upload(0, $userId, $mydb, 'img', 'board');
  checkImageType($boardCover);

  $mydb->query("INSERT INTO boards (board_title, is_public, user_id, board_cover) 
                      VALUES ('$boardTitle', $isPublic, $userId, '$boardCover')");

  $mydb->close();

  $_SESSION['login_type'] = 'alert-success';
  $_SESSION['login_msg'] = 'Add Sound Board Success!';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");
  exit();
} else {
  header("Location: /error_html/404.html");

  $mydb->close();
  exit();
}
/**
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
    exit();$_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = 'You should login first!';
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
}*/
?>
