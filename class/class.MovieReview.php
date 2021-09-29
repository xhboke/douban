<?php

/** 
 * 长评论
 * 
 * 根据影片编号获取长评论（）
 * @author      xhboke 
 * @version     1.0
 */


include_once('class.Movie.php');

/**
 * 影片对应影评
 */
class MovieReviews extends Movie
{
    public $id;
    public $url;
    public $data;
    public $Reviews;
    public function __construct($id, $page = 0, $sort = 'hotest')
    {
        $this->id = $id;
        $this->url = parent::MovieRootUrl . '/subject/' . $id . '/reviews?start=' . $page * 20 . '&sort=' . $sort;
        $this->data = $this->curl_get($this->url);
    }
    public function getReviews()
    {
        $_data = $this->preg('#<div data-cid="([\s\S]*?)">([\s\S]*?)<img width="24" height="24" src="([\s\S]*?)">([\s\S]*?)<a href="https:\/\/www.douban.com\/people\/([\s\S]*?)\/" class="name">([\s\S]*?)</a>([\s\S]*?)<span content="([\s\S]*?)" class="main-meta">([\s\S]*?)<h2><a href="([\s\S]*?)">([\s\S]*?)<\/a><\/h2>([\s\S]*?)<div class="short-content">([\s\S]*?)</div>([\s\S]*?)<span id="r-useful_count-([\s\S]*?)">([\s\S]*?)</span>([\s\S]*?)<span id="r-useless_count-([\s\S]*?)">([\s\S]*?)<\/span>#', $this->data, 0);
        $_count = count($_data[1]);
        for ($i = 0; $i < $_count; $i++) {
            $this->Reviews[$i]['id'] = $_data[1][$i];
            $this->Reviews[$i]['image'] = $_data[3][$i];
            $this->Reviews[$i]['uid'] = $_data[5][$i];
            $this->Reviews[$i]['name'] = $_data[6][$i];
            $_rating = $this->preg('#<span class="allstar([\s\S]*?)0 main-title-rating"#', $_data[7][$i], 1);
            $this->Reviews[$i]['rating'] = isset($_rating[0]) ? $_rating[0] : 'Null';
            $this->Reviews[$i]['time'] = $_data[8][$i];
            $this->Reviews[$i]['title'] = $_data[11][$i];
            $_content = preg_replace('#&nbsp;\(<([\s\S]*?)>展开<\/a>\)#', '', $_data[13][$i]);
            $_content = preg_replace('#<p class="spoiler-tip">([\s\S]*?)<\/p>#', '[剧透]', $_content);
            $this->Reviews[$i]['content'] = trim($_content);
            $this->Reviews[$i]['useful_count'] = trim($_data[16][$i]);
            $this->Reviews[$i]['useless_count'] = trim($_data[19][$i]);
        }
        return $this->Json($this->Reviews);
    }
};

/** 
 * 长评论具体
 * 
 * 根据评论编号获取具体评论
 * @author      xhboke 
 * @version     1.0
 */
class MovieReviewContext extends Movie
{
    public $id;
    public $url;
    public $data;
    public $Title;
    public $Reviewer;
    public $MovieInfo;
    public $Context;
    public $All;
    public function __construct($id)
    {
        $this->id = $id;
        $this->url = parent::MovieRootUrl . '/review/' . $id . '/';
        $this->data = $this->curl_get($this->url);
    }

    public function getTitle()
    {
        $this->Title = $this->preg('#<span property="v:summary">([\s\S]*?)<\/span>#', $this->data, 1)[0];
        return $this->Title;
    }
    public function getReviewer()
    {
        $_data = $this->preg('#<a href="https:\/\/www.douban.com\/people\/([\s\S]*?)\/">([^\<]*\n[^\<]*)*<span>([\s\S]*?)<\/span>#', $this->data, 0);
        $this->Reviewer['uid'] = $_data[1][0];
        $this->Reviewer['name'] = $_data[3][0];
        return $this->Reviewer;
    }
    public function getMovieInfo()
    {
        $_data = $this->preg('#<a href="https:\/\/movie.douban.com\/subject\/([\s\S]*?)\/">([\s\S]*?)<\/a>#', $this->data, 0);
        $this->MovieInfo['id']  = array_shift($_data[1]);
        $this->MovieInfo['name'] = array_shift($_data[2]);
        return $this->MovieInfo;
    }
    public function getContext()
    {
        $this->Context = $this->preg('#<div id="link-report">([\s\S]*?)<div class="main-author">#', $this->data, 1)[0];
        return $this->Context;
    }
    public function getAll()
    {
        $this->All['id'] = $this->id;
        $this->All['title'] = $this->getTitle();
        $this->All['reviewer'] = $this->getReviewer();
        $this->All['movie'] = $this->getMovieInfo();
        $this->All['context'] = $this->getContext();
        return $this->Json($this->All);
    }
};
