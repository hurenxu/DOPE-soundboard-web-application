<?php

function errorPage($errMsg, $alterType, $goBackUrl = '/mp/0') {

  require "/var/www/html/template/alter_template.php";
  require "/var/www/html/template/popup_window_template.html";
}

/**
 * @param $mydb database
 * @return mixed user Name
 */
function checkLoginState($mydb) {
  if (!isset($_SESSION['loginId'])) {
    $_SESSION = array();

    $_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = 'Please Login in first!';
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");

    $mydb->close();

    exit();
  } else {
    // get user id by login_id
    $loginId = $_SESSION['loginId'];
    $result = $mydb->query("SELECT user_id from users WHERE login_id = '$loginId'");

    // check if user info is changed
    if ($result->num_rows == 0) {
      $_SESSION = array();

      $_SESSION['login_type'] = 'alert-danger';
      $_SESSION['login_msg'] = 'Your account is changed. Please contact with administration!';
      $previousUrl = $_SERVER['HTTP_REFERER'];
      header("Location: $previousUrl");

      $mydb->close();

      exit();
    } else {
      return $result->fetch_row()[0];
    }
  }
}
/**
 * handle error message
 */
function handleErrorMsg() {
  if (isset($_SESSION['login_type'])) {
    $alterType = $_SESSION['login_type'];
    $message = $_SESSION['login_msg'];
    require 'template/show_login_message_js.html';

    unset($_SESSION['login_type']);
    unset($_SESSION['Login_msg']);
  }
}

/**
 * @param $index position of file
 * @param $userId user id
 * @param $mydb database
 * @param $allowdType aloowtype
 * @param $itemType item type
 * @param int $itemId item id
 * @return string new path
 */
function upload($index, $userId, $mydb, $allowdType, $itemType, $itemId = -1) {
  // obtain numAdd
  $numAdd = $mydb->query("SELECT num_add FROM users WHERE user_id = $userId")
    ->fetch_row()[0];

  if ($itemId != -1) {
    if ($itemType == 'sound') {
      $rows = $mydb->query("SELECT image_path, sound_path FROM sounds WHERE sound_id = $itemId")
        ->fetch_array(MYSQLI_ASSOC);

      // check type
      if ($allowdType == 'sound') {
        $oldPath = $rows['sound_path'];
      } else {
        $oldPath = $rows['image_path'];
      }
    } else {
      $oldPath = $mydb->query("SELECT board_cover FROM boards WHERE board_id = $itemId")
        ->fetch_row()[0];
    }
  }

  // check if file changed
  if (!$_FILES['upload']['tmp_name'][$index]) {
    return $oldPath;
  }

  //Obtain the temp file path
  $tmpPath = $_FILES['upload']['tmp_name'][$index];

  if ($allowdType == 'sound') {
    checkSoundType($tmpPath);
  } else {
    checkImageType($tmpPath);
  }

  //Setup our new file path
  $newPath = "$allowdType/" . $userId . "_" . $numAdd;

  $mydb->query("UPDATE users SET num_add = $numAdd + 1 WHERE user_id = $userId;");

  if ($itemId != -1) {
    unlink($oldPath);
  }

  //crop and resize picture
  if ($allowdType == 'img') {
    $width = 310;
    $height = 170;
    list($width_orig, $height_orig) = getimagesize($tmpPath);

    $impage_p = imagecreatetruecolor($width, $height);

    $file_type = mime_content_type($tmpPath);

    if (strpos($file_type, 'png') == true) {
      $image = imagecreatefrompng($tmpPath);
    } else {
      $image = imagecreatefromjpeg($tmpPath);
    }

    imagecopyresampled($impage_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

    if (strpos($file_type, 'png') == true) {
      imagepng($impage_p, $newPath);
    } else {
      imagejpeg($impage_p, $newPath);
    }

    return $newPath;
  }

  //Upload the file into the temp dir
  if(!move_uploaded_file($tmpPath, $newPath)) {
    $_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = 'Sorry, upload file fail! Please choose another file.!';
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");
    exit();
  }


  return $newPath;
}

function checkImageType($file) {
  $file_type = image_type_to_mime_type(exif_imagetype($file));
  if((strpos($file_type, 'png') == false) && (strpos($file_type, 'jpeg') == false)) {
    unlink($file);
    $_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = 'Sorry, this is not an image type!';
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");
    exit();
  }
}
function checkSoundType($file) {
  $file_type = mime_content_type($file);
  if((strpos($file_type, 'mpeg') == false) && (strpos($file_type, 'wav') == false) && (strpos($file_type, 'ogg') == false)) {
    unlink($file);
    $_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = "Sorry, this is not an audio type! $file_type";
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");
    exit();
  }
}

function test_input($data) {
  if(!isset($data)) {
    $_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = 'Cannot leave blank(s)!';
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");
    exit();
  }
  elseif(strpos($data, "script")) {
    $_SESSION['login_type'] = 'alert-danger';
    $_SESSION['login_msg'] = 'You cannot input script, are you hacker?';
    $previousUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $previousUrl");
    exit();
  }
}

function check_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
