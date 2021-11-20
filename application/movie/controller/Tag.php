<?php

/**
 * 影片分类
 *
 * @author      xhboke
 * @version     1.0
 */

namespace app\movie\controller;

use DouBanLib\MovieTag;
use think\facade\Cache;

class Tag
{
    public function getTag($tags = '', $page = 0, $sort = 'U', $genres = '', $country = '', $year_range = ''): \think\response\Json
    {
        $cacheName = 'Tag_' . $tags . '_' . $page . '_' . $sort . '_' . $genres . '_' . $country . '_' . $year_range;

        if (cache($cacheName)) {
            return cache($cacheName);
        } else {
            $obj = new MovieTag($tags, $page, $sort, $genres, $country, $year_range);
            $data = $obj->getTag();
            if (!empty($data)) {
                cache($cacheName, json($data));
            }
            return json($data);
        }
    }

    public function getMoive($MovieType = '热门', $MovieSort = 'recommend', $page_limit = '24', $page = 1): string
    {
        $cacheName = 'Movie_' . $MovieType . '_' . $MovieSort . '_' . $page_limit . '_' . $page;

        if (cache($cacheName)) {
            return cache($cacheName);
        } else {
            $data = MovieTag::getIndexMovie($MovieType, $MovieSort, $page_limit, $page);
            if (!empty($data)) {
                cache($cacheName, $data);
            }
            return $data;
        }
    }

    public function getTv($TvType = '热门', $TvSort = 'recommend', $page_limit = '24', $page = 1): string
    {
        $cacheName = 'Tv_' . $TvType . '_' . $TvSort . '_' . $page_limit . '_' . $page;

        if (cache($cacheName)) {
            return cache($cacheName);
        } else {
            $data = MovieTag::getIndexTv($TvType, $TvSort, $page_limit, $page);
            if (!empty($data)) {
                cache($cacheName, $data);
            }
            return $data;
        }
    }

    public function getNowplaying($_city = "chengdu"): \think\response\Json
    {
        $cacheName = 'Nowplaying_' . $_city;

        if (cache($cacheName)) {
            return cache($cacheName);
        } else {
            $data = MovieTag::NowPlaying($_city);
            if (!empty($data)) {
                cache($cacheName, json($data));
            }
            return json($data);
        }
    }
}
