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
use think\facade\Cache;

class Info
{
    public function getMovieInfo($id, $flag = 0): \think\response\Json
    {
        if ($flag == 1) {
            cache('info_' . $id, NULL);
        }
        if (cache('info_' . $id)) {
            return cache('info_' . $id);
        } else {
            $obj = new MovieInfo($id);
            $data = $obj->getAll($isAccurate = false, true);
            if (!empty($data['Name'])) {
                cache('info_' . $id, json($data));
            }
            return json($data);
        }
    }

    public function getComments($id, $page = 0, $sort = 'new_score'): \think\response\Json
    {
        if (cache('comments_' . $id . '_' . $page . '_' . $sort)) {
            return cache('comments_' . $id . '_' . $page . '_' . $sort);
        } else {
            $obj = new MovieComment($id, $page, 'P', $sort);
            $data = $obj->getComments();
            if (!empty($data)) {
                cache('comments_' . $id . '_' . $page . '_' . $sort, json($data));
            }
            return json($data);
        }
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

    public function play($url = ""): \think\response\Json
    {
        return json(MovieInfo::play($url));
    }
}
