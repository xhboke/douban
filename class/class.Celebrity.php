<?php

/** 
 * 获取名人相关信息 
 * 
 * 根据编号获取名人相关信息
 * @author      xhboke 
 * @version     1.0
 */

include_once('class.Movie.php');
class Celebrity extends Movie
{
    public $id;
    public $url;
    public $data;
    public $Name;
    public $Image;
    public $Sex;
    public $Constellation;
    public $Birthday;
    public $BirthPlace;
    public $Profession;
    public $Othername;
    public $Familymember;
    public $Website;
    public $Introduction;

    public $Some_Images;
    public $Recent_Movies;
    public $All;
    public function __construct($id)
    {
        $this->id = $id;
        $this->url = parent::MovieRootUrl . '/celebrity/' . $id . '/';
        $this->data = $this->curl_get($this->url);
    }

    public function getAll()
    {
        $this->All['id'] = $this->id;
        $this->All['name'] = $this->getName();
        $this->All['image'] = $this->getImage();
        $this->All['sex'] = $this->getSex();
        $this->All['constellation'] = $this->getConstellation();
        $this->All['birthday'] = $this->getBirthday();
        $this->All['birthplace'] = $this->getBirthPlace();
        $this->All['profession'] = $this->getProfession();
        $this->All['othername'] = $this->getOthername();
        $this->All['familymember'] = $this->getFamilymember();
        $this->All['website'] = $this->getWebsite();
        $this->All['introduction'] = $this->getIntroduction();
        $this->All['some_images'] = $this->get_Some_Images();
        $this->All['recent_movies'] = $this->get_Recent_Movies();
        return $this->Json($this->All);
    }
    public function getName()
    {
        $this->Name = trim($this->preg('#<h1>([\s\S]*?)<\/h1>#', $this->data, 1)[0]);
        return $this->Name;
    }
    public function getImage()
    {
        $this->Image = trim($this->preg('#<img class="media" src="([\s\S]*?)" \/>#', $this->data, 1)[0]);
        return $this->Image;
    }
    public function getSex()
    {
        $this->Sex = trim($this->preg('#<span>性别<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]);
        return $this->Sex;
    }
    public function getConstellation()
    {
        $this->Constellation = trim($this->preg('#<span>星座<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]);
        return $this->Constellation;
    }
    public function getBirthday()
    {
        $this->Birthday = trim($this->preg('#<span>出生日期<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]);
        return $this->Birthday;
    }
    public function getBirthPlace()
    {
        $this->BirthPlace = trim($this->preg('#<span>出生地<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]);
        return $this->BirthPlace;
    }
    public function getProfession()
    {
        $this->Profession = trim($this->preg('#<span>职业<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]);
        return $this->Profession;
    }
    public function getOthername()
    {
        $this->Othername = trim($this->preg('#<span>更多外文名<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]);
        return $this->Othername;
    }
    public function getFamilymember()
    {
        $this->Familymember = trim(preg_replace("/<a[^>]*>(.*?)<\/a>/is", "$1", $this->preg('#<span>家庭成员<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]));
        return $this->Familymember;
    }
    public function getWebsite()
    {
        $this->Website = trim($this->preg('#<span>官方网站</span>:([\s\S]*?)<a href="([\s\S]*?)" target="_blank">#', $this->data, 2)[0]);
        return $this->Website;
    }
    public function getIntroduction()
    {
        if (strpos($this->data, '<span class="all hidden">') !== false) {
            $this->Introduction = trim($this->preg('#<span class="all hidden">([\s\S]*?)<\/span>#', $this->data, 1)[0]);
        } else {
            $this->Introduction =  trim($this->preg('#影人简介([\s\S]*?)<div class="bd">([\s\S]*?)<\/div>#', $this->data, 2)[0]);
        }
        return $this->Introduction;
    }
    public function get_Some_Images()
    {
        $this->Some_Images = $this->preg('#<img src="([\s\S]*?)">#', $this->getSubstr($this->data, '<div id="photos" class="mod">', '</ul>'), 1);
        return $this->Some_Images;
    }
    public function get_Recent_Movies()
    {
        $_data = $this->preg('#<div class="pic">([\s\S]*?)<a href="https:\/\/movie.douban.com\/subject\/([\s\S]*?)\/">([\s\S]*?)<img class=""([\s\S]*?)alt="([\s\S]*?)"([\s\S]*?)src="([\s\S]*?)"([\s\S]*?)title="([\s\S]*?)"\/>([\s\S]*?)<\/div>#', $this->getSubstr($this->data, '<div id="recent_movies" class="mod">', '</ul>'), 0);
        try {
            $_count = count($_data['2']);
            for ($i = 0; $i < $_count; $i++) {
                $this->Recent_Movies[$i]['id'] = $_data[2][$i];
                $this->Recent_Movies[$i]['image'] = $_data[7][$i];
                $this->Recent_Movies[$i]['title'] = $_data[5][$i];
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $this->Recent_Movies;
    }
}

/** 
 * 获取名人图片 
 * 
 * 根据编号获取名人图片
 * @author      xhboke 
 * @version     1.0
 */

class Celebrity_Image extends Celebrity
{
    public $id;
    public $url;
    public $data;
    public function __construct($id)
    {
        parent::__construct($id);
        $this->id = $id;
        $this->url = parent::MovieRootUrl . '/celebrity/' . $this->id . '/photos/';
        $this->data = $this->getSubstr($this->curl_get($this->url), '<ul class="poster-co', '</ul>');
    }
};

class Celebrity_Movie extends Celebrity
{
};

class Celebrity_Partner extends Celebrity
{
};
