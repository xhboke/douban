<?php

/**
 * 获取人物相关信息
 * /celebrity...
 *
 * @author      xhboke
 * @version     1.0
 */

namespace DouBanLib;

include_once('Movie.php');

class MovieCelebrity extends Movie
{
    public $id;
    public $url;
    public $data;
    public $Name, $ChineseName;
    public $Image;
    public $Sex;
    public $Constellation;
    public $Birthday;
    public $BirthPlace;
    public $Profession;
    public $OtherName;
    public $FamilyMember;
    public $Website;
    public $Introduction;

    public $SomeImages;
    public $RecentMovies, $Award, $Partners;
    public $All;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->url = parent::MovieRootUrl . '/celebrity/' . $id . '/';
        $this->data = $this->curl_get($this->url);
    }

    public function getAll()
    {
        $this->All['id'] = $this->id;
        $this->All['name'] = $this->getName();
        $this->All['chineseName'] = $this->getChineseName();
        $this->All['image'] = $this->getImage();
        $this->All['sex'] = $this->getSex();
        $this->All['constellation'] = $this->getConstellation();
        $this->All['birthday'] = $this->getBirthday();
        $this->All['birthplace'] = $this->getBirthPlace();
        $this->All['profession'] = $this->getProfession();
        $this->All['otherName'] = $this->getOtherName();
        $this->All['familyMember'] = $this->getFamilymember();
        $this->All['website'] = $this->getWebsite();
        $this->All['introduction'] = $this->getIntroduction();
        $this->All['images'] = $this->getSomeImages();
        $this->All['award'] = $this->getAward();
        $this->All['recent_movies'] = $this->getRecentMovies();
        $this->All['partners'] = $this->getPartners();
        return $this->All;
    }

    public function getName(): string
    {
        $this->Name = trim($this->preg('#<h1>([\s\S]*?)<\/h1>#', $this->data, 1)[0]);
        return $this->Name;
    }

    public function getChineseName(): string
    {
        $this->ChineseName = trim($this->preg('#<div class="nbg" title="([\s\S]*?)">#', $this->data, 1)[0]);
        return $this->ChineseName;
    }

    public function getImage(): string
    {
        $this->Image = trim($this->preg('#<meta property="og:image" content="([\s\S]*?)" \/>#', $this->data, 1)[0]);
        return $this->Image;
    }

    public function getSex(): string
    {
        $this->Sex = trim($this->preg('#<span>性别<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]);
        return $this->Sex;
    }

    public function getConstellation(): string
    {
        $this->Constellation = trim($this->preg('#<span>星座<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]);
        return $this->Constellation;
    }

    public function getBirthday(): string
    {
        $this->Birthday = trim($this->preg('#<span>出生日期<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]);
        return $this->Birthday;
    }

    public function getBirthPlace(): string
    {
        $this->BirthPlace = trim($this->preg('#<span>出生地<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]);
        return $this->BirthPlace;
    }

    public function getProfession(): string
    {
        $this->Profession = str_replace(" ", "", trim($this->preg('#<span>职业<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]));
        return $this->Profession;
    }

    public function getOtherName(): string
    {
        $this->OtherName = str_replace(" ", "", trim($this->preg('#<span>更多外文名<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0]));
        return $this->OtherName;
    }

    public function getFamilyMember(): string
    {
        $this->FamilyMember = str_replace(" ", "", trim(preg_replace("/<a[^>]*>(.*?)<\/a>/is", "$1", $this->preg('#<span>家庭成员<\/span>:([\s\S]*?)<\/li>#', $this->data, 1)[0])));
        return $this->FamilyMember;
    }

    public function getWebsite(): string
    {
        $this->Website = trim($this->preg('#<span>官方网站</span>:([\s\S]*?)<a href="([\s\S]*?)" target="_blank">#', $this->data, 2)[0]);
        return $this->Website;
    }

    public function getIntroduction(): string
    {
        if (strpos($this->data, '<span class="all hidden">') !== false) {
            $this->Introduction = trim($this->preg('#<span class="all hidden">([\s\S]*?)<\/span>#', $this->data, 1)[0]);
        } else {
            $this->Introduction = trim($this->preg('#影人简介([\s\S]*?)<div class="bd">([\s\S]*?)<\/div>#', $this->data, 2)[0]);
        }
        return $this->Introduction;
    }

    public function getSomeImages(): array
    {
        $this->SomeImages = $this->preg('#<img src="([\s\S]*?)">#', $this->getSubstr($this->data, '<div id="photos" class="mod">', '</ul>'), 1);
        return $this->SomeImages;
    }

    /**
     * 最近影片
     * /celebrity
     *
     * @access public
     * @return array
     */
    public function getRecentMovies(): array
    {
        $_data = $this->preg('#<div class="pic">([\s\S]*?)<a href="https:\/\/movie.douban.com\/subject\/([\s\S]*?)\/">([\s\S]*?)<img class=""([\s\S]*?)alt="([\s\S]*?)"([\s\S]*?)src="([\s\S]*?)"([\s\S]*?)title="([\s\S]*?)"\/>([\s\S]*?)<\/div>#', $this->getSubstr($this->data, '<div id="recent_movies" class="mod">', '</ul>'), 0);
        try {
            $_count = count($_data['2']);
            for ($i = 0; $i < $_count; $i++) {
                $this->RecentMovies[$i]['id'] = $_data[2][$i];
                $this->RecentMovies[$i]['image'] = $_data[7][$i];
                $this->RecentMovies[$i]['title'] = $_data[5][$i];
            }
        } catch (\Throwable $th) {
            $this->RecentMovies = [];
        }
        return $this->RecentMovies;
    }

    /**
     * 获取人物的获奖
     * /celebrity
     *
     * @access public
     * @return array
     */
    public function getAward(): array
    {
        try {
            $_data = $this->preg('#<ul class="award">([\s\S]*?)</ul>#', $this->data, 1);
            $_count = count($_data);
            for ($i = 0; $i < $_count; $i++) {
                $item = $this->preg('#<li class="">([\s\S]*?)</li>([\s\S]*?)<li class="">([\s\S]*?)<a href="([\s\S]*?)">([\s\S]*?)<\/a>([\s\S]*?)<\/li>([\s\S]*?)<li class="">([\s\S]*?)<\/li>([\s\S]*?)<li class="">([\s\S]*?)<\/li>#', $_data[$i], 0);
                $this->Award[$i]['year'] = $item[1][0];
                $this->Award[$i]['awards_url'] = $item[4][0];
                $this->Award[$i]['awards_name'] = $item[5][0];
                $this->Award[$i]['award'] = $item[8][0];
                if (empty($item[10][0])) {
                    $this->Award[$i]['movie_id'] = "";
                    $this->Award[$i]['movie_name'] = "";
                } else {
                    $this->Award[$i]['movie_id'] = $this->preg('#<a href=\'https:\/\/movie.douban.com\/subject\/([\s\S]*?)\/\' target=\'_blank\'>([\s\S]*?)<\/a>#', $item[10][0], 1)[0];
                    $this->Award[$i]['movie_name'] = $this->preg('#<a href=\'https:\/\/movie.douban.com\/subject\/([\s\S]*?)\/\' target=\'_blank\'>([\s\S]*?)<\/a>#', $item[10][0], 2)[0];
                }
            }
        } catch (\Throwable $th) {
            $this->Award = [];
        }
        return $this->Award;
    }

    /**
     * 获取人物的Partners
     * /celebrity
     *
     * @access public
     * @return array
     */
    public function getPartners(): array
    {
        try {
            $_data = $this->preg('#<img alt="([\s\S]*?)"([\s\S]*?)src="([\s\S]*?)"([\s\S]*?)合作作品([\s\S]*?)\#([\s\S]*?)">([\s\S]*?)<\/a>\)#', $this->getSubstr($this->data, '<div id="partners" class="mod">', '</ul>'), 0);
            $_count = count($_data[1]);
            $_return = [];
            for ($i = 0; $i < $_count; $i++) {
                $_return[$i]['id'] = $_data[6][$i];
                $_return[$i]['name'] = $_data[1][$i];
                $_return[$i]['image'] = $_data[3][$i];
                $_return[$i]['count'] = $_data[7][$i];
            }
            $this->Partners = $_return;
        } catch (\Throwable $th) {
            $this->Partners = [];
        }
        return $this->Partners;
    }

    /**
     * 获取人物所有图片
     * /celebrity/:id/photos/
     *
     * @static
     * @access public
     * @param int $id
     * @param int $page
     * @param string $sort size\like\time
     * @return array
     */
    static public function getAllPhotos(int $id, int $page = 0, string $sort = 'like'): array
    {
        try {
            $_data = parent::curl_get(parent::MovieRootUrl . '/celebrity/' . $id . '/photos/?start=' . $page * 30 . '&sortby=' . $sort);
            $_item = parent::preg('#<li>([\s\S]*?)<\/li>#', parent::getSubstr($_data, ' <div id="content">', '</ul>'), 1);
            $_count = count($_item);
            $_return = [];
            for ($i = 0; $i < $_count; $i++) {
                $_return[$i]['id'] = parent::preg('#photo\/([\s\S]*?)\/" class="">#', $_item[$i], 1)[0];
                $_return[$i]['image'] = parent::preg('#<img src="([\s\S]*?)"#', $_item[$i], 1)[0];
                $_return[$i]['size'] = trim(parent::preg('#<div class="prop">([\s\S]*?)<\/div>#', $_item[$i], 1)[0]);
                $_return[$i]['name'] = trim(preg_replace('#<a href="([\s\S]*?)">([\s\S]*?)<\/a>#', '', parent::preg('#<div class="name">([\s\S]*?)<\/div>#', $_item[$i], 1)[0]));
                $_return[$i]['count'] = parent::preg('#<a href="([\s\S]*?)comments">([\s\S]*?)<\/a>#', $_item[$i], 2)[0];
            }
            return $_return;
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * 获取人物图片及相关评论
     * /celebrity/:id/photo/:pid/
     *
     * @static
     * @access public
     * @param int $id 人物ID
     * @param int $pid 图片ID
     * @param int $page 页数
     * @return array
     */
    static public function getPhotos(int $id, int $pid, int $page = 0): array
    {
        try {
            $_data = parent::curl_get(parent::MovieRootUrl . '/celebrity/' . $id . '/photo/' . $pid . '/?start=' . $page * 100);
            $_return = [];
            $_return['id'] = $id;
            $_return['pid'] = $pid;
            $_return['image'] = parent::preg('#<div class="photo-show">([\s\S]*?)<img src="([\s\S]*?)" \/>#', $_data, 2)[0];
            $_return['desc'] = strip_tags(trim(parent::preg('#<div class="photo_descri">([\s\S]*?)<\/div>#', $_data, 1)[0]));
            $_return['rec_num'] = parent::preg('#<span class="rec-num">([\s\S]*?)<\/span>#', $_data, 1)[0];
            $_return['fav_num'] = parent::preg('#<span class="fav-num"([\s\S]*?)<a href="\#">([\s\S]*?)<\/a>#', $_data, 2)[0];
            $_item = parent::preg('#<div class="comment-item"([\s\S]*?)<div class="group_banned">#', $_data, 1);
            $_count = count($_item);
            for ($i = 0; $i < $_count; $i++) {
                $_return['comment'][$i]['id'] = parent::preg('#id=([\s\S]*?) data-cid#', $_item[$i], 1)[0];
                $_return['comment'][$i]['name'] = parent::preg('#alt="([\s\S]*?)"\/>#', $_item[$i], 1)[0];
                $_return['comment'][$i]['avatar'] = parent::preg('#src="([\s\S]*?)"#', $_item[$i], 1)[0];
                $_return['comment'][$i]['time'] = parent::preg('#<span class="">([\s\S]*?)<\/span>#', $_item[$i], 1)[0];
                $_return['comment'][$i]['content'] = parent::preg('#<p class="">([\s\S]*?)<\/p>#', $_item[$i], 1)[0];
                $_return['comment'][$i]['quote'] = parent::preg('#<span class="all">([\s\S]*?)<\/span>#', $_item[$i], 1)[0];
            }
            return $_return;
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * 获取人物全部获奖
     * /celebrity/:id/awards/
     *
     * @static
     * @access public
     * @param int $id 人物ID
     * @return array
     */
    static public function getAwards(int $id): array
    {
        try {
            $_data = parent::curl_get(parent::MovieRootUrl . '/celebrity/' . $id . '/awards/');
            $_item = parent::preg('#<div class="awards">([\s\S]*?)<div class="hd">([\s\S]*?)</div>([\s\S]*?)<\/div>#', $_data, 0)[0];
            $_count = count($_item);
            $_return = [];
            for ($i = 0; $i < $_count; $i++) {
                $year = parent::getSubstr($_item[$i], '<h2>', '</h2>');
                $award = parent::preg('#<ul class="award">([\s\S]*?)<li>([\s\S]*?)<a href="([\s\S]*?)">([\s\S]*?)<\/a>([\s\S]*?)<li>([\s\S]*?)<\/li>([\s\S]*?)<li>([\s\S]*?)<\/li>#', $_item[$i], 0);
                $_icount = count($award[0]);
                for ($j = 0; $j < $_icount; $j++) {
                    $_return[$i][$j]['year'] = $year;
                    $_return[$i][$j]['name'] = $award[4][$j];
                    $_return[$i][$j]['url'] = $award[3][$j];
                    $_return[$i][$j]['award'] = $award[3][$j];
                    $_return[$i][$j]['movie'] = array('id' => parent::preg('#subject\/([\s\S]*?)\/#', $award[8][$j], 1)[0], 'name' => parent::preg('#>([\s\S]*?)<\/a>#', $award[8][$j], 1)[0]);
                }
            }
            return $_return;
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * 获取人物全部影片
     * /celebrity/:id/movies
     *
     * @static
     * @access public
     * @param int $id 人物ID
     * @param int $page 页数
     * @param string $sort 排序
     * @return array
     */
    static public function getAllMovies(int $id, int $page = 0, string $sort = 'time'): array
    {
        try {
            $_data = parent::curl_get(parent::MovieRootUrl . '/celebrity/' . $id . '/movies?start=' . $page * 10 . '&format=pic&sortby=' . $sort);
            $_return = [];
            $_return['id'] = $id;
            $_return['count'] = parent::preg('#<h1>([\s\S]*?)（([\s\S]*?)）<\/h1>#', $_data, 2)[0];
            $_item = parent::preg('#<a class="nbg" href="([\s\S]*?)<img src="([\s\S]*?)" alt="([\s\S]*?)" title="([\s\S]*?)subject\/([\s\S]*?)\/"#', $_data, 0);
            $_count = count($_item[0]);

            for ($i = 0; $i < $_count; $i++) {
                $_return['data'][$i]['id'] = $_item[5][$i];
                $_return['data'][$i]['name'] = $_item[3][$i];
                $_return['data'][$i]['image'] = $_item[2][$i];
            }
            return $_return;
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * 获取人物全部partners
     * /celebrity/:id/partners
     *
     * @static
     * @access public
     * @param int $id 人物ID
     * @param int $page 页数
     * @return array
     */
    static public function getAllPartners(int $id, int $page = 0): array
    {
        try {
            $_data = parent::curl_get(parent::MovieRootUrl . '/celebrity/' . $id . '/partners?start=' . $page * 10);
            $_item = parent::preg('#<div class="partners item" id="([\s\S]*?)">([\s\S]*?)<\/ul>#', $_data, 0)[0];
            $_count = count($_item);
            $_return = [];
            for ($i = 0; $i < $_count; $i++) {
                $_return[$i]['id'] = parent::preg('#<div class="partners item" id="([\s\S]*?)">#', $_item[$i], 1)[0];
                $_return[$i]['pid'] = parent::preg('#celebrity\/([\s\S]*?)\/">#', $_item[$i], 1)[0];
                $_return[$i]['num'] = parent::preg('#<li>合作作品\(([\s\S]*?)\)#', $_item[$i], 1)[0];
                $_return[$i]['col_num'] = parent::preg('#<li class="mt10">([\s\S]*?)<\/li>#', $_item[$i], 1)[0];
                $_return[$i]['name'] = parent::preg('#<img alt="([\s\S]*?)" src="([\s\S]*?)" class=#', $_item[$i], 1)[0];
                $_return[$i]['image'] = parent::preg('#<img alt="([\s\S]*?)" src="([\s\S]*?)" class=#', $_item[$i], 2)[0];
                $_return[$i]['role'] = parent::preg('#<li class="">([\s\S]*?)<\/li>#', $_item[$i], 1)[0];
                $works = parent::preg('#subject\/([\s\S]*?)\/">([\s\S]*?)<\/a>#', $_item[$i], 0);
                $count_works = count($works[1]);
                for ($j = 0; $j < $count_works; $j++) {
                    $_return[$i]['works'][$j]['mid'] = $works[1][$j];
                    $_return[$i]['works'][$j]['mname'] = $works[2][$j];
                }
            }
            return $_return;
        } catch (\Throwable $th) {
            return [];
        }
    }
}
