<?php
/**
 * @author <xhboke>
 * @since <5.0.1>
 * @GitHub https://github.com/xhboke/douban
 */

 
class douban
{
    var $SearchData;
    var $IdInfoData;
    var $IdReviewData;
    var $tagData;
    var $celebrityData;
    var $top250Data;
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
                $SearchData['Data'][$PageCount * 10 + $x]['Id'] = $ReturnId[1][$x];
                $SearchData['Data'][$PageCount * 10 + $x]['Name'] = $ReturnName[1][$x];
                $SearchData['Data'][$PageCount * 10 + $x]['Img'] = $ReturnImg[1][$x];
                $SearchData['Data'][$PageCount * 10 + $x]['Rating'] = $ReturnRating[1][$x];
            }
        }
        $this->SearchData = json_encode($SearchData, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
    }


    public function IdInfo($Id)
    {
        $IdUrl = 'https://movie.douban.com/subject/' . $Id . '/';
        $UrlData = self::http_get($IdUrl);
        $ReturnData['info'] = self::IdInfoArr($UrlData);
        $ReturnData['info'] = array_merge(['Id' => $Id], $ReturnData['info']);
        $ReturnData['url'] = self::GetEpisodeUrl($UrlData);
        $ReturnData['recommend'] = self::Get_recommen_dations($UrlData);
        $this->IdInfoData = $ReturnData;
    }

    public function IdInfoArr($UrlData)
    {
        preg_match_all('#<meta name="keywords" content="([\s\S]*?),#', $UrlData, $PlayName);
        preg_match_all('#<span property="v:genre">([\s\S]*?)<\/span>#', $UrlData, $PlayGenre);
        preg_match_all('#"datePublished": "([\s\S]*?)",#', $UrlData, $PlayDatapublish);
        preg_match_all('#property="v:average">([\s\S]*?)<\/strong>#', $UrlData, $PlayRating);
        preg_match_all('#"image": "([\s\S]*?)",#', $UrlData, $PlayImg);
        preg_match_all('#<a href="/celebrity/(.*?)\/" rel="v:starring">([\s\S]*?)<\/a>#', $UrlData, $PlayActor);
        preg_match_all('#<a href="/celebrity/(.*?)\/" rel="v:directedBy">([\s\S]*?)<\/a>#', $UrlData, $PlayDirector);
        preg_match_all('#<span property="v:votes">([\s\S]*?)<\/span>#', $UrlData, $PlayVotes);
        preg_match_all('#<span class="year">([\s\S]*?)<\/span>#', $UrlData, $PlayYear);
        //preg_match_all('#<span property="v:runtime" content="([\s\S]*?)">#', $UrlData, $PlayRuntime);
        if (strpos($UrlData, '<span class="all hidden">') !== false) {
            preg_match_all('#<span class="all hidden">([\s\S]*?)<\/span>#', $UrlData, $PlayDesc);
        } else {
            preg_match_all('#<span property="v:summary" class="">([\s\S]*?)<\/span>#', $UrlData, $PlayDesc);
        }
        for ($i = 0; $i < count($PlayActor[1]); $i++) {
            $Play_Actor[$i]['Id'] = $PlayActor[1][$i];
            $Play_Actor[$i]['Name'] = $PlayActor[2][$i];
        }
        for ($i = 0; $i < count($PlayDirector[1]); $i++) {
            $Play_Director[$i]['Id'] = $PlayDirector[1][$i];
            $Play_Director[$i]['Name'] = $PlayDirector[2][$i];
        }
        $IdInfoData['Name'] = $PlayName[1][0];
        $IdInfoData['PlayDesc'] = trim($PlayDesc[1][0]);
        $IdInfoData['DatePublished'] = $PlayDatapublish[1][0];
        $IdInfoData['Genre'] = $PlayGenre[1];
        $IdInfoData['Rating'] = floatval($PlayRating[1][0]);
        $IdInfoData['PlayVotes'] = $PlayVotes[1][0];
        $IdInfoData['PlayImg'] = $PlayImg[1][0];
        $IdInfoData['PlayYear'] = $PlayYear[1][0];
        $IdInfoData['PlayActor'] = $Play_Actor;
        $IdInfoData['Director'] = $Play_Director;

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
        $parmsId = '#<a href="https:\/\/movie.douban.com\/subject\/(.*?)\/\?from=subject-page" class=""#';
        $parmsImg = '#<img src="(.*?)" alt="(.*?)" class=""#';

        preg_match_all($parmsId, self::GetSubstr($UrlData, '<div id="recommendations" class="">', '<div id="comments-section">'), $recommen_dation_id);
        preg_match_all($parmsImg, self::GetSubstr($UrlData, '<div id="recommendations" class="">', '<div id="comments-section">'), $recommen_dation_img);
        for ($x = 0; $x < count($recommen_dation_id[1]); $x++) {
            $recommen_dations[$x]['Id'] = $recommen_dation_id[1][$x];
            $recommen_dations[$x]['Name'] = $recommen_dation_img[2][$x];
            $recommen_dations[$x]['Img'] = $recommen_dation_img[1][$x];
        }
        return $recommen_dations;
    }


    public function IdReviews($Id, $ReviewsPageCount = 0)
    {
        $IdReviewsUrl = 'https://movie.douban.com/subject/' . $Id . '/comments?sort=new_score&status=P&limit=20&start=' . $ReviewsPageCount * 20;
        $IdReviewsData = self::http_get($IdReviewsUrl);
        preg_match_all('#<span class="short">([\s\S]*?)<\/span>#', $IdReviewsData, $ReviewsContentList);
        preg_match_all('#<a title="(.*)" href="#', $IdReviewsData, $ReviewsAvatarList);
        preg_match_all('#<img src="(.*)" class="" />#', $IdReviewsData, $ReviewsImgList);
        preg_match_all('#<span class="allstar(.*)0 rating#', $IdReviewsData, $ReviewsRatingList);
        $count =  count($ReviewsAvatarList[1]);
        for ($x = 0; $x < $count; $x++) {
            $IdReviewData[20 * $ReviewsPageCount + $x]['Avatar'] = $ReviewsAvatarList[1][$x];
            $IdReviewData[20 * $ReviewsPageCount + $x]['Img'] = $ReviewsImgList[1][$x];
            $IdReviewData[20 * $ReviewsPageCount + $x]['Rating'] = floatval($ReviewsRatingList[1][$x]);
            $IdReviewData[20 * $ReviewsPageCount + $x]['Content'] = $ReviewsContentList[1][$x];
        }


        return $this->IdReviewData = json_encode($IdReviewData, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
    }


    public function Get_tag($sort = 'U', $tags = '', $page = 0, $genres = '', $countries = '', $year_range = '')
    {
        //https://movie.douban.com/j/new_search_subjects?sort=U&range=0,10&tags=电影,经典&start=0&genres=剧情&countries=中国大陆&year_range=2020,2020
        //https://movie.douban.com/j/new_search_subjects?sort=U&tags=&start=0&genres=&countries=&year_range=
        //$sort = 'U', $tags = '', $page = 0, $genres = '', $countries = '', $year_range = ''
        $page_start = $page * 20;
        $tagUrl = 'https://movie.douban.com/j/new_search_subjects?sort=' . $sort . '&range=0,10&tags=' . $tags . '&start=' . $page_start . '&genres=' . $genres . '&countries=' . $countries . '&year_range=' . $year_range;
        $apiData = json_decode(self::http_get($tagUrl), true);
        $count = count($apiData['data']);
        if ($count) {
            $returnData['status'] = 0;
            $returnData['count'] = $count;
            for ($i = $page_start; $i < $page_start + $count; $i++) {
                $returnData['data'][$i]['id'] = $apiData['data'][$i - $page_start]['id'];
                $returnData['data'][$i]['title'] = $apiData['data'][$i - $page_start]['title'];
                $returnData['data'][$i]['cover'] = $apiData['data'][$i - $page_start]['cover'];
                $returnData['data'][$i]['rate'] = $apiData['data'][$i - $page_start]['rate'];
            }
        } else {
            $returnData['status'] = 1;
            $returnData['count'] = 0;
        }
        return $this->tagData = json_encode($returnData, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
    }

    public function Get_celebrity($id)
    {
        $celebrityUrl = 'https://movie.douban.com/celebrity/' . $id . '/';
        $celebrity_Data = self::http_get($celebrityUrl);
        preg_match_all('#<h1>([\s\S]*?)<\/h1>#', $celebrity_Data, $Name);
        preg_match_all('#<a class="nbg" title="([\s\S]*?)" href="([\s\S]*?)">#', $celebrity_Data, $celebrity_Img);
        preg_match_all('#<span>性别<\/span>:([\s\S]*?)<\/li>#', $celebrity_Data, $Sex);
        preg_match_all('#<span>星座<\/span>:([\s\S]*?)<\/li>#', $celebrity_Data, $Constellation);
        preg_match_all('#<span>出生日期<\/span>:([\s\S]*?)<\/li>#', $celebrity_Data, $BirthDay);
        preg_match_all('#<span>出生地<\/span>:([\s\S]*?)<\/li>#', $celebrity_Data, $BirthPlace);
        preg_match_all('#<span>职业<\/span>:([\s\S]*?)<\/li>#', $celebrity_Data, $Profession);
        preg_match_all('#<span>更多外文名<\/span>:([\s\S]*?)<\/li>#', $celebrity_Data, $OtherName);
        preg_match_all('#<span>家庭成员<\/span>:([\s\S]*?)<\/li>#', $celebrity_Data, $FamilyMember);

        $Family_Member = preg_replace("/<a[^>]*>(.*?)<\/a>/is", "$1", $FamilyMember[1][0]);

        preg_match_all('#<span>官方网站<\/span>:([\s\S]*?)target="_blank">([\s\S]*?)<\/a>([\s\S]*?)<\/li>#', $celebrity_Data, $Website);

        $ReturnData['Id'] = $id;
        $ReturnData['Name'] = $Name[1][0];
        $ReturnData['Img'] = $celebrity_Img[2][0];
        $ReturnData['Sex'] = trim($Sex[1][0]);
        $ReturnData['Constellation'] = trim($Constellation[1][0]);
        $ReturnData['BirthDay'] = trim($BirthDay[1][0]);
        $ReturnData['BirthPlace'] = trim($BirthPlace[1][0]);
        $ReturnData['Profession'] = trim($Profession[1][0]);
        $ReturnData['OtherName'] = trim($OtherName[1][0]);
        $ReturnData['FamilyMember'] = trim($Family_Member);
        $ReturnData['Website'] = trim($Website[2][0]);
        if (strpos($celebrity_Data, '<span class="all hidden">') !== false) {
            preg_match_all('#<span class="all hidden">([\s\S]*?)<\/span>#', $celebrity_Data, $Brief_introduction);
            $ReturnData['Brief_introduction'] = trim($Brief_introduction[1][0]);
        } else {
            preg_match_all('#影人简介([\s\S]*?)<div class="bd">([\s\S]*?)<\/div>#', $celebrity_Data, $Brief_introduction);
            $ReturnData['Brief_introduction'] = trim($Brief_introduction[2][0]);
        }
        return $this->celebrityData = json_encode($ReturnData, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
    }

    public function top250($page)
    {
        $page_start = $page * 25;
        $top250Url = 'https://movie.douban.com/top250?start=0' . $page_start;
        $top250Data = self::http_get($top250Url);
        preg_match_all('#<em class="">([\s\S]*?)<\/em>([\s\S]*?)<a href="https:\/\/movie.douban.com\/subject\/([\s\S]*?)\/">([\s\S]*?)<img width="100" alt="([\s\S]*?)" src="([\s\S]*?)" class="">#', $top250Data, $preg);
        preg_match_all('#<span class="rating_num" property="v:average">([\s\S]*?)<\/span>#', $top250Data, $Rank);
        $count = count($preg[0]);
        for ($i = 0; $i < $count; $i++) {
            $returnData[$i]['Em'] = $preg[1][$i];
            $returnData[$i]['Id'] = $preg[3][$i];
            $returnData[$i]['Name'] = $preg[5][$i];
            $returnData[$i]['Rank'] = $Rank[1][$i];
            $returnData[$i]['Img'] = $preg[6][$i];
        }
        return $this->top250Data = json_encode($returnData, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
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
        curl_setopt($oCurl, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.193 Safari/537.36');
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


