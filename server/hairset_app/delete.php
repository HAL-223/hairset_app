<?php

require_once('config.php');
require_once('functions.php');

session_start();

$id = $_GET['id'];
if (!is_numeric($id)) {
  header('Location: index.php');
  exit;
}

$dbh = connectDb();
$sql = "select * from styles";
// $sql = 'SELECT * FROM styles WHERE id = :id';
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// $style = $stmt->fetch();

// if (empty($style))
// {
//   header('Location: index.php');
//   exit;
// }

$style = $stmt->fetch(PDO::FETCH_ASSOC);
if (empty($style)) {
  header('Location: index.php');
  exit;
}

$sql_delete = 'DELETE FROM styles WHERE id = :id';
$stmt = $dbh->prepare($sql_delete);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

header('Location: index.php');
exit;