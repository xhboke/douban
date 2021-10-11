<?php

/**
 * Movie
 *
 * 豆瓣电影的基本变量和方法
 * @author      xhboke
 * @version     1.0
 */

namespace DouBanLib;

include_once('DouBan.php');

class Movie extends DouBan
{
    const MovieRootUrl = 'https://movie.douban.com';
    const _MovieRootUrl = 'https://m.douban.com';

    public function isId(): bool
    {
        if (empty($this->data)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
