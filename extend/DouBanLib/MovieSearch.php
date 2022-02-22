<?php

/**
 * 搜索影片
 *
 * 根据关键词搜索影片，获取搜索关键词提示
 * @author      xhboke
 * @version     1.0
 */

namespace DouBanLib;

include_once('Movie.php');

class MovieSearch extends Movie
{
    public $name;
    public $page;
    public $url;
    public $data;
    public $Count;
    public $SearchData;

    /**
     * 搜索电影信息
     * @access public
     * @param string $name 搜索影片名称
     * @param int $page 页码
     */
    public function __construct(string $name, int $page = 0)
    {
        $this->name = $name;
        $this->page = $page;
        $this->url = parent::_MovieRootUrl . '/j/search/?q=' . $name . '&t=1002&p=' . $page;
        $this->data = json_decode($this->curl_get($this->url));
        $this->Count = $this->data->count;
    }

    /**
     * 获取搜索电影信息
     * @access public
     * @return array
     */
    public function getSearchData(): array
    {
        if ($this->isEmpty()) {
            $_name = $this->preg('#<span class="subject-title">([\s\S]*?)<\/span>#', $this->data->html, 1);
            $_image = $this->preg('#<img src="([\s\S]*?)"\/>#', $this->data->html, 1);
            $_id = $this->preg('#\/movie\/subject\/(.*)\/">#', $this->data->html, 1);
            $_rating = $this->preg('#<span>(.*)<\/span>#', $this->data->html, 1);
            $_count = count($_id);
            $this->SearchData['count'] = $this->Count;
            for ($i = 0; $i < $_count; $i++) {
                $this->SearchData['data'][$this->page * 10 + $i]['id'] = $_id[$i];
                $this->SearchData['data'][$this->page * 10 + $i]['name'] = $_name[$i];
                $this->SearchData['data'][$this->page * 10 + $i]['rating'] = $_rating[$i];
                $this->SearchData['data'][$this->page * 10 + $i]['image'] = $_image[$i];
            }
        } else {
            $this->SearchData['count'] = 0;
            $this->SearchData['data'] = [];
        }
        return $this->SearchData;
    }

    /**
     * 获取搜索影片关键词提示
     * @static
     * @access public
     * @param string $name 搜索词
     * @return array
     */
    static public function getSearchSuggest(string $name): array
    {
        return json_decode(parent::curl_get(parent::MovieRootUrl . '/j/subject_suggest?q=' . $name), true);
    }

    /**
     * 判断搜索结果是否为空
     * @access private
     * @return boolean
     */
    private function isEmpty(): bool
    {
        if ($this->Count == 0) {
            return false;
        }
        return true;
    }
}

;
