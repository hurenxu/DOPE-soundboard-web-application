<?php
require 'api.php';
$pageTitle = 'Board Page';

session_start();

require_once "dbinfo.php.inc";

$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

//get boardID from request url.
$boardId = intval(strtok($_SERVER['REQUEST_URI'], '/'));
$boardId = intval(strtok('/'));

$_SESSION['boardId'] = $boardId;

// check if user info is changed
if (isset($_SESSION['loginId'])) {
  // get user id by login_id
  $loginId = $_SESSION['loginId'];
  $result = $mydb->query("SELECT user_id from users WHERE login_id = '$loginId'");

  if ($result->num_rows != 0) {
    $userId = $result->fetch_row()[0];

    $logId = $mydb->query("SELECT log_id FROM logs WHERE user_id = $userId")
      ->fetch_row()[0];

    $result = $mydb->query("SELECT num_access FROM log_and_board 
                              WHERE board_id = $boardId AND log_id = $logId");

    if ($result->num_rows == 0) {
      $mydb->query("INSERT INTO log_and_board (log_id, board_id, num_access) 
                     VALUES ($logId, $boardId, 1)");
    } else {
      $numAccess = $result->fetch_row()[0];

      $mydb->query("UPDATE log_and_board SET num_access = $numAccess + 1
                     WHERE board_id = $boardId AND log_id = $logId");
    }
  } else {
    $errMsg = "Your account is changed! Please contact with administration!";
    $alterType = "alert-warning";
    errorPage($errMsg, $alterType);

    $_SESSION = array();
    session_destroy();

    exit();
  }
}

require "template/main_template.html";

if (isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] == 1) {
  $loginId = $_SESSION['loginId'];
  include 'template/login_outter_nav_template.html';
} else {
  require('template/unlogin_outter_nav_template.html');

}

$title = $mydb->query("SELECT board_title FROM boards WHERE board_id = $boardId")->fetch_row()[0];
$headingType = "sound";
$addUrl = '/as';
$listUrl = "/dl/$boardId/0/12";
$gridUrl = "/vg/$boardId/0";
$showMode = substr($_SERVER['REQUEST_URI'], 2, 1);
$sortMode = substr($_SERVER['REQUEST_URI'], 1, 1);

$_SESSION['sort_mode'] = $sortMode;
$aUrl = $sortMode . $showMode;

if (isset($userId)){
  $ret = $mydb->query("SELECT * FROM boards WHERE board_id = $boardId AND user_id = $userId");
  $isOwner = $ret->num_rows == 0 ? 0 : 1;
} else {
  $isOwner = 0;
}

require "/var/www/html/template/heading_template.html";

// render public board
$pageNum = intval(strtok('/'));
$pageMax = intval(strtok('/'));

$_SESSION['page_max'] = $pageMax;

if ($pageMax == 0) {
  $pageMax = 12;
}
$limitStart = $pageNum * 12;
if ($sortMode == 'n') {
  $result = $mydb->query("SELECT * FROM sounds WHERE board_id= $boardId ORDER BY sound_name LIMIT $limitStart,12");
} else {
  $result = $mydb->query("SELECT * FROM sounds WHERE board_id= $boardId ORDER BY sound_id DESC LIMIT $limitStart,12");
}

$resultNum = $result->num_rows;
$totalNum = $mydb->query("SELECT * FROM sounds WHERE board_id= $boardId")->num_rows;

$_SESSION['show_mode'] = $showMode;

// validate url
if ($pageNum > $totalNum / $pageMax || $pageNum < 0) {
  header("Location: /error_html/404.html");
  $mydb->close();
  exit();
}

require '/var/www/html/template/board_outter_template.html';

// render all sound
for ($index = 0; $index < $resultNum; $index++) {
  $row = $result->fetch_array(MYSQLI_ASSOC);

  $imgPath = $row['image_path'];
  $soundName = $row['sound_name'];
  $soundId = $row['sound_id'];
  $soundPath = $row['sound_path'];

  $deleteUrl = '/ds/' . $soundId;
  $modifyUrl = '/us/' . $soundId;
  $boardUrl = '/vg/' . $soundId;

  if ($showMode == "g") {
    require "template/sound_cell_template.html";
  } else{
    require "/var/www/html/template/list_sound_cell_template.html";
  }
}

// render pagination
$direct = substr($_SERVER['REQUEST_URI'], 1, 2);
if ($showMode == "l") {
  echo "</tbody></table></div></div>";
} else {
  echo "</div></section></div>";
}

require '/var/www/html/template/pagination_template.html';
require '/var/www/html/template/popup_window_template.html';

handleErrorMsg();
echo "</body></html>";

//close database
$mydb->close();
?>
