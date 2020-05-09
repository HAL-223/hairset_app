<?php

// 接続処理を行う関数
function connectDb()
{
  try {
    return new PDO(DSN, USER, PASSWORD);
  } catch (PDOException $e) {
    echo $e->getMessage();
    exit;
  }
}

// エスケープ処理を行う関数
function h($s)
{
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}
