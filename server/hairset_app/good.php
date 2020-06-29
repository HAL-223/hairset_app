<?php

require_once('config.php');
require_once('functions.php');

$id = $_GET['id'];
$user_id = $_GET['user_id'];
$style_id  = $_GET['style_id'];

$dbh = connectDb();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if ($id) {
    $sql = "delete from good where id = :id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":id", $id);
  } else {
    $sql = "insert into good (user_id, style_id) values (:user_id, :style_id)";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":style_id", $style_id);
  }
  
  $stmt->execute();

  $url = $_SERVER['HTTP_REFERER'];
  header('Location:' . $url);
  exit;
}

