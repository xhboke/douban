<?php
/**
 * 影片搜索
 *
 * @author      xhboke
 * @version     1.0
 */

namespace app\movie\controller;

use DouBanLib\MovieSearch;

class Search
{

    public function getSearchSuggest($search_suggest): \think\response\Json
    {
        return json(MovieSearch::getSearchSuggest($search_suggest));
    }

    public function getSearch($name, $page): \think\response\Json
    {
        $obj = new MovieSearch($name, $page);
        return json($obj->getSearchData());
    }
}