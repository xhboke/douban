<?php

namespace app\movie\controller;

use DouBanLib\MovieCelebrity;
use think\response\Json;

class Celebrity
{
    /**
     * 获取人物的基本信息
     * @access public
     * @param integer $id 人物ID
     * @return Json
     */
    public function getCelebrityInfo(int $id): Json
    {
        $obj = new MovieCelebrity($id);
        return json($obj->getAll());
    }

    /**
     * 获取人物的全部图片
     * @access public
     * @param integer $id 人物ID
     * @param integer $page 图片页数
     * @param string $sort 排序方式【’like‘，’size‘，’time‘】
     * @return Json
     */
    public function getCelebrityPhotos(int $id, int $page = 0, string $sort = ''): Json
    {
        return json(MovieCelebrity::getAllPhotos($id, $page, $sort));
    }

    /**
     * 获取人物的图片
     * @access public
     * @param integer $id 人物ID
     * @param integer $pid 图片ID
     * @param int $page
     * @return Json
     */
    public function getCelebrityPhoto(int $id, int $pid, int $page = 0): Json
    {
        return json(MovieCelebrity::getPhotos($id, $pid, $page));
    }

    /**
     * 获取人物奖项
     * @access public
     * @param string $id 人物ID
     * @return Json
     */
    public function getCelebrityAwards(string $id): Json
    {
        return json(MovieCelebrity::getAwards($id));
    }

    /**
     * 获取人物作品
     * @access public
     * @param string $id 人物ID
     * @param integer $page 作品页数
     * @param string $sort 排序方式【’time‘，’vote‘】
     * @return Json
     */
    public function getCelebrityMovies(string $id, int $page = 0, string $sort = 'time'): Json
    {

        return json(MovieCelebrity::getAllMovies($id, $page, $sort));
    }

    /**
     * 获取人物合作2次以上的影人
     * @access public
     * @param string $id 人物ID
     * @param integer $page 页数
     * @return Json
     */
    public function getCelebrityPartners(string $id, int $page = 0): Json
    {
        return json(MovieCelebrity::getAllPartners($id, $page));
    }


}