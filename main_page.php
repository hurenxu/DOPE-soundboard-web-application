<?php
$pageTitle = 'Sound Board';

require '/var/www/html/api.php';
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
    $_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = 'Your account is changed! Please contact with administration!!';
    $previousUrl = $_SERVER['HTTP_REFERER'];

    $_SESSION = array();
    session_destroy();

    $mydb->close();

    header("Location: $previousUrl");

    exit();
  }

  require '/var/www/html/template/login_outter_nav_template.html';
} else {
  require('/var/www/html/template/unlogin_outter_nav_template.html');
}

// check if connection is built
if ($mydb->connect_errno) {
  echo "<div class=\"alert alert-warning m-auto\" role=\"alert\">Connect failed: $mydb->connect_error </div>";
  echo "</body></html>";
  exit();
} else {
  require '/var/www/html/template/heading_template.html';
}

//get page number
$pageNum = intval(substr($_SERVER['REQUEST_URI'], 4));
$limitStart = $pageNum * 3;

$result = $mydb->query("SELECT * FROM boards WHERE is_public = 1 ORDER BY board_id DESC LIMIT $limitStart,3");
$resultNum = $result->num_rows;
$totalNum = $mydb->query("SELECT * FROM boards WHERE is_public = 1")->num_rows;

$pageMax = 3;

// validate url
if ($pageNum > $totalNum / $pageMax || $pageNum < 0) {
  header("Location: /error_html/404.html");
  exit();
}

require '/var/www/html/template/board_outter_template.html';
// render all sound boards
for ($index = 0; $index < $resultNum; $index++) {
  $row = $result->fetch_array(MYSQLI_ASSOC);

  $userId = $row['user_id'];

  $loginId= $mydb->query("SELECT login_id FROM users WHERE user_id = $userId")->fetch_row()[0];

  $imgPath = $row['board_cover'];
  $boardTitle = $row['board_title'];
  $boardId = $row['board_id'];
  $isPublic = $row['is_public'];

  $deleteUrl = '/db/' . $boardId;
  $modifyUrl = '/ub/' . $boardId;
  $boardUrl = '/vg/' . $boardId  . '/0';

  require "/var/www/html/template/cell_template.html";
}

echo '</div></section></div>';
$direct = "mp";
// render pagination
require '/var/www/html/template/pagination_template.html';
require '/var/www/html/template/popup_window_template.html';

handleErrorMsg();

echo "</body></html>";
//close database
$mydb->close();
?>
