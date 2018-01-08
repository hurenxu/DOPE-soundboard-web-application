<?php
session_start();
$pageTitle = 'Delete User';

// check if user is login
if (!isset($_SESSION['loginId'])) {
  $alterType = "alert-warning";
  $errMsg = "Error.";
  require "/var/www/html/template/alter_template.php";

  exit();
}

if (!isset($_REQUEST['action'])) {
  require 'template/main_template.html';
  require 'template/delete_confirm_template.html';

  echo '</body></html>';
} else {
  // check confirmation
  if($_REQUEST['action'] == "Cancel") {
    header("Location: /905038544");
  }
  else {

    require_once "dbinfo.php.inc";

    $mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

    // obtain necessary info
    $loginId = $_SESSION['loginId'];
    $userId = $mydb->query("SELECT user_id from users WHERE login_id = '$loginId'")
      ->fetch_row()[0];

    // get log id
    $userDeleteId = intval(substr($_SERVER['REQUEST_URI'], 4));
    $logId = $mydb->query("SELECT log_id from logs WHERE user_id = $userDeleteId")
      ->fetch_row()[0];

    // delete log_and_board
    $boardDelete = $mydb->query("SELECT * FROM boards where user_id = $userDeleteId");
    $boardNum = $boardDelete->num_rows;

    for ($index = 0; $index < $boardNum; $index++) {
      $row = $boardDelete->fetch_array(MYSQLI_ASSOC);
      unlink($row['board_cover']);
      $boardID = $row['board_id'];

      // get all sounds in current sound board
      $result = $mydb->query("SELECT * FROM sounds where board_id = $boardID");
      $soundNum = $result->num_rows;

      // delete image and sound files from this soundboard
      for ($i = 0; $i < $soundNum; $i++) {
        $rowSound = $result->fetch_array(MYSQLI_ASSOC);
        unlink($rowSound['image_path']);
        unlink($rowSound['sound_path']);
      }

      $mydb->query("DELETE FROM sounds WHERE board_id = $boardID");
    }

    // delete all info related with user
    $mydb->query("DELETE FROM log_and_board WHERE log_id = $logId");
    $mydb->query("DELETE FROM boards WHERE user_id = $userDeleteId");
    $mydb->query("DELETE FROM logs WHERE user_id = $userDeleteId");
    $mydb->query("DELETE FROM users WHERE user_id = $userDeleteId");

    header("Location: /905038544");

    $mydb->close();
  }
}
?>
