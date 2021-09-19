<?php
include_once('class.Movie.php');

class MovieSearch extends Movie
{
    public $name;
    public $page;
    public $url;
    public $data;
    public $Count;
    public $SearchData;
    public function __construct($name, $page = 0)
    {
        $this->name = $name;
        $this->page = $page;
        $this->url = parent::_MovieRootUrl . '/j/search/?q=' . $name . '&t=movie&p=' . $page;
        $this->data = json_decode($this->curl_get($this->url));
        $this->Count = $this->data->count;
    }

    public function getSearchData()
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
        return $this->Json($this->SearchData);
    }

    public static function getSearchSuggest($name)
    {
        $_url = 'https://movie.douban.com/j/subject_suggest?q=' . $name;
        $_data = parent::curl_get($_url);
        $_json = json_decode($_data, true);

        $_count = count($_json);
        $_return = [];
        for ($i = 0; $i < $_count; $i++) {
            $_return[$i] = $_json[$i]['title'];
        }
        return parent::Json($_return);
    }

    private function isEmpty()
    {
        if ($this->Count == 0) {
            return false;
        } else {
            return true;
        }
    }
}
