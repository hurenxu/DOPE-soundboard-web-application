<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add User Page</title>
</head>
<body>
<?php
session_start();
$pageTitle = 'Add user';
require('template/main_template.html');
$loginId = $_SESSION['loginId'];
require 'template/login_outter_nav_template.html';
//echo "<div class=\"container mt-3 pl-5\">";
//require('template/another_nav_template.html');
//echo "<div class=\" container row mt-2\">";
?>
<form style="margin: 0 35%" enctype="multipart/form-data" action="/add_admin_user.php" method="post">
    <label>First name*
        <input type="text" name="first_name"><br><br>
    </label>
    <label>Last name:*
        <input type="text" name="last_name"><br><br>
    </label>
    <label>
        Is Admin:*
        <input type="radio" name="is_admin" value="yes">yes
        <input type="radio" name="is_admin" value="no">no
    </label>
    <label>
        Email:*
        <input type="text" name="email">Login ID:*
        <input type="text" name="login_id">Password:*
    </label>
    <label>
        Password:*
        <input type="password" name="password">
    </label>
    * means that you cannot leave blank.<br><br>
    <input type="submit" value="submit">
</form>
</body>
</html>
