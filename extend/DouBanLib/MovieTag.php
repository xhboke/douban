<?php

/**
 * 指定类型影片
 *
 * 获取指定类型影片，TOP250\各种分类\自定义
 * @author      xhboke
 * @version     1.0
 */

namespace DouBanLib;

include_once('Movie.php');

class MovieTag extends Movie
{
    public $tags;
    public $page;
    public $sort;
    public $genres;
    public $country;
    public $year_range;
    public $url;
    public $data;
    public $MovieTagData;
    public $Count;

    /**
     * 分类影片
     * /tag/#/?sort=T&range=0,10&tags=电影,剧情,中国大陆,经典,2020
     *
     * @access public
     * @param string $tags 电影标签以","分割
     * @param int $page 页码
     * @param string $sort 标记最多:T,近期热门:U,最高评分:S,最新上影:R
     * @param string $genres 电影类型（剧情、喜剧、动作...)
     * @param string $country 电影地区（中国大陆、美国、日本...)
     * @param string $year_range 时间范围（例如：2019,2020）
     */
    public function __construct(string $tags = '', int $page = 0, string $sort = 'U', string $genres = '', string $country = '', string $year_range = '')
    {
        $this->tags = $tags;
        $this->page = $page;
        $this->sort = $sort;
        $this->genres = $genres;
        $this->country = $country;
        $this->year_range = $year_range;
        $this->url = parent::MovieRootUrl . '/j/new_search_subjects?sort=' . $this->sort . '&range=0,10&tags=' . $this->tags . '&start=' . $this->page * 20 . '&genres=' . $this->genres . '&countries=' . $this->country . '&year_range=' . $this->year_range;
        $this->data = json_decode($this->curl_get($this->url));
    }

    /**
     * 获取指定类型的影片
     * @access public
     * @return array
     */
    public function getTag(): array
    {
        $this->MovieTagData = array();
        $this->Count = count($this->data->data);
        if ($this->Count) {
            $this->MovieTagData['count'] = $this->Count;
            $_page_start = $this->page * 20;
            $_page_end = $_page_start + $this->Count;
            for ($i = $_page_start; $i < $_page_end; $i++) {
                $this->MovieTagData['data'][$i]['id'] = $this->data->data[$i - $_page_start]->id;
                $this->MovieTagData['data'][$i]['name'] = $this->data->data[$i - $_page_start]->title;
                $this->MovieTagData['data'][$i]['image'] = $this->data->data[$i - $_page_start]->cover;
                $this->MovieTagData['data'][$i]['rating'] = $this->data->data[$i - $_page_start]->rate;
            }
        } else {
            $this->MovieTagData['data'] = [];
        }
        return $this->MovieTagData;
    }

    /**
     * 获取豆瓣电影250榜单
     * @param int $page 页数
     * @return array
     */
    static public function getTop250($page): array
    {
        $_page_start = $page * 25;
        $_url = parent::MovieRootUrl . '/top250?start=' . $_page_start;
        $_data = parent::curl_get($_url);
        $_ = parent::preg('#<em class="">([\s\S]*?)<\/em>([\s\S]*?)<a href="https:\/\/movie.douban.com\/subject\/([\s\S]*?)\/">([\s\S]*?)<img width="100" alt="([\s\S]*?)" src="([\s\S]*?)" class="">#', $_data, 0);
        $_rank = parent::preg('#<span class="rating_num" property="v:average">([\s\S]*?)<\/span>#', $_data, 1);
        try {
            $_count = count($_[0]);
            $_return = [];
            if ($_count) {
                for ($i = 0; $i < $_count; $i++) {
                    $_return[$i]['em'] = $_[1][$i];
                    $_return[$i]['id'] = $_[3][$i];
                    $_return[$i]['name'] = $_[5][$i];
                    $_return[$i]['rank'] = $_rank[$i];
                    $_return[$i]['img'] = $_[6][$i];
                }
            } else {
                $_return = [];
            }
            return $_return;
        } catch (\Throwable $th) {
            return [];
        }
    }

    static public function getIndexMovie($MovieType = '热门', $MovieSort = 'recommend', $page_limit = '24', $page = 1): string
    {
        $page_start = ($page - 1) * 24;
        $MovieUrl = 'https://movie.douban.com/j/search_subjects?type=movie&tag=' . $MovieType . '&sort=' . $MovieSort . '&page_limit=' . $page_limit . '&page_start=' . $page_start;
        return parent::curl_get($MovieUrl);
    }

    static public function getIndexTv($TvType = '热门', $TvSort = 'recommend', $page_limit = '24', $page = 1): string
    {
        $page_start = ($page - 1) * 24;
        $TvUrl = 'https://movie.douban.com/j/search_subjects?type=tv&tag=' . $TvType . '&sort=' . $TvSort . '&page_limit=' . $page_limit . '&page_start=' . $page_start;
        return parent::curl_get($TvUrl);
    }

    /**
     * NowPlaying 正在上映
     * @return array
     */
    static public function NowPlaying($_city = "chengdu"): array
    {

        $_url = parent::MovieRootUrl . '/cinema/nowplaying/' . $_city . '/';
        $_data = parent::getSubstr(parent::curl_get($_url), '<div id="nowplaying">', '<div id="upcoming">');
        $_ = parent::preg('#id="([\s\S]*?)"([\s\S]*?)data-title="([\s\S]*?)"([\s\S]*?)data-score="([\s\S]*?)"([\s\S]*?)data-star="([\s\S]*?)"([\s\S]*?)data-release="([\s\S]*?)"([\s\S]*?)data-duration="([\s\S]*?)"([\s\S]*?)data-region="([\s\S]*?)"([\s\S]*?)data-director="([\s\S]*?)"([\s\S]*?)data-actors="([\s\S]*?)"([\s\S]*?)data-votecount="([\s\S]*?)"([\s\S]*?)<img src="([\s\S]*?)"#', $_data, 0);
        $_count = parent::getCount($_[1]);
        $_return = [];
        for ($i = 0; $i < $_count; $i++) {
            $_return[$i]['id'] = $_[1][$i];
            $_return[$i]['name'] = $_[3][$i];
            $_return[$i]['rating'] = $_[5][$i];
            $_return[$i]['release'] = $_[9][$i];
            $_return[$i]['duration'] = $_[11][$i];
            $_return[$i]['region'] = $_[13][$i];
            $_return[$i]['director'] = $_[15][$i];
            $_return[$i]['actors'] = $_[17][$i];
            $_return[$i]['votecount'] = $_[19][$i];
            $_return[$i]['image'] = $_[21][$i];
        }
        return $_return;
    }
};
