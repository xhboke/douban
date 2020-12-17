<?php
header("Access-Control-Allow-Origin:*");
header("Content-type: text/json; charset=utf-8");


include_once('douban.php');
$douban = new douban;
$mode = isset($_GET['mode']) ? $_GET['mode'] : '热门'; //'热门','最新','经典'
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'recommend'; //热度：recommend时间：time 评价：rank
$page = isset($_GET['page']) ? $_GET['page'] : 0; //热度：recommend时间：time 评价：rank
$douban->Movie($mode, $sort, '24', $page);
print_r(json_encode($douban->GetMovieArr));
