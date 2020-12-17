<?php
header("Access-Control-Allow-Origin:*");
header("Content-type: text/json; charset=utf-8");
include_once('douban.php');
$Id = isset($_GET['id']) ? $_GET['id'] : exit;
$douban = new douban;

$douban->IdInfo($Id);
$douban->IdInfoData['url']['status'] = 0;
$douban->IdInfoData['url']['type'] = 'movie';
$douban->IdInfoData['url']['data'][0]['from'] = $data[$Id]['play_name'];
$douban->IdInfoData['url']['data'][0]['url'] = urlencode($data[$Id]['play_url']);
print_r(json_encode($douban->IdInfoData, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT));





