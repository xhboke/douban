<?php

class DouBan
{
    public const DouBanUrl = 'https://www.douban.com';
    public const Cookie = '';
    public const BusyTip = '检测到有异常请求从你的 IP 发出';

    /**  
     * 获取网页源码
     * 
     * @access public 
     * @param string $url 目标链接
     * @return string
     */
    public static function curl_get($url)
    {
        $oCurl = curl_init();
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($oCurl, CURLOPT_PROXY, "127.0.0.1");
        // curl_setopt($oCurl, CURLOPT_PROXYPORT, "10809");
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if (intval($aStatus["http_code"]) == 200) {
            return $sContent;
        } else {
            return "";
        }
    }

    /**  
     * POST方法
     * 
     * @access public 
     * @param string $url 目标链接
     * @param array $data 数据
     * @return string
     */
    public static function curl_post($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);
        }
        curl_close($curl);
        return $tmpInfo;
    }


    /**  
     * 取中间文本
     * 
     * @access public 
     * @param string $str 匹配文本
     * @param string $leftStr 匹配左文本
     * @param string $rightStr 匹配右文本
     * @return string
     */
    public static function getSubstr($str, $leftStr, $rightStr)
    {
        $left = strpos($str, $leftStr);
        $right = strpos($str, $rightStr, $left);
        if ($left < 0 or $right < $left) return '';
        return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
    }


    /**  
     * 正则匹配
     * 
     * @access public 
     * @param mixed $pattern 正则表达式
     * @param mixed $subject 匹配对象
     * @param mixed $order 匹配顺序号
     * @return array
     */
    public static function preg($pattern, $subject, $order)
    {
        preg_match_all($pattern, $subject, $_Return);
        return $order == 0 ? $_Return : $_Return[$order];
    }

    /**  
     * 去除数组空格
     * 
     * @access public 
     * @param array $params 数组
     * @return array|string
     */
    public function trimArray($params)
    {
        if (!is_array($params))
            return trim($params);
        return array_map([__CLASS__, 'trimArray'], $params);
    }

    /**
     * 数据转为Json格式
     * @access public
     * @param array
     * @return string
     */
    static public function Json($array)
    {
        return json_encode($array, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
    }
};
