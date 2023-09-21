<?php
  function get($parameter) {
    if (isset($_GET[$parameter])) {
      return strip_tags(trim(addslashes($_GET[$parameter])));
    }
    else {
      return false;
    }
  }
  function post($parameter) {
    if (isset($_POST[$parameter])) {
      return htmlspecialchars(trim(strip_tags($_POST[$parameter])));
    }
    else {
      return false;
    }
  }
  function checkUsername($username) {
    return preg_match("/[^a-zA-Z0-9_]/", $username);
  }
  function checkEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return false;
    }
    else {
      return true;
    }
  }
  function generateSalt($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }
  function createSHA256($password){
    $salt = generateSalt(16);
    $hash = '$SHA$'.$salt.'$'.hash('sha256', hash('sha256', $password).$salt);
    return $hash;
  }
  
  function checkSHA256($password, $realPassword){
    $parts = explode('$', $realPassword);
    $salt = $parts[2];
    $hash = hash('sha256', hash('sha256', $password).$salt);
    $hash = '$SHA$'.$salt.'$'.$hash;
    return (($hash == $realPassword) ? true : false);
  }
?>
