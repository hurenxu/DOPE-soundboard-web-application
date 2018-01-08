<?php
require 'api.php';
$pageTitle = 'Personal Page';

require('/var/www/html/template/main_template.html');

require_once "/var/www/html/dbinfo.php.inc";

$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

session_start();

// check if logged In
if (isset($_SESSION['LoggedIn'])) {
  // get user id by login_id
  $loginId = $_SESSION['loginId'];
  $result = $mydb->query("SELECT user_id from users WHERE login_id = '$loginId'");

  // check if user info is changed
  if ($result->num_rows == 0) {
    $errMsg = "Your accound is changed, please contact with Administration.";
    $alterType = "alert-warning";
    errorPage($errMsg, $alterType);

    session_destroy();

    $mydb->close();
    exit();
  } else {
    $userId = $result->fetch_row()[0];
  }

  require '/var/www/html/template/login_outter_nav_template.html';
} else {
  header("Location: /error_html/404.html");

  $mydb->close();
  exit();
}

$headingType = 'board';
$addUrl = '/ab';
$isPublic = substr($_SERVER['REQUEST_URI'], 1, 1) == "c" ? 1 : 0;
$title = $isPublic == 1 ? "Public Boards" : "Private Boards";
$isOwner = 1;
require '/var/www/html/template/heading_template.html';

// render public board
$pageNum = intval(substr($_SERVER['REQUEST_URI'], 4));
$limitStart = $pageNum * 3;
$isPublic = substr($_SERVER['REQUEST_URI'], 1, 1) == "c" ? 1 : 0;
$showMode = substr($_SERVER['REQUEST_URI'], 2, 1);

$result = $mydb->query("SELECT * FROM boards WHERE is_public = $isPublic
                              AND user_id = $userId ORDER BY board_id DESC LIMIT $limitStart,3");
$resultNum = $result->num_rows;
$totalNum = $mydb->query("SELECT * FROM boards WHERE is_public = $isPublic
                               AND user_id = $userId")->num_rows;

// store in session
$_SESSION['is_public'] = $isPublic;
$_SESSION['show_mode'] = $showMode;

$pageMax = 3;

// validate url
if ($pageNum > $totalNum / $pageMax || $pageNum < 0) {
  header("Location: /error_html/404.html");
  $mydb->close();
  exit();
}

require '/var/www/html/template/board_outter_template.html';
// render all sound boards
for ($index = 0; $index < $resultNum; $index++) {
  $row = $result->fetch_array(MYSQLI_ASSOC);

  $imgPath = $row['board_cover'];
  $boardTitle = $row['board_title'];
  $boardId = $row['board_id'];
  $isPublic = $row['is_public'];

  $deleteUrl = '/db/' . $boardId;
  $modifyUrl = '/ub/' . $boardId;
  $boardUrl = '/vg/' . $boardId  . '/0';

  require "/var/www/html/template/cell_template.html";
}

$direct = substr($_SERVER['REQUEST_URI'], 1, 2);
echo "</div></section></div></div>";

// render pagination
require '/var/www/html/template/pagination_template.html';
require '/var/www/html/template/popup_window_template.html';

handleErrorMsg();
echo "</body></html>";

//close database
$mydb->close();
?>
