<?php

namespace app\movie\controller;

use DouBanLib\Photo;

class Photos
{
    public function getPhoto($id): \think\response\Json
    {
        $obj = new Photo($id);
        return json($obj->getAll());
    }
}