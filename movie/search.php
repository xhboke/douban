<?php
header("Access-Control-Allow-Origin:*");
header("Content-type: text/json; charset=utf-8");

include_once('douban.php');
$SearchName = isset($_GET['s']) ? $_GET['s'] : exit;
$PageCount = isset($_GET['page']) ? $_GET['page'] : 0;

$douban = new douban;

$douban->Search($SearchName, $PageCount);
print_r($douban->SearchData);
