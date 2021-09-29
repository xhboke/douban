<?php

/** 
 * 评论
 * 
 * 根据编号获取电影的评论
 * @author      xhboke 
 * @version     1.0
 */

include_once('class.Movie.php');
class MovieComment extends Movie
{
    public $id;
    public $status;
    public $sort;
    public $page;
    public $url;
    public $data;
    public $Comments;

    /**  
     * @access public 
     * @param string $id 豆瓣电影ID
     * @param int $page 评论页数
     * @param string $status 看过:P 在看:N 想看:F
     * @param string $sort 热门:new_score 最新:time
     * @param string $type 好评:h 一般:m 差评:l
     */
    public function __construct($id, $page = 0, $status = 'P', $sort = 'new_score', $type = '')
    {
        $this->id = $id;
        $this->status = $status;
        $this->sort = $sort;
        $this->page = $page;
        $this->url = parent::MovieRootUrl . '/subject/' . $this->id . '/comments?sort=' . $this->sort . '&status=' . $this->status . '&start=' . $page * 20 . '&percent_type=' . $type;
        $this->data = $this->curl_get($this->url);
    }
    public function __destruct()
    {
    }
    public function getComments()
    {
        $_data = $this->preg('#<div class="comment-item " data-cid="([\s\S]*?)">([\s\S]*?)<img src="([\s\S]*?)" class="" />([\s\S]*?)<span class="votes vote-count">([\s\S]*?)<\/span>([\s\S]*?)<a href="https:\/\/www.douban.com\/people\/([\s\S]*?)/" class="">([\s\S]*?)<\/a>([\s\S]*?)<span class="allstar([\s\S]*?)0 rating([\s\S]*?)<span class="comment-time " title="([\s\S]*?)">([\s\S]*?)<span class="short">([\s\S]*?)<\/span>#', $this->data, 0);
        $_count = count($_data[1]);
        for ($i = 0; $i < $_count; $i++) {
            $this->Comments[20 * $this->page + $i]['id'] = $_data[1][$i];
            $this->Comments[20 * $this->page + $i]['image'] = $_data[3][$i];
            $this->Comments[20 * $this->page + $i]['vote-count'] = $_data[5][$i];
            $this->Comments[20 * $this->page + $i]['uid'] = $_data[7][$i];
            $this->Comments[20 * $this->page + $i]['name'] = $_data[8][$i];
            $this->Comments[20 * $this->page + $i]['rating'] = floatval($_data[10][$i]);
            $this->Comments[20 * $this->page + $i]['time'] = $_data[12][$i];
            $this->Comments[20 * $this->page + $i]['content'] = $_data[14][$i];
        }
        return $this->Json($this->Comments);
    }
};
