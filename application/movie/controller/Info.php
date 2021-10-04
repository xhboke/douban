<?php
/**
 * 影片信息
 *
 * @author      xhboke
 * @version     1.0
 */

namespace app\movie\controller;

use DouBanLib\MovieInfo;
use DouBanLib\MovieComment;
use DouBanLib\MovieReview;

class Info
{
    public function getMovieInfo($id): \think\response\Json
    {
        $obj = new MovieInfo($id);
        return json($obj->getAll(true, true));
    }

    public function getComments($id, $page = 0, $sort = 'new_score'): \think\response\Json
    {
        $obj = new MovieComment($id, $page, 'P', $sort);
        return json($obj->getComments());
    }

    public function getReviews($id, $page = 0, $sort = 'hotest'): \think\response\Json
    {
        $obj = new MovieReview($id, $page, $sort);
        return json($obj->getReviews());
    }

    public function getCelebrities($id): \think\response\Json
    {
        return json(MovieInfo::getCelebrities($id));
    }

    public function getAllPhotos($id): \think\response\Json
    {
        return json(MovieInfo::getAllPhotos($id));
    }

    public function getPhotos($id, $page = 0, $type = 'S'): \think\response\Json
    {
        return json(MovieInfo::getPhotos($id, $page, $type));
    }

}

