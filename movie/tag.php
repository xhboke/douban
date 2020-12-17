<?php
header("Access-Control-Allow-Origin:*");
header("Content-type: text/json; charset=utf-8");
include_once('douban.php');
$douban = new douban;

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'U';
$tags = isset($_GET['tags']) ? $_GET['tags'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$genres = isset($_GET['genres']) ? $_GET['genres'] : '';
$countries = isset($_GET['countries']) ? $_GET['countries'] : '';
$year_range = isset($_GET['year_range']) ? $_GET['year_range'] : '';

$douban->Get_tag($sort,$tags,$page,$genres,$countries,$year_range);
print_r($douban->tagData);