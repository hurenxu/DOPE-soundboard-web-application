<?php
session_start();
$pageTitle = 'Delete Sound';

require_once "dbinfo.php.inc";

$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

$boardId = $_SESSION['boardId'];

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

if (!isset($_REQUEST['action'])) {
  require 'template/main_template.html';
  require 'template/delete_confirm_template.html';

  echo '</body></html>';
} else {
  // check confirmation
  if($_REQUEST['action'] == "Cancel") {
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");
    exit();
  }

  // get sound Id
  $soundId = intval(substr($_SERVER['REQUEST_URI'], 4));

  //update log
  $numSound = $mydb->query("SELECT num_sound FROM logs WHERE user_id = $userId")
    ->fetch_row()[0];
  $mydb->query("UPDATE logs SET num_sound = $numSound - 1
                     WHERE user_id = $userId");

  // delete sound image and sound
  $rows = $mydb->query("SELECT image_path, sound_path FROM sounds 
                            WHERE sound_id = $soundId")->fetch_array(MYSQLI_ASSOC);
  unlink($rows['image_path']);
  unlink($rows['sound_path']);

  $mydb->query("DELETE FROM sounds WHERE sound_id = $soundId");

  $_SESSION['login_type'] = 'alert-success';
  $_SESSION['login_msg'] = 'Delete Sound Success!';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");
  exit();

  $mydb->close();
}
?>
