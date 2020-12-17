<?php
header("Access-Control-Allow-Origin:*");
header("Content-type: text/json; charset=utf-8");
include_once('douban.php');
$douban = new douban;
$page = isset($_GET['page']) ? $_GET['page'] : '0';

$douban->top250($page);
print_r($douban->top250Data);