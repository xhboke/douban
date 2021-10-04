<?php
/**
 * 影片分类
 *
 * @author      xhboke
 * @version     1.0
 */

namespace app\movie\controller;

use DouBanLib\MovieTag;

class Tag
{
    public function getTag($tags = '', $page = 0, $sort = 'U', $genres = '', $country = '', $year_range = ''): \think\response\Json
    {
        $obj = new MovieTag($tags, $page, $sort, $genres, $country, $year_range);
        return json($obj->getTag());
    }
}