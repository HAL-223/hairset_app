<?php

require_once('config.php');
require_once('functions.php');

$id = $_GET['id'];

$dbh = connectDb();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  // フォームに入力されたデータの受け取り
  $good = $_GET['good'];

  if ($good == "1") {
    $good_value = 1;
  } else {
    $good_value = 0;
  }

    // データを更新する処理
  $sql = "update styles set good = :good where id = :id";


  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(":id", $id);
  $stmt->bindParam(":user_id", $user_id);
  $stmt->bindParam(":style_id", $style_id);
  $stmt->bindParam(":good", $good);
  $stmt->execute();

  $url = $_SERVER['HTTP_REFERER'];
  header('Location:' . $url);
  exit;
}

