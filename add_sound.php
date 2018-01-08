<?php
require 'api.php';
session_start();

require_once "dbinfo.php.inc";
$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

$userId = checkLoginState($mydb);

//obtain board info
$boardId = intval($_SESSION['boardId']);

if(isset($_REQUEST['soundName'])) {
  // upload two file
  $soundPath = upload(0, $userId, $mydb, 'sound', 'sound');
  checkSoundType($soundPath);
  $imagePath = upload(1, $userId, $mydb, 'img', 'sound');
  checkImageType($imagePath);

//  test_input($_REQUEST['soundName']);
//  $soundName = check_input($_REQUEST['soundName']);
  $soundName = $_REQUEST['soundName'];
  //TODO if error happen, delete uploaded file

  // inter into db
  $ret = $mydb->query("INSERT INTO sounds 
                            (sound_name, sound_path, image_path, board_id, play_time) 
                            VALUES ('$soundName', '$soundPath', '$imagePath', $boardId, 0)");

  $numSound = intval($mydb->query("SELECT num_sound FROM logs WHERE user_id = $userId")
    ->fetch_row()[0]);

  $mydb->query("UPDATE logs SET 
                        num_sound = $numSound + 1
                        WHERE user_id = $userId");

  $mydb->close();

  $_SESSION['login_type'] = 'alert-success';
  $_SESSION['login_msg'] = 'Add Sound Success!';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");
  exit();
} else {
  header("Location: /error_html/404.html");

  $mydb->close();
  exit();
}

?>
