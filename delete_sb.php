<?php
session_start();
$pageTitle = 'Delete Sound Board';

require_once "dbinfo.php.inc";
$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

// check if user is login
if (!isset($_SESSION['loginId'])) {
  $_SESSION['login_type'] = 'alert-danger';
  $_SESSION['login_msg'] = 'You should login first!';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");

  $_SESSION = array();
  session_destroy();

  $mydb->close();
  exit();
}

// get user id by login_id
$loginId = $_SESSION['loginId'];
$result = $mydb->query("SELECT user_id from users WHERE login_id = '$loginId'");

// check if user info is changed
if ($result->num_rows == 0) {
  $_SESSION['login_type'] = 'alert-danger';
  $_SESSION['login_msg'] = 'Your account is changed! Please contact with administration!!';
  $previousUrl = $_SERVER['HTTP_REFERER'];

  $_SESSION = array();
  session_destroy();

  $mydb->close();

  header("Location: $previousUrl");

  exit();
} else {
  $userId = $result->fetch_row()[0];
}


if (!$_REQUEST['action']) {
  require 'template/main_template.html';
  require 'template/delete_confirm_template.html';
  echo '</body></html>';
} else {
  // check confirmation
  if($_REQUEST['action'] == "Cancel") {
    //todo goback previous
    header("Location: /cg/0");
  }

  // get board Id
  $boardId = intval(substr($_SERVER['REQUEST_URI'], 4));
  // get all sounds in current sound board
  $result = $mydb->query("SELECT * FROM sounds where board_id = $boardId");
  $deletedNumSound = $result->num_rows;
  // delete image and sound files from this soundboard
  for ($index = 0; $index < $result->num_rows; $index++) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    unlink($row['image_path']);
    unlink($row['sound_path']);
  }
  // remove sound record from this soundboard
  $mydb->query("DELETE FROM sounds WHERE board_id = $boardId");
  //update log
  $numSound = $mydb->query("SELECT num_sound FROM logs WHERE user_id = $userId")
    ->fetch_row()[0];
  $mydb->query("UPDATE logs SET num_sound = $numSound - $deletedNumSound
                     WHERE user_id = $userId");
  // delete boards
  $boardCover = $mydb->query("SELECT board_cover FROM boards 
                                       WHERE board_id = $boardId")->fetch_row()[0];
  // delete log_and_board
  $mydb->query("DELETE FROM log_and_board WHERE board_id = $boardId");
  unlink($boardCover);
  $mydb->query("DELETE FROM boards WHERE board_id = $boardId");

  $_SESSION['login_type'] = 'alert-success';
  $_SESSION['login_msg'] = 'Delete Sound Board Success!';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");
  exit();
}

$mydb->close();
?>
