<?php
function http_get($url){
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return false;
        }
}

$url = isset($_GET['url']) ? $_GET['url'] : '';

if(empty($url)){
    echo '请选择播放链接';
}else{
$data = http_get($url);
    if(strpos($data,'var redirecturl = "http://vip.okzybo.com";') !== false){
        header('Location: '.$url);
    }else{
        header('Location: https://api.sigujx.com/?url='.$url);   
    }
}


