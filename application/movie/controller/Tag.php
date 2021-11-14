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

    public function getMoive($MovieType = '热门', $MovieSort = 'recommend', $page_limit = '24', $page = 1): string
    {
        return MovieTag::getIndexMovie($MovieType, $MovieSort, $page_limit, $page);
    }

    public function getTv($TvType = '热门', $TvSort = 'recommend', $page_limit = '24', $page = 1): string
    {
        return MovieTag::getIndexTv($TvType, $TvSort, $page_limit, $page);
    }

    public function getNowplaying($_city = "chengdu"): \think\response\Json
    {
        return json(MovieTag::NowPlaying($_city));
    }
}
