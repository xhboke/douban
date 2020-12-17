<?php
header("Access-Control-Allow-Origin:*");
header("Content-type: text/json; charset=utf-8");
include_once('douban.php');
$Id = isset($_GET['id']) ? $_GET['id'] : exit;
$PageCount = isset($_GET['page']) ? $_GET['page'] : 0;
$douban = new douban;
$douban->IdReviews($Id,$PageCount);
print_r($douban->IdReviewData);