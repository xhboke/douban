<?php
error_reporting(0);
header("Access-Control-Allow-Origin:*");
header("Content-type: text/json; charset=utf-8");

include_once('./class/class.Movie.php');
include_once('./class/class.MovieComment.php');
include_once('./class/class.MovieItem.php');
include_once('./class/class.MovieReview.php');
include_once('./class/class.MovieSearch.php');
include_once('./class/class.MovieTag.php');
include_once('./class/class.Celebrity.php');

$_type = isset($_GET["type"]) ? $_GET["type"] : '';

switch ($_type) {
    case 'year':
        goto year;
    case 'info':
        goto info;
    case 'search':
        goto search;
    case 'search_suggest':
        goto search_suggest;
    case 'review':
        goto review;
    case 'comment':
        goto comment;
    case 'review_context':
        goto review_context;
    case 'celebrity':
        goto celebrity;
    case 'tag':
        goto tag;
    case 'top250':
        goto top250;
    case 'indexM':
        goto indexM;
    case 'indexT':
        goto indexT;
    case 'nowplaying':
        goto nowplaying;
    case 'carousel':
        goto carousel;
    default:
        goto play;
}


/***************************************************************************************************/
### 播放影片
play:
$url = isset($_GET['url']) ? $_GET['url'] : '';
if (empty($url)) {
    echo 'error';
} else {
    header('Location: https://panguapi.ntryjd.net/jiexi/?url=' . $url);
}

/***************************************************************************************************/
### 首页幻灯片
carousel:
$jsonstr = file_get_contents('./.cache/carousel.json');
echo $jsonstr;

/***************************************************************************************************/
### 根据年代返回影片
year:
$_range = isset($_GET["year_range"]) ? $_GET["year_range"] : '9';
$_year = ['1,1959', '1960,1969', '1970,1979', '1980,1989', '1990,1999', '2000,2009', '2010,2019', '2019,2019', '2020,2020', '2021,2021'];
$_page = isset($_GET["page"]) ? $_GET["page"] : 0;
$obj = new MovieTag('', $_page, 'U', '', '', $_year[$_range]);
print_r($obj->getTag());

/***************************************************************************************************/
### 返回电影信息
info:
$_id = isset($_GET["id"]) ? $_GET["id"] : '';
$_method = isset($_GET["method"]) ? $_GET["method"] : 0;
$obj = new MovieInfo($_id);
if ($obj->isId()) {
    if ($_method == 0) {
        print_r($obj->getAll(True));
    } elseif ($_method == 99) {
        print_r($obj->get_INFO());
    } else {
        print_r($obj->getAll(false, false));
    }
} else {
    print_r($obj->Json(['Check whether the ID(' . $_id . ') is correct!']));
}

/***************************************************************************************************/
### 搜索电影
search:
$_s = isset($_GET["s"]) ? $_GET["s"] : '';
$_page = isset($_GET["page"]) ? $_GET["page"] : 0;
$obj = new MovieSearch($_s, $_page);
print_r($obj->getSearchData());

/***************************************************************************************************/
### 搜索关键词建议
search_suggest:
$_name = isset($_GET["name"]) ? $_GET["name"] : exit();
print_r(MovieSearch::getSearchSuggest($_name));

/***************************************************************************************************/
### 评论
review:
$_id = isset($_GET["id"]) ? $_GET["id"] : '';
$_page = isset($_GET["page"]) ? $_GET["page"] : 0;
$_sort = isset($_GET["sort"]) ? $_GET["sort"] : 'hotest';
$obj = new MovieReviews($_id, $_page, $_sort);
if ($obj->isId()) {
    print_r($obj->getReviews());
} else {
    print_r($obj->Json(['Check whether the ID(' . $_id . ') is correct!']));
}

comment:
$_id = isset($_GET["id"]) ? $_GET["id"] : '';
$_page = isset($_GET["page"]) ? $_GET["page"] : 0;
$_status = isset($_GET["status"]) ? $_GET["status"] : 'P';
$_sort = isset($_GET["sort"]) ? $_GET["sort"] : 'new_score';
$__type = isset($_GET["_type"]) ? $_GET["_type"] : '';

$obj = new MovieComment($_id, $_page, $_status, $_sort, $__type);
if ($obj->isId()) {
    print_r($obj->getComments());
} else {
    print_r($obj->Json(['Check whether the ID(' . $_id . ') is correct!']));
}

/***************************************************************************************************/
### 评论内容
review_context:
$_id = isset($_GET["id"]) ? $_GET["id"] : '';
$obj = new MovieReviewContext($_id);
if ($obj->isId()) {
    print_r($obj->getAll());
} else {
    print_r($obj->Json(['Check whether the ID(' . $_id . ') is correct!']));
}

/***************************************************************************************************/
### 名人
celebrity:
$_id = isset($_GET["id"]) ? $_GET["id"] : '';
$obj = new Celebrity($_id);
if ($obj->isId()) {
    print_r($obj->getAll());
} else {
    print_r($obj->Json(['Check whether the ID(' . $_id . ') is correct!']));
}

/***************************************************************************************************/
### 影片标签
tag:
$_tags = isset($_GET["tags"]) ? $_GET["tags"] : '';
$_page = isset($_GET["page"]) ? $_GET["page"] : 0;
$_sort = isset($_GET["sort"]) ? $_GET["sort"] : 'U';
$_genres = isset($_GET["genres"]) ? $_GET["genres"] : '';
$_country = isset($_GET["country"]) ? $_GET["country"] : '';
$_year_range = isset($_GET["year_range"]) ? $_GET["year_range"] : '';
$obj = new MovieTag($_tags, $_page, $_sort, $_genres, $_country, $_year_range);
print_r($obj->getTag());

/***************************************************************************************************/
### 影片排行榜
top250:
$_page = isset($_GET["page"]) ? $_GET["page"] : 0;
print_r(MovieTag::getTop250($_page));

/***************************************************************************************************/
### 首页电影
indexM:
$_method = isset($_GET["method"]) ? $_GET["method"] : 0;
if ($_method == 0) {
    $_string = file_get_contents('./.cache/indexM.json');
    echo $_string;
} else {
    try {
        file_put_contents('./.cache/indexM.json', MovieTag::getIndexMovie());
        echo 'indexM,Success!';
    } catch (Exception $e) {
        echo 'indexM,Error!';
    }
}

/***************************************************************************************************/
### 首页电视剧
indexT:
$_method = isset($_GET["method"]) ? $_GET["method"] : 0;
if ($_method == 0) {
    $_string = file_get_contents('./.cache/indexT.json');
    echo $_string;
} else {
    try {
        file_put_contents('./.cache/indexT.json', MovieTag::getIndexTv());
        echo 'indexT,Success!';
    } catch (Exception $e) {
        echo 'indexT,Error!';
    }
}

/***************************************************************************************************/
### 正在热播
nowplaying:
$_method = isset($_GET["method"]) ? $_GET["method"] : 0;
if ($_method == 0) {
    $_string = file_get_contents('./.cache/nowplaying.json');
    echo $_string;
} else {
    try {
        file_put_contents('./.cache/nowplaying.json', MovieTag::NowPlaying());
        echo 'nowplaying,Success!';
    } catch (Exception $e) {
        echo 'nowplaying,Error!';
    }
}
