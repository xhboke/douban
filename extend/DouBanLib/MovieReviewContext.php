<?php

namespace DouBanLib;

include_once('Movie.php');

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
        $this->MovieInfo['id'] = array_shift($_data[1]);
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
        return $this->All;
    }
}
