<?php

/**
 * 影片基本信息
 *
 * @author      xhboke
 * @version     1.0
 */

namespace DouBanLib;

include_once('Movie.php');

class MovieInfo extends Movie
{
    public $id, $url, $data, $Name, $ChineseName, $Description, $DatePublished, $Genre, $Language, $Other_name, $Rating, $Votes, $Image, $Year, $IMDB, $Episode, $Actor, $region, $All_directors, $All_writers, $All_actors, $Single_episode_length, $EpisodeUrl, $All, $OtherLike;

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
     * /subject/:id/
     *
     * @access public
     * @param boolean $isAccurate 电影播放链接不存在下使用外部播放链接
     * @param boolean $is_play_url 从豆瓣电影获取电影的播放链接
     * @return array
     */
    public function getAll(bool $isAccurate, bool $is_play_url = True): array
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
        $this->All['Other_name'] = $this->get_OtherName();
        $this->All['Rating'] = $this->getRating();
        $this->All['RatingPeople'] = $this->getRatingPeople();
        $this->All['Votes'] = $this->getVotes();
        $this->All['Description'] = $this->getDescription();
        $this->All['Episode'] = $this->getEpisode();
        $this->All['Single_episode_length'] = $this->get_Single_episode_length();
        $this->All['Movie_length'] = $this->get_MovieLength();
        $this->All['IMDB'] = $this->getIMDB();
        $this->All['All_directors'] = $this->get_All_directors();
        $this->All['All_writers'] = $this->get_All_writers();
        $this->All['All_actors'] = $this->get_All_actors();
        $this->All['Actors'] = $this->getActor();
        if ($is_play_url == True) {
            $this->All['EpisodeUrl'] = $this->getEpisodeUrl();
        }
        # 201是豆瓣电影无资源，202是资源网无资源
        if ($this->All['EpisodeUrl']['status'] == 201 && $isAccurate == true) {
            $_ = $this->get_Out_Play_Url($this->All['ChineseName']);
            $this->All['EpisodeUrl']['data'] = $_;
            if (count($_) > 0) {
                $this->All['EpisodeUrl']['status'] = 300;
            } else {
                $this->All['EpisodeUrl']['status'] = 567;
            }
            $this->All['EpisodeUrl']['type'] = 'movie';
        }
        $this->All['OtherLike'] = $this->getOtherLike();
        return $this->All;
    }

    /**
     * 只获取电影的基本信息
     * @access public
     * @return array
     */
    public function get_INFO(): array
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
        $this->All['Movie_length'] = $this->get_MovieLength();
        return $this->All;
    }

    /**
     * 获取电影豆瓣ID
     * @access public
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * 获取电影豆瓣页面数据
     * @access public
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * 获取电影的名称（可能含有其他语言）
     * @access public
     * @return string
     */
    public function getName(): string
    {
        $this->Name = $this->preg('#"name": "([\s\S]*?)"#', $this->data, 1)[0];
        return $this->Name;
    }

    /**
     * 获取电影的中文名称（去除其他语言）
     * @access public
     * @return string
     */
    public function getChineseName(): string
    {
        $this->ChineseName = trim(str_replace(array("\n", "\r", "(豆瓣)"), "", $this->preg('#<title>([\s\S]*?)<\/title>#', $this->data, 1)[0]));
        return $this->ChineseName;
    }

    /**
     * 获取电影的描述（富文本会含<br />）
     * @access public
     * @return string
     */
    public function getDescription(): string
    {
        if (strpos($this->data, '<span class="all hidden">') !== false) {
            $this->Description = trim($this->preg('#<span class="all hidden">([\s\S]*?)<\/span>#', $this->data, 1)[0]);
        } else {
            $this->Description = trim($this->preg('#<span property="v:summary" class="">([\s\S]*?)<\/span>#', $this->data, 1)[0]);
        }
        return $this->Description;
    }

    /**
     * 获取电影的出版日期
     * @access public
     * @return string
     */
    public function getDatePublished(): string
    {
        $_ = $this->preg('#<span property="v:initialReleaseDate" content="([\s\S]*?)">#', $this->data, 1);
        $this->DatePublished = $_ == null ? '' : implode('/', $_);
        return $this->DatePublished;
    }

    /**
     * 获取电影的语言
     * @access public
     * @return string
     */
    public function get_Language(): string
    {
        $_ = str_replace(" ", "", trim($this->preg('#语言:<\/span>([\s\S]*?)<br\/>#', $this->data, 1)[0]));
        $this->Language = $_ == null ? '' : $_;
        return $this->Language;
    }

    /**
     * 获取电影的其他名称（多个以"/"分割）
     * @access public
     * @return string
     */
    public function get_OtherName(): string
    {
        $this->Other_name = str_replace(" ", '', strip_tags($this->preg('#又名:<\/span>([\s\S]*?)<br\/>#', $this->data, 1)[0]));
        return $this->Other_name;
    }

    /**
     * 获取电影的类型
     * @access public
     * @return array
     */
    public function getGenre(): array
    {
        $this->Genre = $this->preg('#<span property="v:genre">([\s\S]*?)<\/span>#', $this->data, 1);
        return $this->Genre;
    }

    /**
     * 获取电影的评分
     * @access public
     * @return string
     */
    public function getRating(): string
    {
        $this->Rating = $this->preg('#property="v:average">([\s\S]*?)<\/strong>#', $this->data, 1)[0];
        return $this->Rating;
    }

    /**
     * 获取电影的评分人数
     * @access public
     * @return string
     */
    public function getVotes(): string
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
    public function getImage(): string
    {
        $this->Image = $this->preg('#"image": "([\s\S]*?)",#', $this->data, 1)[0];
        return $this->Image;
    }

    /**
     * 获取电影的年代
     * @access public
     * @return string
     */
    public function getYear(): string
    {
        if (strpos($this->data, '<span class="year">') !== false) {
            $this->Year = $this->preg('#<span class="year">\(([\s\S]*?)\)<\/span>#', $this->data, 1)[0];
        } elseif (strpos($this->data, 'v:initialReleaseDate') !== false) {
            $this->Year = $this->preg('#<span property="v:initialReleaseDate" content="([\s\S]*?)-#', $this->data, 1)[0];
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
    public function getIMDB(): string
    {
        $this->IMDB = trim($this->preg('#IMDb:<\/span>([\s\S]*?)<br>#', $this->data, 1)[0]);
        return $this->IMDB;
    }

    /**
     * 获取电影参与演员（包含头像）
     * @access public
     * @return array
     */
    public function getActor(): array
    {
        $_ActorInfo = $this->preg('#<div class="avatar" style="background-image: url\(([\s\S]*?)\)">([\s\S]*?)<span class="name"><a href="https:\/\/movie.douban.com\/celebrity\/([\s\S]*?)/" title="([\s\S]*?)" class="name">([\s\S]*?)<\/a>([\s\S]*?)<span class="role" title="([\s\S]*?)">#', $this->data, 0);
        $_count = $this->getCount($_ActorInfo[0]);
        $this->Actor = [];
        for ($i = 0; $i < $_count; $i++) {
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
    public function getEpisode(): string
    {
        $this->Episode = trim($this->preg('#集数:<\/span>([\s\S]*?)<br\/>#', $this->data, 1)[0]);
        return $this->Episode;
    }

    /**
     * 获取电视剧的单集片长
     * @access public
     * @return string
     */
    public function get_Single_episode_length(): string
    {
        $this->Single_episode_length = trim($this->preg('#单集片长:<\/span>([\s\S]*?)<br\/>#', $this->data, 1)[0]);
        return $this->Single_episode_length;
    }

    /**
     * 获取电影的时长
     * @access public
     * @return string
     */
    public function get_MovieLength(): string
    {
        $_ = $this->preg('#<span property="v:runtime" content="([\s\S]*?)">([\s\S]*?)<\/span>#', $this->data, 2)[0];
        return $_ == null ? '' : $_;
    }

    /**
     * 获取所有的导演
     * @access public
     * @return string
     */
    public function get_All_directors(): string
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
    public function get_All_writers(): string
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
    public function get_All_actors(): string
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
    public function get_region(): string
    {
        $_ = $this->preg('#<span class="pl">制片国家/地区:<\/span> ([\s\S]*?)<br\/>#', $this->data, 1)[0];
        $_ = $_ == null ? '' : $_;
        $this->region = str_replace(" ", '', strip_tags($_));
        return $this->region;
    }

    /**
     * 获取各评分人数
     * @access public
     * @return array
     */
    public function getRatingPeople(): array
    {
        $this->RatingPeople = $this->preg('#<span class="rating_per">([\s\S]*?)<\/span>#', $this->data, 1);
        return $this->RatingPeople;
    }

    /**
     * 获取影片播放链接
     * @access public
     * @return array
     */
    public function getEpisodeUrl(): array
    {
        // 有播放链接的标识
        if (strpos($this->data, '<ul class="bs">') !== false) {
            #电影类型和电视剧类型区别
            if (strpos($this->data, '"@type": "Movie"') !== false) {
                $data = $this->getMovieEpisodeUrl();
                foreach ($data as $key => $value) {
                    $id[$key] = $value['from'];
                    $price[$key] = $value['url'];
                }
                array_multisort($price, SORT_NUMERIC, SORT_DESC, $id, SORT_STRING, SORT_ASC, $data);
                $this->EpisodeUrl = array('status' => 200, 'type' => 'movie', 'data' => $data);
            } elseif (strpos($this->data, '"@type": "TVSeries"') !== false) {
                $this->EpisodeUrl = array('status' => 200, 'type' => 'tv', 'data' => $this->getTVSeriesEpisodeUrl());
            } else {
                $this->EpisodeUrl = array('status' => 999);
            }
        } else {
            $this->EpisodeUrl = array('status' => 201);
        }
        return $this->EpisodeUrl;
    }

    /**
     * 获取其他影片推荐
     * @access public
     * @return array
     */
    public function getOtherLike(): array
    {
        $_data = $this->getSubstr($this->data, '<div class="recommendations-bd">', '</div>');
        $_id = $this->preg('#<a href="https:\/\/movie.douban.com\/subject\/(.*?)\/\?from=subject-page" class=""#', $_data, 1);
        $_img = $this->preg('#<img src="(.*?)" alt="(.*?)" class=""#', $_data, 1);
        $_name = $this->preg('#<img src="(.*?)" alt="(.*?)" class=""#', $_data, 2);
        $_count = $this->getCount($_id);
        $this->OtherLike = [];
        for ($i = 0; $i < $_count; $i++) {
            $this->OtherLike[$i]['id'] = $_id[$i];
            $this->OtherLike[$i]['name'] = $_name[$i];
            $this->OtherLike[$i]['img'] = $_img[$i];
        }
        return $this->OtherLike;
    }

    /**
     * 获取电视剧对应的播放链接
     * @access private
     * @return array
     */
    private function getTVSeriesEpisodeUrl(): array
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
        }
        return $this->doubanUrlToUrl($PlayUrl);
    }

    /**
     * 获取电影对应的播放链接
     * @access private
     * @return array
     */
    private function getMovieEpisodeUrl(): array
    {
        preg_match_all('#<a class="playBtn" data-cn="(.*?)"([\s\S]*?)href="(.*?)"([\s\S]*?)>#', $this->data, $PlayList);
        $MovieEpisodeUrlData = [];
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
     * @return mixed
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
     * @param string $_wd 搜索影片名称
     * @return array
     */
    private function get_Out_Play_Url(string $_wd): array
    {
        $_wd = urlencode($_wd);
        $_config = array(
            'url' => 'http://tiankongzy.cc/index.php/vod/search.html?wd=',
            'method' => 'get',
            'baseUrl' => 'http://tiankongzy.cc/index.php/vod/detail/id/[].html'
        );
        $_data = $this->curl_get($_config['url'] . $_wd);
        preg_match_all('#<a href="\/index.php\/vod/detail\/id\/([\s\S]*?).html#', $_data, $_id);
        // 多个结果，默认选择第一个匹配
        $_id = $_id[1][0];
        $_url = str_replace("[]", $_id, $_config['baseUrl']);
        $_data = $this->curl_get($_url);
        preg_match_all('#<li><input type="checkbox" name="copy_sel" value="([\s\S]*?)" checked\/>([\s\S]*?)\$#', $_data, $_play);
        $_url = $_play[1];
        $_origin = $_play[2];
        $m = count($_url);
        $_return = [];
        for ($i = 0; $i < $m; $i++) {
            $_return[$i]['from'] = $_origin[$i];
            $_return[$i]['url'] = urlencode($_url[$i]);
        }
        return $_return;
    }

    /**
     * 获取影片的所有参演人员
     * /subject/:id/celebrities
     *
     * @static
     * @access public
     * @param int $id
     * @return array
     */
    static public function getCelebrities(int $id): array
    {
        $_data = parent::curl_get(parent::MovieRootUrl . '/subject/' . $id . '/celebrities');
        $_item = parent::preg('#<li class="celebrity">([\s\S]*?)<\/li>#', $_data, 1);
        $_count = parent::getCount($_item);
        $_return = [];
        for ($i = 0; $i < $_count; $i++) {
            $_return[$i]['id'] = parent::preg('#celebrity\/([\s\S]*?)\/" title="([\s\S]*?)" class="name">#', $_item[$i], 1)[0];
            $_return[$i]['name'] = parent::preg('#" class="name">([\s\S]*?)<\/a><\/span>#', $_item[$i], 1)[0];
            $_return[$i]['image'] = parent::preg('#background-image: url\(([\s\S]*?)\)">#', $_item[$i], 1)[0];
            $_return[$i]['role'] = parent::preg('#<span class="role" title="([\s\S]*?)">#', $_item[$i], 1)[0];
            $_works = parent::preg('#subject\/([\s\S]*?)\/" target([\s\S]*?)>([\s\S]*?)<\/a>#', $_item[$i], 0);
            $_count_works = parent::getCount($_works[1]);
            $_return[$i]['works'] = [];
            for ($j = 0; $j < $_count_works; $j++) {
                $_return[$i]['works'][$j]['id'] = $_works[1][$j];
                $_return[$i]['works'][$j]['name'] = $_works[3][$j];
            }
        }
        return $_return;
    }

    /**
     * 获取影片的全部图片
     * /subject/:id/all_photos
     *
     * @static
     * @access public
     * @param string $id
     * @return array
     */
    static public function getAllPhotos(string $id): array
    {

        $_data = parent::getSubstr(parent::curl_get(parent::MovieRootUrl . "/subject/" . $id . '/all_photos'), '<div id="content">', '<div class="aside">');
        $_item = parent::preg('#<h2>([\s\S]*?)&nbsp;([\s\S]*?)type=([\s\S]*?)" target="_self">([\s\S]*?)<\/a>#', $_data, 0);
        $_count = parent::getCount($_item[1]);
        $_return = [];
        for ($i = 0; $i < $_count; $i++) {
            $_return[$i]['name'] = trim($_item[1][$i]);
            $_return[$i]['type'] = $_item[3][$i];
            $_return[$i]['count'] = $_item[4][$i];
            $_return[$i]['image'] = [];
            $_image = parent::preg('#photos\/photo\/([\s\S]*?)\/">([\s\S]*?)<img src="([\s\S]*?)">#', parent::getSubstr($_data, $_return[$i]['name'], '</ul>'), 0);
            $_count_image = isset($_image[1]) ? count($_image[1]) : 0;
            for ($j = 0; $j < $_count_image; $j++) {
                $_return[$i]['image'][$j]['id'] = $_image[1][$j];
                $_return[$i]['image'][$j]['url'] = $_image[3][$j];
            }
        }
        return $_return;
    }

    /**
     * 获取影片图片
     * /subject/:id/photos?type=S&start=30&sortby=like&size=a&subtype=a
     *
     * @static
     * @access public
     * @param string $id
     * @param intenger $page
     * @param string $type S:剧照、R:海报、W:壁纸
     * @param string $sort
     * @return array
     */
    static public function getPhotos(string $id, $page = 0, string $type = 'S', string $sort = 'like'): array
    {
        $_data = parent::curl_get(parent::MovieRootUrl . "/subject/" . $id . '/photos' . '?start=' . 30 * $page . '&type=' . $type . '&sortby=' . $sort);
        $_item = parent::preg('#data-id="([\s\S]*?)">([\s\S]*?)<\/li>#', $_data, 0);
        $_count = parent::getCount($_item[1]);
        $_return = [];
        for ($i = 0; $i < $_count; $i++) {
            $_return[$i]['id'] = $_item[1][$i];
            $_return[$i]['image'] = parent::preg('#<img src="([\s\S]*?)" \/>#', $_item[2][$i], 1)[0];
            $_return[$i]['size'] = trim(parent::preg('#<div class="prop">([\s\S]*?)<\/div>#', $_item[2][$i], 1)[0]);
            $_return[$i]['desc'] = trim(preg_replace('#<a([\s\S]*?)<\/a>#', '', parent::preg('#<div class="name">([\s\S]*?)<\/div>#', $_item[2][$i], 1)[0]));
            $_return[$i]['reply'] = trim(parent::preg('#comments">([\s\S]*?)<\/a>#', $_item[2][$i], 1)[0]);
        }
        return $_return;
    }

    /**
     * 播放影片
     *
     * @static
     * @access public
     * @param string $url
     * @return string
     */
    static public function play(string $url)
    {
        return json_decode(parent::curl_get('https://json.pangujiexi.com:12345/json.php?url=' . $url));
    }
}
