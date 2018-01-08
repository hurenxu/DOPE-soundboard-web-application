<?php
require 'api.php';
$pageTitle = 'Modify Sound Board';

session_start();

//get boardID from request url.
$boardId = intval(substr($_SERVER['REQUEST_URI'], 4));

require_once "dbinfo.php.inc";

$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

$userId = checkLoginState($mydb);

// check if request is submit from form
if (isset($_REQUEST['boardTitle'])) {
  test_input($_REQUEST['boardTitle']);
  // get sound board info
  $boardTitle = check_input($_REQUEST['boardTitle']);
  $isPublic = $_REQUEST['isPublic'] ? 1 : 0;
  $boardCover = upload(0, $userId, $mydb, 'img', 'board', $boardId);
//  $boardCover = uploadFile(0, $userID, $mydb, $boardId);

  $mydb->query("UPDATE boards SET
                      board_title = '$boardTitle',
                      is_public = $isPublic,
                      board_cover = '$boardCover'
                      WHERE board_id = $boardId");

  $_SESSION['login_type'] = 'alert-success';
  $_SESSION['login_msg'] = 'Update Sound Board Success!';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");
  exit();
}

$mydb->close();
?>
