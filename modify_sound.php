<?php
require 'api.php';
$pageTitle = 'Modify Sound';

session_start();

//get boardID from request url.
$soundId = intval(substr($_SERVER['REQUEST_URI'], 4));

require_once "dbinfo.php.inc";

$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

$userId = checkLoginState($mydb);

// check if request from form
if (isset($_REQUEST['soundName'])) {
  // get sound info
  test_input($_REQUEST['soundName']);
  $soundName = check_input($_REQUEST['soundName']);

  // upload two file
  $soundPath = upload(0, $userId, $mydb, 'sound', 'sound', $soundId);
  $imagePath = upload(1, $userId, $mydb, 'img', 'sound', $soundId);

  $mydb->query("UPDATE sounds SET
                      sound_name = '$soundName',
                      image_path = '$imagePath',
                      sound_path = '$soundPath'
                      WHERE sound_id = $soundId");

  $boardId = $mydb->query("SELECT board_id FROM sounds WHERE sound_id = $soundId")
    ->fetch_row()[0];

  $_SESSION['login_type'] = 'alert-success';
  $_SESSION['login_msg'] = 'Update Sound Success!';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");
  exit();
} else {
  $result = $mydb->query("SELECT * FROM sounds WHERE sound_id = $soundId");

  // check if board is exist
  if ($result->num_rows == 0) {
    http_response_code(404);

    $mydb->close();

    exit();
  }

  require 'template/main_template.html';
  if (isset($_SESSION['loginId'])) {
    require 'template/login_outter_nav_template.html';
  } else {
    require 'template/unlogin_outter_nav_template.html';
  }

  //obtain board information for tempalte
  $rows = $result->fetch_array(MYSQLI_ASSOC);

  $soundName = $rows['sound_name'];
  $imagePath = $rows['image_path'];
  $soundPath = $rows['sound_path'];

  require '/var/www/html/template/modify_sound_form_template.html';
  echo '</div></html>';
}

$mydb->close();
?>
