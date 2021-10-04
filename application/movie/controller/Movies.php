<?php
/**
 * 影片的集合
 *
 * @author      xhboke
 * @version     1.0
 */

namespace app\movie\controller;

use DouBanLib\MovieTag;

class Movies
{
    public function getTop250($page = 0): \think\response\Json
    {
        return json(MovieTag::getTop250($page));
    }
}