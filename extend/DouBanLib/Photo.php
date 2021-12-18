<?php

/** 
 * 图片
 * /photos/photo
 * 
 * 根据编号获取名人相关信息
 * @author      xhboke 
 * @version     1.0
 */

namespace DouBanLib;

include_once('Movie.php');

class Photo extends Movie
{
    private $id;
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $data;

    public function __construct($id)
    {
        $this->id = $id;
        $this->url = parent::MovieRootUrl . '/photos/photo/' . $id . '/';
        $this->data = $this->curl_get($this->url);
    }

    /**
     * 获取图片及评论信息
     * /photos/photo/:id/
     * 
     * @static
     * @access public
     * @return array
     */
    public function getAll(): array
    {
        $_return = [];
        $_return['id'] = $this->id;
        $_return['title'] = $this->preg('#<h1><span id="title-anchor">([\s\S]*?)<\/span>#', $this->data, 1)[0];
        $_return['desc'] = trim(strip_tags($this->preg('#<div class="photo_descri">([\s\S]*?)</div>#', $this->data, 1)[0]));
        $_return['fav-num'] = $this->preg('#<span class="fav-num"([\s\S]*?)<a href="\#">([\s\S]*?)<\/a>#', $this->data, 2)[0];

        $_item = parent::preg('#<div class="comment-item"([\s\S]*?)<div class="group_banned">#', $this->data, 1);
        $_count = parent::getCount($_item);
        for ($i = 0; $i < $_count; $i++) {
            $_return['comment'][$i]['id'] = parent::preg('#id=([\s\S]*?) data-cid#', $_item[$i], 1)[0];
            $_return['comment'][$i]['name'] = parent::preg('#alt="([\s\S]*?)"\/>#', $_item[$i], 1)[0];
            $_return['comment'][$i]['avater'] = parent::preg('#src="([\s\S]*?)"#', $_item[$i], 1)[0];
            $_return['comment'][$i]['time'] = parent::preg('#<span class="">([\s\S]*?)<\/span>#', $_item[$i], 1)[0];
            $_return['comment'][$i]['content'] = parent::preg('#<p class="">([\s\S]*?)<\/p>#', $_item[$i], 1)[0];
            $_return['comment'][$i]['quote'] = parent::preg('#<span class="all">([\s\S]*?)<\/span>#', $_item[$i], 1)[0];
        }
        return $_return;
    }
}
