<?php
/**
 * @author <xhboke>
 * @since <1.0.0>
 * @GitHub https://github.com/xhboke/douban
 */

$douban = New Douban;
//$douban->Search('搜索名称');
//print_r($douban->SearchData);
//$douban->IdInfo('1292213');
//print_r($douban->IdInfoData);
//$douban->IdReviews('1292064','0');
//print_r($douban->IdReviewData);
//$douban->IdPhotos('10793510','S','0');
//print_r($douban->IdPhotosData);
//$douban->GetIndexMovie('豆瓣高分','recommend','20');
//print_r($douban->GetIndexMovieData);
//$douban->GetIndexTv('综艺','recommend','20');
//print_r($douban->GetIndexTvData);
class Douban {

    public function __construct(){
          
    }


    /**
     * 取豆瓣搜索结果
     * @access public
     * @author xhapa
     * @version 1.0
     * @param mixed $SearchName 名称
     * @param mixed $SearchCount 搜索个数
     * @return array SearchData
     */
    public function Search( $SearchName , $PageCount = "0" )
    {   
        $SearchUrl = 'https://m.douban.com/j/search/?q='.$SearchName.'&t=movie&p='.$PageCount;
        $ApiData = json_decode(self::http_get($SearchUrl));
        preg_match_all('#<span class="subject-title">(.*)<\/span>#', $ApiData->html, $ReturnName);
        preg_match_all('#<img src="(.*)"\/>#', $ApiData->html, $ReturnImg);
        preg_match_all('#\/movie\/subject\/(.*)\/">#', $ApiData->html, $ReturnId);
        preg_match_all('#<span>(.*)<\/span>#', $ApiData->html, $ReturnRating);

        $SearchData['Id'] = $ReturnId[1];
        $SearchData['Name'] = $ReturnName[1];
        $SearchData['Img'] = $ReturnImg[1];
        $SearchData['Rating'] = $ReturnRating[1];

        return $this->SearchData = $SearchData;
    }


    /**
     * subject ID取播放链接及相关信息
     * @access public
     * @author xhapa
     * @version 1.0
     * @param mixed $Id 豆瓣ID
     * @return array IdInfoData
     */
    public function IdInfo( $Id )
    {
        $IdUrl = 'https://movie.douban.com/subject/'.$Id.'/';
        $UrlData = self::http_get($IdUrl);
        //$IdInfoArr = json_decode(self::GetSubstr($UrlData,'<script type="application/ld+json">','</script>'));
        if(strpos($UrlData,'<ul class="bs">') !== false){ 

            if(strpos($UrlData,'"  href="javascript: void 0;"') !== false){
                preg_match_all('#<a class="playBtn" data-cn="(.*?)" data-source="(.*?)"  href="javascript: void 0;">#', $UrlData, $PlayList);
                $PlayOri = $PlayList[1];
                if(strpos($UrlData,'play_link: "') !== false){
                    preg_match_all('#sources\[(.*?)\] = \[([\s\S]*?)\];#', $UrlData, $PlayUrlList);
                    $i = 0;
                    foreach($PlayUrlList[2] as $a){
                        preg_match_all('#{play_link: "(.*)", ep: "#', $a, $ls);
                        $PlayUrl[$i] = $ls[1];
                        $i++;
                    }
                }else{
                    preg_match_all('#<script type="text\/javascript" src="https:\/\/img3.doubanio.com\/misc\/mixed_static\/(.*?).js"><\/script>#', $UrlData, $JsId);
                    $JsUrl = 'https://img3.doubanio.com/misc/mixed_static/'.end($JsId[1]).'.js';
                    $JsData = self::http_get($JsUrl);
                    preg_match_all('#sources\[(.*?)\] = \[([\s\S]*?)\];#', $JsData, $PlayUrlList);
                    $i = 0;
                    foreach($PlayUrlList[2] as $a){
                        preg_match_all('#{play_link: "(.*)", ep: "#', $a, $ls);
                        $PlayUrl[$i] = $ls[1];
                        $i++;
                    }
                }
                $PlayType = 'Z';
            }else{
                preg_match_all('#<a class="playBtn" data-cn="(.*)" href="(.*)" target="_blank">#', $UrlData, $PlayList);
                $PlayOri = $PlayList[1];
                $PlayUrl = $PlayList[2];
                $PlayType = 'M';
            }
        }else{
            $PlayOri = null;
            $PlayUrl = null;
            $PlayType = 'N';
        }
        if(strpos($UrlData,'<span class="all hidden">') !== false){ 
            preg_match_all('#<span class="all hidden">([\s\S]*?)<\/span>#', $UrlData, $PlayDesc);
        }else{
            preg_match_all('#<span property="v:summary" class="">([\s\S]*?)<\/span>#', $UrlData, $PlayDesc);
        }
        preg_match_all('#<a class="playBtn" data-cn="(.*?)" data-source="(.*?)"  href="javascript: void 0;">#', $UrlData, $PlayList);
        preg_match_all('#"name": "([\s\S]*?)",#', $UrlData, $PlayName);
        preg_match_all('#<span property="v:genre">([\s\S]*?)<\/span>#', $UrlData, $PlayGenre);
        preg_match_all('#"datePublished": "([\s\S]*?)",#', $UrlData, $PlayDatapublish);
        preg_match_all('#property="v:average">([\s\S]*?)<\/strong>#', $UrlData, $PlayRating);
        preg_match_all('#"image": "([\s\S]*?)",#', $UrlData, $PlayImg);
        preg_match_all('#"name": "([\s\S]*?)"#', self::GetSubstr($UrlData,'"actor":','datePublished'), $PlayActor);

        $IdInfoData['Name'] = $PlayName[1][0];$IdInfoData['PlayDesc'] = $PlayDesc[1][0];$IdInfoData['DatePublished'] = $PlayDatapublish[1][0];
        $IdInfoData['Genre'] = $PlayGenre[1];$IdInfoData['Rating'] = $PlayRating[1][0];$IdInfoData['PlayType'] = $PlayType;$IdInfoData['PlayImg'] = $PlayImg[1][0];
        $IdInfoData['PlayActor'] = $PlayActor[1];
        $IdInfoData['PlayOri'] = $PlayOri;$IdInfoData['PlayUrl'] = $PlayUrl;
        
        return $this->IdInfoData=$IdInfoData;

    }


    /**
     * 播放来源的ID对应播放来源
     * @access public
     * @author xhapa
     * @version 1.0
     * @param mixed $Id 豆瓣播放来源ID
     */
    public function IdToOri( $Id )
    {
        $arrer = array(
            '0'=>'',
            '1'=>'腾讯视频',
            '3'=>'优酷视频',
            '8'=>'哔哩哔哩',
            '9'=>'爱奇艺',
            '15'=>'咪咕视频',
            '17'=>'西瓜视频'
        );
    }


    /**
     * 取subject id对应的短评(reviews)
     * @access public
     * @author xhapa
     * @version 1.0
     * @param mixed $Id subject id
     * @return array IdReviewData
     */
    public function IdReviews( $Id , $ReviewsPageCount = '0' )
    {
        $IdReviewsUrl = 'https://movie.douban.com/subject/'.$Id.'/comments?sort=new_score&status=P&limit=20&start='.$ReviewsPageCount * 20;
        $IdReviewsData = self::http_get($IdReviewsUrl);
        preg_match_all('#<span class="short">([\s\S]*?)<\/span>#', $IdReviewsData, $ReviewsContentList);
        preg_match_all('#<a title="(.*)" href="#', $IdReviewsData, $ReviewsAvatarList);
        preg_match_all('#<img src="(.*)" class="" />#', $IdReviewsData, $ReviewsImgList);
        preg_match_all('#<span class="allstar(.*)0 rating#', $IdReviewsData, $ReviewsRatingList);
        $IdReviewData['Avatar'] = $ReviewsAvatarList[1];$IdReviewData['Img'] = $ReviewsImgList[1];
        $IdReviewData['Rating'] = $ReviewsRatingList[1];$IdReviewData['Content'] = $ReviewsContentList[1];
        return $this->IdReviewData=$IdReviewData;

    }


    /**
     * 取subject id对应的照片(Photos)
     * @access public
     * @author xhapa
     * @version 1.0
     * @param mixed $Id subject id
     * @param mixed $PhotosType S:剧照 R:海报 W:壁纸
     * @param mixed $PhotosPageCount 具体页数
     * @return array IdReviewData
     */
    public function IdPhotos( $Id , $PhotosType = 'S' ,$PhotosPageCount = '0')
    {
        $IdPhotosUrl = 'https://movie.douban.com/subject/'.$Id.'/photos?type='.$PhotosType.'&start='.$PhotosPageCount * 30;
        $IdPhotosData = self::http_get($IdPhotosUrl); 
        preg_match_all('#<div class="cover">([\s\S]*?)<img src="(.*)" />#', $IdPhotosData, $IdPhotosList);
        return $this->IdPhotosData=$IdPhotosList[2];
    }

    /**
     * 选电影类
     * @access public
     * @author xhapa
     * @version 1.0
     * @param mixed $IndexMovieType 热门 最新 经典 可播放 豆瓣高分 ...
     * @param mixed $IndexMovieSort recommend:热度  time:时间  rank：评价
     * @param mixed $page_limit 影片的数量 max:500
     * @return array GetIndexMovieData
     */
    public function GetIndexMovie( $IndexMovieType = '豆瓣高分' , $IndexMovieSort = 'recommend' , $page_limit = '20')
    {
        $IndexMovieUrl = 'https://movie.douban.com/j/search_subjects?type=movie&tag='.$IndexMovieType.'&sort='.$IndexMovieSort.'&page_limit='.$page_limit.'&page_start=0';
        $IndexMovieData = self::http_get($IndexMovieUrl); 
        $IndexMovieArr = json_decode($IndexMovieData , true);
        return $this->GetIndexMovieData = $IndexMovieArr;
    }

    /**
     * 选电视剧类
     * @access public
     * @author xhapa
     * @version 1.0
     * @param mixed $IndexTvType 热门 美剧 英剧 韩剧 综艺...
     * @param mixed $IndexTvSort recommend:热度  time:时间  rank：评价
     * @param mixed $page_limit 影片的数量 
     * @return array GetIndexTvData
     */
    public function GetIndexTv( $IndexTvType = '热门' , $IndexTvSort = 'recommend' , $page_limit = '20')
    {
        $IndexTvUrl = 'https://movie.douban.com/j/search_subjects?type=tv&tag='.$IndexTvType.'&sort='.$IndexTvSort.'&page_limit='.$page_limit.'&page_start=0';
        $IndexTvData = self::http_get($IndexTvUrl); 
        $IndexTvArr = json_decode($IndexTvData , true);
        return $this->GetIndexTvData = $IndexTvArr;
    }

    public function GetSubstr( $str, $leftStr, $rightStr )
    {
        $left = strpos($str, $leftStr);
        $right = strpos($str, $rightStr,$left);
        if($left < 0 or $right < $left) return '';
        return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
    }


    public function http_get($url){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

}