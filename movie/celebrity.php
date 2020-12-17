<?php
header("Access-Control-Allow-Origin:*");
header("Content-type: text/json; charset=utf-8");
include_once('douban.php');
$Id = isset($_GET['id']) ? $_GET['id'] : exit;

$douban = new douban;
$douban->Get_celebrity($Id);
print_r($douban->celebrityData);