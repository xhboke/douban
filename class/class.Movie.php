<?php
include_once('class.DouBan.php');

class Movie extends DouBan
{
    public const MovieRootUrl = 'https://movie.douban.com';
    public const _MovieRootUrl = 'https://m.douban.com';

    public function isId()
    {
        if (empty($this->data)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
};
