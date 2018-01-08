<?php
$pageTitle = 'Alter Page';

  require '/var/www/html/template/main_template.html';
  $pageTitle = 'Warning';
  if (isset($_SESSION['loginId'])) {
    $loginId = $_SESSION['loginId'];
    require '/var/www/html/template/login_outter_nav_template.html';
  } else {
    require '/var/www/html/template/unlogin_outter_nav_template.html';
  }

  echo "	<div class=\"container\" style=\"margin-top: 7rem; text-align: center\">";

  echo "<div class=\"alert $alterType \" role=\"alert\">
         $errMsg <a href=\"$goBackUrl\" class=\"alert-link\">Go Back(click me!)</a>.</div>";

  echo "</div></body></html>";
?>
