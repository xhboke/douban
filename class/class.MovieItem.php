<?php

/** 
 * 获取影片信息 
 * 
 * 根据编号获取电影相关信息
 * @author      xhboke 
 * @version     1.0
 */
include_once('class.Movie.php');

class MovieInfo extends Movie
{
    public $id;
    public $url;
    public $data;
    public $Name;
    public $ChineseName;
    public $Description;
    public $DatePublished;
    public $Genre;
    public $Language;
    public $Other_name;
    public $Rating;
    public $Votes;
    public $Image;
    public $Year;
    public $IMDB;
    public $Episode;
    public $Actor;
    public $region;
    public $All_directors;
    public $All_writers;
    public $All_actors;
    public $Single_episode_length;
    public $EpisodeUrl;
    public $All;
    public $OtherLike;

    public function __construct($id)
    {
        $this->id = $id;
        $this->url = parent::MovieRootUrl . '/subject/' . $id . '/';
        $this->data = $this->curl_get($this->url);
    }
    public function __destruct()
    {
    }

    /**  
     * 获取指定电影的信息
     * @access public 
     * @param boolean $isAccurate 电影播放链接不存在下使用外部播放链接
     * @param boolean $is_play_url 从豆瓣电影获取电影的播放链接
     * @return string
     */
    public function getAll($isAccurate = false, $is_play_url = True)
    {
        $this->All['Id'] = $this->getId();
        $this->All['Name'] = $this->getName();
        $this->All['ChineseName'] = $this->getChineseName();
        $this->All['Image'] = $this->getImage();
        $this->All['Year'] = $this->getYear();
        $this->All['DatePublished'] = $this->getDatePublished();
        $this->All['Genre'] = $this->getGenre();
        $this->All['Language'] = $this->get_Language();
        $this->All['Region'] = $this->get_region();
        $this->All['Other_name'] = $this->get_Other_name();
        $this->All['Rating'] = $this->getRating();
        $this->All['Votes'] = $this->getVotes();
        $this->All['Description'] = $this->getDescription();
        $this->All['Episode'] = $this->getEpisode();
        $this->All['Single_episode_length'] = $this->get_Single_episode_length();
        $this->All['Movie_length'] = $this->get_Movie_length();
        $this->All['IMDB'] = $this->getIMDB();
        $this->All['All_directors'] = $this->get_All_directors();
        $this->All['All_writers'] = $this->get_All_writers();
        $this->All['All_actors'] = $this->get_All_actors();
        $this->All['Actors'] = $this->getActor();
        if ($is_play_url == True) {
            $this->All['EpisodeUrl'] = $this->getEpisodeUrl();
        }
        if ($this->All['EpisodeUrl']['status'] == 404 && $isAccurate == true) {
            $_ = $this->get_Out_Play_Url($this->All['ChineseName']);
            $this->All['EpisodeUrl']['data'] = $_;
            $this->All['EpisodeUrl']['status'] = 404;
            $this->All['EpisodeUrl']['type'] = 'movie';
        }
        $this->All['OtherLike'] = $this->getOtherLike();
        return $this->Json($this->All);
    }

    /**  
     * 只获取电影的基本信息
     * @access public 
     * @return string
     */
    public function get_INFO()
    {
        $this->All['Id'] = $this->getId();
        $this->All['Name'] = $this->getName();
        $this->All['Image'] = $this->getImage();
        $this->All['Year'] = $this->getYear();
        $this->All['DatePublished'] = $this->getDatePublished();
        $this->All['Genre'] = $this->getGenre();
        $this->All['Language'] = trim($this->get_Language());
        $this->All['Rating'] = $this->getRating();
        $this->All['Votes'] = $this->getVotes();
        $this->All['Description'] = $this->getDescription();
        $this->All['Single_episode_length'] = trim($this->get_Single_episode_length());
        $this->All['Movie_length'] = $this->get_Movie_length();
        return $this->Json($this->All);
    }

    /**  
     * 获取电影豆瓣ID
     * @access public 
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**  
     * 获取电影豆瓣页面数据
     * @access public 
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**  
     * 获取电影的名称（可能含有其他语言）
     * @access public 
     * @return string
     */
    public function getName()
    {
        $this->Name = $this->preg('#"name": "([\s\S]*?)"#', $this->data, 1)[0];
        return $this->Name;
    }

    /**  
     * 获取电影的中文名称（去除其他语言）
     * @access public 
     * @return string
     */
    public function getChineseName()
    {
        $this->ChineseName = trim(str_replace(array("\n", "\r", "(豆瓣)"), "", $this->preg('#<title>([\s\S]*?)<\/title>#', $this->data, 1)[0]));
        return $this->ChineseName;
    }

    /**  
     * 获取电影的描述（富文本含<br />）
     * @access public 
     * @return string
     */
    public function getDescription()
    {
        if (strpos($this->data, '<span class="all hidden">') !== false) {
            $this->Description = trim($this->preg('#<span class="all hidden">([\s\S]*?)<\/span>#', $this->data, 1)[0]);
        } else {
            $this->Description = trim($this->preg('#<span property="v:summary" class="">([\s\S]*?)<\/span>#', $this->data, 1)[0]);
        }
        return $this->Description;
    }

    /**  
     * 获取电影的出版日期（多个只取第一个）
     * @access public 
     * @return string
     */
    public function getDatePublished()
    {
        $_ = $this->preg('#<span property="v:initialReleaseDate" content="([\s\S]*?)">#', $this->data, 1)[0];
        $_ = $_ == null ? '' : $_;
        $this->DatePublished = str_replace(" ", '', strip_tags($_));
        return $this->DatePublished;
    }

    /**  
     * 获取电影的语言
     * @access public 
     * @return string
     */
    public function get_Language()
    {
        $_ =  trim($this->preg('#语言:<\/span>([\s\S]*?)<br\/>#', $this->data, 1)[0]);
        $this->Language = $_ == null ? '' : $_;
        return $this->Language;
    }

    /**  
     * 获取电影的其他名称（多个以"/"分割）
     * @access public 
     * @return string
     */
    public function get_Other_name()
    {
        $this->Other_name = str_replace(" ", '', strip_tags($this->preg('#又名:<\/span>([\s\S]*?)<br\/>#', $this->data, 1)[0]));
        return $this->Other_name;
    }

    /**  
     * 获取电影的类型
     * @access public 
     * @return array
     */
    public function getGenre()
    {
        $this->Genre = $this->preg('#<span property="v:genre">([\s\S]*?)<\/span>#', $this->data, 1);
        return $this->Genre;
    }

    /**  
     * 获取电影的评分
     * @access public 
     * @return string
     */
    public function getRating()
    {
        $this->Rating = $this->preg('#property="v:average">([\s\S]*?)<\/strong>#', $this->data, 1)[0];
        return $this->Rating;
    }

    /**  
     * 获取电影的评分人数
     * @access public 
     * @return string
     */
    public function getVotes()
    {
        $_ = $this->preg('#<span property="v:votes">([\s\S]*?)<\/span>#', $this->data, 1)[0];
        $this->Votes = $_ == null ? '' : $_;
        return $this->Votes;
    }

    /**  
     * 获取电影的海报
     * @access public 
     * @return string
     */
    public function getImage()
    {
        $this->Image = $this->preg('#"image": "([\s\S]*?)",#', $this->data, 1)[0];
        return $this->Image;
    }

    /**  
     * 获取电影的年代
     * @access public 
     * @return string
     */
    public function getYear()
    {
        if (strpos($this->data, '<span class="year">') !== false) {
            $this->Year = $this->preg('#<span class="year">\(([\s\S]*?)\)<\/span>#', $this->data, 1)[0];
        } elseif (strpos($this->data, 'v:initialReleaseDate') !== false) {
            $this->Year =  $this->preg('#<span property="v:initialReleaseDate" content="([\s\S]*?)-#', $this->data, 1)[0];
        } else {
            $this->Year = '';
        }
        return $this->Year;
    }

    /**  
     * 获取电影的IMDB编号
     * @access public 
     * @return string
     */
    public function getIMDB()
    {
        $this->IMDB = trim($this->preg('#IMDb:<\/span>([\s\S]*?)<br>#', $this->data, 1)[0]);
        return $this->IMDB;
    }

    /**  
     * 获取电影参与演员（包含头像）
     * @access public 
     * @return array
     */
    public function getActor()
    {
        $_ActorInfo = $this->preg('#<div class="avatar" style="background-image: url\(([\s\S]*?)\)">([\s\S]*?)<span class="name"><a href="https:\/\/movie.douban.com\/celebrity\/([\s\S]*?)/" title="([\s\S]*?)" class="name">([\s\S]*?)<\/a>([\s\S]*?)<span class="role" title="([\s\S]*?)">#', $this->data, 0);
        for ($i = 0; $i < count($_ActorInfo[0]); $i++) {
            $this->Actor[$i]['ActorId'] = $_ActorInfo[3][$i];
            $this->Actor[$i]['ActorName'] = $_ActorInfo[5][$i];
            $this->Actor[$i]['ActorNameChinese'] = $_ActorInfo[4][$i];
            $this->Actor[$i]['ActorImage'] = $_ActorInfo[1][$i];
            $this->Actor[$i]['ActorRole'] = $_ActorInfo[7][$i];
        }
        return $this->Actor;
    }

    /**  
     * 获取电视剧的集数
     * @access public 
     * @return string
     */
    public function getEpisode()
    {
        $this->Episode = trim($this->preg('#集数:<\/span>([\s\S]*?)<br\/>#', $this->data, 1)[0]);
        return $this->Episode;
    }

    /**  
     * 获取电视剧的单集片长
     * @access public 
     * @return string
     */
    public function get_Single_episode_length()
    {
        $this->Single_episode_length = trim($this->preg('#单集片长:<\/span>([\s\S]*?)<br\/>#', $this->data, 1)[0]);
        return $this->Single_episode_length;
    }

    /**  
     * 获取电影的时长
     * @access public 
     * @return string
     */
    public function get_Movie_length()
    {
        $_ = $this->preg('#<span property="v:runtime" content="([\s\S]*?)">([\s\S]*?)<\/span>#', $this->data, 2)[0];
        $_ = $_ == null ? '' : $_;
        $this->Movie_length = $_;
        return $this->Movie_length;
    }

    /**  
     * 获取所有的导演
     * @access public 
     * @return string
     */
    public function get_All_directors()
    {
        $_ = $this->preg('#<span ><span class=\'pl\'>导演</span>: ([\s\S]*?)<br\/>#', $this->data, 1)[0];
        $_ = $_ == null ? '' : $_;
        $this->All_directors = str_replace(" ", '', strip_tags($_));
        return $this->All_directors;
    }

    /**  
     * 获取所有的编剧
     * @access public 
     * @return string
     */
    public function get_All_writers()
    {
        $_ = $this->preg('#<span ><span class=\'pl\'>编剧</span>: ([\s\S]*?)<br\/>#', $this->data, 1)[0];
        $_ = $_ == null ? '' : $_;
        $this->All_writers = str_replace(" ", '', strip_tags($_));
        return $this->All_writers;
    }

    /**  
     * 获取所有的演员
     * @access public 
     * @return string
     */
    public function get_All_actors()
    {
        $_ = $this->preg('#<span class="actor"><span class=\'pl\'>主演</span>: ([\s\S]*?)<br\/>#', $this->data, 1)[0];
        $_ = $_ == null ? '' : $_;
        $this->All_actors = str_replace(" ", '', strip_tags($_));
        return $this->All_actors;
    }

    /**  
     * 获取所有的地区
     * @access public 
     * @return string
     */
    public function get_region()
    {
        $_ = $this->preg('#<span class="pl">制片国家/地区:<\/span> ([\s\S]*?)<br\/>#', $this->data, 1)[0];
        $_ = $_ == null ? '' : $_;
        $this->region = str_replace(" ", '', strip_tags($_));
        return $this->region;
    }

    /**  
     * 获取影片播放链接
     * @access public 
     * @return array
     */
    public function getEpisodeUrl()
    {
        // 有播放链接的标识
        if (strpos($this->data, '<ul class="bs">') !== false) {
            #电影类型和电视剧类型区别
            if (strpos($this->data, '"@type": "Movie"') !== false) {
                $data = $this->getMovieEpisodeUrl($this->data);
                foreach ($data as $key => $value) {
                    $id[$key] = $value['from'];
                    $price[$key] = $value['url'];
                }
                array_multisort($price, SORT_NUMERIC, SORT_DESC, $id, SORT_STRING, SORT_ASC, $data);
                $this->EpisodeUrl = array('status' => 0, 'type' => 'movie', 'data' => $data);
            } elseif (strpos($this->data, '"@type": "TVSeries"') !== false) {
                $this->EpisodeUrl = array('status' => 0, 'type' => 'tv', 'data' => $this->getTVSeriesEpisodeUrl($this->data));
            } else {
                $this->EpisodeUrl = array('status' => 403);
            }
        } else {
            $this->EpisodeUrl = array('status' => 404);
        }
        return $this->EpisodeUrl;
    }

    /**  
     * 获取其他影片推荐
     * @access public 
     * @return array
     */
    public function getOtherLike()
    {
        $_data = $this->getSubstr($this->data, '<div class="recommendations-bd">', '</div>');
        $_id = $this->preg('#<a href="https:\/\/movie.douban.com\/subject\/(.*?)\/\?from=subject-page" class=""#', $_data, 1);
        $_img = $this->preg('#<img src="(.*?)" alt="(.*?)" class=""#', $_data, 1);
        $_name = $this->preg('#<img src="(.*?)" alt="(.*?)" class=""#', $_data, 2);
        try {
            $_count = count($_id);
            for ($i = 0; $i < $_count; $i++) {
                $this->OtherLike[$i]['id'] = $_id[$i];
                $this->OtherLike[$i]['name'] = $_name[$i];
                $this->OtherLike[$i]['img'] = $_img[$i];
            }
            return $this->OtherLike;
        } catch (\Throwable $th) {
            return array();
        }
    }

    /**  
     * 获取电视剧对应的播放链接
     * @access private 
     * @return array
     */
    private function getTVSeriesEpisodeUrl()
    {
        //播放链接在本页面
        if (strpos($this->data, 'var sources = {};') !== false) {
            preg_match_all('#sources\[(.*?)\] = \[([\s\S]*?)\];#', $this->data, $PlayUrlList);
            $i = 0;
            foreach ($PlayUrlList[2] as $a) {
                preg_match_all('#{play_link: "(.*)", ep: "#', $a, $ls);
                $PlayUrl[$i] = $ls[1];
                $i++;
            }
            return $this->doubanUrlToUrl($PlayUrl);
            #取js源码类型
        } else {
            preg_match_all('#<script type="text\/javascript" src="https:\/\/img3.doubanio.com\/misc\/mixed_static\/(.*?).js"><\/script>#', $this->data, $JsId);
            $JsUrl = 'https://img3.doubanio.com/misc/mixed_static/' . end($JsId[1]) . '.js';
            $JsData = $this->curl_get($JsUrl);
            preg_match_all('#sources\[(.*?)\] = \[([\s\S]*?)\];#', $JsData, $PlayUrlList);

            $i = 0;
            foreach ($PlayUrlList[2] as $list) {

                preg_match_all('#{play_link: "([\s\S]*?)", ep: "#', $PlayUrlList[2][$i], $ls);
                $j = 0;
                foreach ($ls[1] as $a) {
                    $PlayUrl[$i][$j] = $a;
                    $j++;
                }
                $i++;
            }

            return $this->doubanUrlToUrl($PlayUrl);
        }
    }

    /**  
     * 获取电影对应的播放链接
     * @access private 
     * @return array
     */
    private function getMovieEpisodeUrl()
    {
        preg_match_all('#<a class="playBtn" data-cn="(.*?)"([\s\S]*?)href="(.*?)"([\s\S]*?)>#', $this->data, $PlayList);
        for ($x = 0; $x < count($PlayList[1]); $x++) {
            $MovieEpisodeUrlData[$x]['from'] = $PlayList[1][$x];
            $MovieEpisodeUrlData[$x]['url'] = $this->doubanUrlToUrl($PlayList[3][$x]);
        }
        return $MovieEpisodeUrlData;
    }

    /**  
     * 去除播放链接中的豆瓣链接
     * @access private 
     * @param mixed $DoubanUrl 豆瓣播放链接
     * @return array
     */
    private function doubanUrlToUrl(&$DoubanUrl)
    {
        $DoubanUrl = str_replace('https://www.douban.com/link2/?url=', '', $DoubanUrl);
        $DoubanUrl = str_replace('http://www.douban.com/link2/?url=', '', $DoubanUrl);
        if (is_array($DoubanUrl)) {
            foreach ($DoubanUrl as $key => $val) {
                if (is_array($val)) {
                    $this->doubanUrlToUrl($DoubanUrl[$key]);
                }
            }
        }
        return $DoubanUrl;
    }


    /**
     * 获取电影的外部播放链接
     * @access private
     * @param string $wd 搜索影片名称
     * @return array
     */
    private function get_Out_Play_Url($_wd)
    {
        $_wd = urlencode($_wd);
        $_config = array(
            'url' => 'http://tiankongzy.cc/index.php/vod/search.html?wd=',
            'method' => 'get',
            'baseUrl' => 'http://tiankongzy.cc/index.php/vod/detail/id/[].html'
        );
        // 搜索结果数据
        // $_data = $this->curl_post($_config['url'], $_config['data']);
        $_data = $this->curl_get($_config['url'] . $_wd);
        // print_r($_data);
        // preg_match_all('#<a href="\/index.php\/vod\/detail\/id\/([\s\S]*?).html#', $_data, $_count);
        // 搜索结果数目
        // $_count =  $_count[1][0];
        preg_match_all('#<a href="\/index.php\/vod/detail\/id\/([\s\S]*?).html#', $_data, $_id);
        // 多个结果，默认选择第一个匹配

        $_id =  $_id[1][0];
        $_url =  str_replace("[]", $_id, $_config['baseUrl']);
        $_data = $this->curl_get($_url);


        preg_match_all('#<li><input type="checkbox" name="copy_sel" value="([\s\S]*?)" checked\/>([\s\S]*?)\$#', $_data, $_play);

        $_url = $_play[1];
        $_origin = $_play[2];
        $m = count($_url);
        for ($i = 0; $i < $m; $i++) {
            $_return[$i]['from'] = $_origin[$i];
            $_return[$i]['url'] = urlencode($_url[$i]);
        }
        return $_return;
    }
};
