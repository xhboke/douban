<?php
/**
 * @author <xhboke>
 * @since <2.0.0>
 * @GitHub https://github.com/xhboke/douban
 */
header("Access-Control-Allow-Origin:*");
header("Content-type: text/json; charset=utf-8");

$Type = isset($_GET['type']) ? $_GET['type'] : '';
$douban = new douban;
if ($Type == 'movie') {
    $mode = isset($_GET['mode']) ? $_GET['mode'] : exit(); //'热门','最新','经典'
    $sort = isset($_GET['sort']) ? $_GET['sort'] : exit(); //热度：recommend时间：time 评价：rank
    $page = isset($_GET['page']) ? $_GET['page'] : exit; //热度：recommend时间：time 评价：rank
    $douban->Movie($mode, $sort, '24', $page);
    print_r(json_encode($douban->GetMovieArr));
} elseif ($Type == 'tv') {
    $douban = new douban;
    $mode = isset($_GET['mode']) ? $_GET['mode'] : ''; //'热门','美剧','英剧'
    $sort = isset($_GET['sort']) ? $_GET['sort'] : ''; //热度：recommend时间：time 评价：rank
    $page = isset($_GET['page']) ? $_GET['page'] : exit; //热度：recommend时间：time 评价：rank
    $douban->Tv($mode, $sort, '24', $page);
    print_r(json_encode($douban->GetTvArr));
} elseif ($Type == 'info') {
    $Id = isset($_GET['id']) ? $_GET['id'] : exit;
    $douban->IdInfo($Id);
    print_r(json_encode($douban->IdInfoData));
} elseif ($Type == 'reviews') {
    $Id = isset($_GET['id']) ? $_GET['id'] : exit;
    $PageCount = isset($_GET['page']) ? $_GET['page'] : '';
    $douban->IdReviews($Id, $PageCount);
    print_r(json_encode($douban->IdReviewData));
} elseif ($Type == 'search') {
    $SearchName = isset($_GET['s']) ? $_GET['s'] : exit();
    $PageCount = isset($_GET['page']) ? $_GET['page'] : '';
    $douban->Search($SearchName, $PageCount);
    print_r($douban->SearchData);
} else {
    exit;
}



class douban
{
    var $SearchData;
    var $IdInfoData;
    var $IdReviewData;
    var $IdPhotosData;
    var $GetMovieArr;
    var $GetTvArr;
    /*
    status:0为正常
    Count:搜索结果数
    Data:[[Id,Img,Rating],...]
    */
    public function Search($SearchName, $PageCount = "0")
    {
        $SearchUrl = 'https://m.douban.com/j/search/?q=' . $SearchName . '&t=movie&p=' . $PageCount;
        $ApiData = json_decode(self::http_get($SearchUrl));
        preg_match_all('#<span class="subject-title">(.*)<\/span>#', $ApiData->html, $ReturnName);
        preg_match_all('#<img src="(.*)"\/>#', $ApiData->html, $ReturnImg);
        preg_match_all('#\/movie\/subject\/(.*)\/">#', $ApiData->html, $ReturnId);
        preg_match_all('#<span>(.*)<\/span>#', $ApiData->html, $ReturnRating);
        if (empty($ReturnId[1])) {
            $SearchData['status'] = 1;
            $SearchData['Count'] = $ApiData->count;
        } else {
            $SearchData['status'] = 0;
            $SearchData['Count'] = $ApiData->count;
            for ($x = 0; $x < count($ReturnId[1]); $x++) {
                $SearchData['Data'][$x]['Id'] = $ReturnId[1][$x];
                $SearchData['Data'][$x]['Name'] = $ReturnName[1][$x];
                $SearchData['Data'][$x]['Img'] = $ReturnImg[1][$x];
                $SearchData['Data'][$x]['Rating'] = $ReturnRating[1][$x];
            }
        }
        $this->SearchData = json_encode($SearchData);
    }


    public function IdInfo($Id)
    {
        $IdUrl = 'https://movie.douban.com/subject/' . $Id . '/';
        $UrlData = self::http_get($IdUrl);
        $ReturnData['info'] = self::IdInfoArr($UrlData);
        $ReturnData['url'] = self::GetEpisodeUrl($UrlData);
        $ReturnData['recommend'] = self::Get_recommen_dations($UrlData);
        $this->IdInfoData = $ReturnData;
    }

    public function IdInfoArr($UrlData)
    {
        preg_match_all('#"name": "([\s\S]*?)",#', $UrlData, $PlayName);
        preg_match_all('#<span property="v:genre">([\s\S]*?)<\/span>#', $UrlData, $PlayGenre);
        preg_match_all('#"datePublished": "([\s\S]*?)",#', $UrlData, $PlayDatapublish);
        preg_match_all('#property="v:average">([\s\S]*?)<\/strong>#', $UrlData, $PlayRating);
        preg_match_all('#"image": "([\s\S]*?)",#', $UrlData, $PlayImg);
        preg_match_all('#"name": "([\s\S]*?)"#', self::GetSubstr($UrlData, '"actor":', 'datePublished'), $PlayActor);
        preg_match_all('#<span property="v:votes">([\s\S]*?)<\/span>#', $UrlData, $PlayVotes);
        preg_match_all('#<span class="year">([\s\S]*?)<\/span>#', $UrlData, $PlayYear);
        //preg_match_all('#<span property="v:runtime" content="([\s\S]*?)">#', $UrlData, $PlayRuntime);
        if (strpos($UrlData, '<span class="all hidden">') !== false) {
            preg_match_all('#<span class="all hidden">([\s\S]*?)<\/span>#', $UrlData, $PlayDesc);
        } else {
            preg_match_all('#<span property="v:summary" class="">([\s\S]*?)<\/span>#', $UrlData, $PlayDesc);
        }
        $IdInfoData['Name'] = $PlayName[1][0];
        $IdInfoData['PlayDesc'] = trim($PlayDesc[1][0]);
        $IdInfoData['DatePublished'] = $PlayDatapublish[1][0];
        $IdInfoData['Genre'] = $PlayGenre[1];
        $IdInfoData['Rating'] = floatval($PlayRating[1][0]);
        $IdInfoData['PlayImg'] = $PlayImg[1][0];
        $IdInfoData['PlayYear'] = $PlayYear[1][0];
        $IdInfoData['PlayActor'] = $PlayActor[1];
        $IdInfoData['PlayVotes'] = $PlayVotes[1][0];
        return $IdInfoData;
    }

    public function GetEpisodeUrl($UrlData)
    {
        if (strpos($UrlData, '<ul class="bs">') !== false) {
            #再检测 "@type": "Movie" 看是否是电影类型
            if (strpos($UrlData, '"@type": "Movie"') !== false) {
                return array('status' => 0, 'type' => 'movie', 'data' => self::GetMovieEpisodeUrl($UrlData));
            } elseif (strpos($UrlData, '"@type": "TVSeries"') !== false) {
                return array('status' => 0, 'type' => 'tv', 'data' => self::GetTVSeriesEpisodeUrl($UrlData));
            } else {
                return array('status' => 2);
            }
        } else {
            #无法播放类型，不管是电影还是电视剧
            return array('status' => 1);
        }
    }

    public function GetMovieEpisodeUrl($UrlData)
    {
        preg_match_all('#<a class="playBtn" data-cn="(.*?)"([\s\S]*?)href="(.*?)"([\s\S]*?)>#', $UrlData, $PlayList);
        for ($x = 0; $x < count($PlayList[1]); $x++) {
            $MovieEpisodeUrlData[$x]['from'] = $PlayList[1][$x];
            $MovieEpisodeUrlData[$x]['url'] = self::DoubanUrlToUrl($PlayList[3][$x]);
        }
        #print_r($MovieEpisodeUrlData);
        return $MovieEpisodeUrlData;
    }

    public function GetTVSeriesEpisodeUrl($UrlData)
    {
        #无需取JS源码类型
        if (strpos($UrlData, 'var sources = {};') !== false) {
            preg_match_all('#sources\[(.*?)\] = \[([\s\S]*?)\];#', $UrlData, $PlayUrlList);
            $i = 0;
            foreach ($PlayUrlList[2] as $a) {
                preg_match_all('#{play_link: "(.*)", ep: "#', $a, $ls);
                $PlayUrl[$i] = $ls[1];
                $i++;
            }
            return self::DoubanUrlToUrl($PlayUrl);
            #取js源码类型
        } else {
            preg_match_all('#<script type="text\/javascript" src="https:\/\/img3.doubanio.com\/misc\/mixed_static\/(.*?).js"><\/script>#', $UrlData, $JsId);
            $JsUrl = 'https://img3.doubanio.com/misc/mixed_static/' . end($JsId[1]) . '.js';
            $JsData = self::http_get($JsUrl);
            preg_match_all('#sources\[(.*?)\] = \[([\s\S]*?)\];#', $JsData, $PlayUrlList);
            $i = 0;
            foreach ($PlayUrlList[2] as $a) {
                preg_match_all('#{play_link: "(.*)", ep: "#', $a, $ls);
                $PlayUrl[$i] = $ls[1];
                $i++;
            }
            //print_r(self::DoubanUrlToUrl($PlayUrl));
            return self::DoubanUrlToUrl($PlayUrl);
        }
    }

    public function Get_recommen_dations($UrlData)
    {
        $parmsId = '#<a href="https:\/\/movie.douban.com\/subject\/(.*?)\/\?from=subject-page" class="" >#';
        $parmsImg = '#<img src="(.*?)" alt="(.*?)" class=""#';

        preg_match_all($parmsId, self::GetSubstr($UrlData, '<div id="recommendations" class="">', '<div id="comments-section">'), $recommen_dation_id);
        preg_match_all($parmsImg, self::GetSubstr($UrlData, '<div id="recommendations" class="">', '<div id="comments-section">'), $recommen_dation_img);
        for ($x = 0; $x < count($recommen_dation_id[1]); $x++) {
            $recommen_dations[$x]['Id'] = $recommen_dation_id[1][$x];
            $recommen_dations[$x]['Name'] = $recommen_dation_img[1][$x];
            $recommen_dations[$x]['Img'] = $recommen_dation_img[2][$x];
        }
        return $recommen_dations;
    }


    public function IdReviews($Id, $ReviewsPageCount = '0')
    {
        $IdReviewsUrl = 'https://movie.douban.com/subject/' . $Id . '/comments?sort=new_score&status=P&limit=20&start=' . $ReviewsPageCount * 20;
        $IdReviewsData = self::http_get($IdReviewsUrl);
        preg_match_all('#<span class="short">([\s\S]*?)<\/span>#', $IdReviewsData, $ReviewsContentList);
        preg_match_all('#<a title="(.*)" href="#', $IdReviewsData, $ReviewsAvatarList);
        preg_match_all('#<img src="(.*)" class="" />#', $IdReviewsData, $ReviewsImgList);
        preg_match_all('#<span class="allstar(.*)0 rating#', $IdReviewsData, $ReviewsRatingList);
        $count = count($ReviewsAvatarList[1]);
        for ($x = 0; $x < $count; $x++) {
            $IdReviewData[$x]['Avatar'] = $ReviewsAvatarList[1][$x];
            $IdReviewData[$x]['Img'] = $ReviewsImgList[1][$x];
            $IdReviewData[$x]['Rating'] = floatval($ReviewsRatingList[1][$x]);
            $IdReviewData[$x]['Content'] = $ReviewsContentList[1][$x];
        }


        return $this->IdReviewData = $IdReviewData;
    }



    public function IdPhotos($Id, $PhotosType = 'S', $PhotosPageCount = '0')
    {
        $IdPhotosUrl = 'https://movie.douban.com/subject/' . $Id . '/photos?type=' . $PhotosType . '&start=' . $PhotosPageCount * 30;
        $IdPhotosData = self::http_get($IdPhotosUrl);
        preg_match_all('#<div class="cover">([\s\S]*?)<img src="(.*)" />#', $IdPhotosData, $IdPhotosList);
        return $this->IdPhotosData = $IdPhotosList[2];
    }


    /*
        https://www.douban.com/link2/?url=
        http://www.douban.com/link2/?url=
    */
    public function DoubanUrlToUrl(&$DoubanUrl)
    {

        $DoubanUrl = str_replace('https://www.douban.com/link2/?url=', '', $DoubanUrl);
        $DoubanUrl = str_replace('http://www.douban.com/link2/?url=', '', $DoubanUrl);
        if (is_array($DoubanUrl)) {
            foreach ($DoubanUrl as $key => $val) {
                if (is_array($val)) {
                    self::DoubanUrlToUrl($DoubanUrl[$key]);
                }
            }
        }
        return $DoubanUrl;

        //$return =  str_replace('https://www.douban.com/link2/?url=', "", $DoubanUrl);
        //$return =  str_replace('http://www.douban.com/link2/?url=', "", $return);
        //return $return;
    }



    public function Movie($MovieType = '豆瓣高分', $MovieSort = 'recommend', $page_limit = '24', $page = 1)
    {
        $page_start = ($page - 1) * 24;
        $MovieUrl = 'https://movie.douban.com/j/search_subjects?type=movie&tag=' . $MovieType . '&sort=' . $MovieSort . '&page_limit=' . $page_limit . '&page_start=' . $page_start;
        $MovieData = self::http_get($MovieUrl);
        $MovieArr = json_decode($MovieData, true);
        return $this->GetMovieArr = $MovieArr;
    }

    public function Tv($TvType = '热门', $TvSort = 'recommend', $page_limit = '24', $page = 1)
    {
        $page_start = ($page - 1) * 24;
        $TvUrl = 'https://movie.douban.com/j/search_subjects?type=tv&tag=' . $TvType . '&sort=' . $TvSort . '&page_limit=' . $page_limit . '&page_start=' . $page_start;

        $TvData = self::http_get($TvUrl);
        $TvArr = json_decode($TvData, true);
        return $this->GetTvArr = $TvArr;
    }



    public function http_get($url)
    {
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



    public function GetSubstr($str, $leftStr, $rightStr)
    {
        $left = strpos($str, $leftStr);
        $right = strpos($str, $rightStr, $left);
        if ($left < 0 or $right < $left) return '';
        return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
    }
}
