<?php
require '/var/www/html/api.php';

session_start();
$pageTitle = 'Administration Page';

if(!isset($_SESSION['loginId'])) {
  $_SESSION['login_type'] = 'alert-danger';
  $_SESSION['login_msg'] = 'Please log in first.';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");
  exit();
}

require_once("dbinfo.php.inc");
$mydb = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

$loginId = $_SESSION['loginId'];
$isAdmin = $mydb->query("SELECT is_admin FROM users WHERE login_id = '$loginId'")->fetch_row()[0];

if(!$isAdmin) {
  $_SESSION['login_type'] = 'alert-danger';
  $_SESSION['login_msg'] = 'You are not admin user.';
  $previousUrl = $_SERVER['HTTP_REFERER'];
  header("Location: $previousUrl");
  exit();
}

require 'template/main_template.html';
require 'template/admin_simple_template.html';
if ($_SESSION['LoggedIn'] == 1) {
  $loginId = $_SESSION['loginId'];
  require 'template/login_outter_nav_template.html';
} else {
  require('template/unlogin_outter_nav_template.html');
}

$result = $mydb->query('SELECT * FROM boards');
$user = $mydb->query("SELECT * FROM users WHERE is_admin = '0'");
$admin = $mydb->query("SELECT * FROM users WHERE is_admin = '1'");
$cellType = 'board';

// render all sound
for ($index = 0; $index < $result->num_rows; $index++) {
  $row = $result->fetch_array(MYSQLI_ASSOC);

  $imgPath = $row['board_cover'];
  $boardTitle = $row['board_title'];
  $boardId = $row['board_id'];
  $isPublic = $row['is_public'];

  $deleteUrl = '/db/' . $boardId;
  $modifyUrl = '/ub/' . $boardId;
  $boardUrl = '/vg/' . $boardId . '/0';

  require "template/cell_template_new_admin.html";
}

for ($index = 0; $index < $user->num_rows; $index++) {
  $rowUser = $user->fetch_array(MYSQLI_ASSOC);
  $name = $rowUser['first_name']." ".$rowUser['last_name'];
  $userId = $rowUser['user_id'];

  $deleteUrl = '/du/' . $userId;
  $modifyUrl = '/uu/' . $userId;
  require "template/user_cell_template.html";
}

for ($index = 0; $index < $admin->num_rows; $index++) {
  $rowAdmin = $admin->fetch_array(MYSQLI_ASSOC);
  $adminName = $rowAdmin['first_name']." ".$rowAdmin['last_name'];
  $adminId = $rowAdmin['user_id'];

  $deleteUrl = '/du/' . $adminId;
  $modifyUrl = '/uu/' . $adminId;
  require "template/admin_cell_template.html";
}

echo '</div></section></div>';
$headingType = 'board';
require '/var/www/html/template/popup_window_template.html';
handleErrorMsg();
echo "</body></html>";
?>
