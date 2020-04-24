<?php

// 接続に必要な情報を定数として定義
define('DSN', 'mysql:host=db;dbname=hairset_system;charset=utf8');
define('USER', 'hairset');
define('PASSWORD', '123456');

// Noticeというエラーを非表示にする
error_reporting(E_ALL & ~E_NOTICE);
