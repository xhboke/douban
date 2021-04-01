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
if ($_type == 'year') {
    $_range = isset($_GET["year_range"]) ? $_GET["year_range"] : 9;
    $_year = ['1,1959', '1960,1969', '1970,1979', '1980,1989', '1990,1999', '2000,2009', '2010,2019', '2019,2019', '2020,2020', '2021,2021'];
    $_page = isset($_GET["page"]) ? $_GET["page"] : 0;
    $obj = new MovieTag('', $_page, 'U', '', '', $_year[$_range]);
    echo $obj->getTag();
} elseif ($_type == 'info') {
    $_id = isset($_GET["id"]) ? $_GET["id"] : '';
    $_method = isset($_GET["method"]) ? $_GET["method"] : 0;
    $obj = new MovieInfo($_id);
    if ($obj->isId()) {
        if ($_method == 0) {
            echo $obj->getAll(True);
        } elseif ($_method == 99) {
            echo $obj->get_INFO();
        } else {
            echo $obj->getAll(false, false);
        }
    } else {
        echo $obj->Json(['Check whether the ID(' . $_id . ') is correct!']);
    }
} elseif ($_type == 'search') {
    $_s = isset($_GET["s"]) ? $_GET["s"] : '';
    $_page = isset($_GET["page"]) ? $_GET["page"] : 0;
    $obj = new MovieSearch($_s, $_page);
    echo $obj->getSearchData();
} elseif ($_type == 'review') {
    $_id = isset($_GET["id"]) ? $_GET["id"] : '';
    $_page = isset($_GET["page"]) ? $_GET["page"] : 0;
    $_sort = isset($_GET["sort"]) ? $_GET["sort"] : 'hotest';
    $obj = new MovieReviews($_id, $_page, $_sort);
    if ($obj->isId()) {
        echo $obj->getReviews();
    } else {
        echo $obj->Json(['Check whether the ID(' . $_id . ') is correct!']);
    }
} elseif ($_type == 'comment') {
    $_id = isset($_GET["id"]) ? $_GET["id"] : '';
    $_page = isset($_GET["page"]) ? $_GET["page"] : 0;
    $_status = isset($_GET["status"]) ? $_GET["status"] : 'P';
    $_sort = isset($_GET["sort"]) ? $_GET["sort"] : 'new_score';
    $_type = isset($_GET["type"]) ? $_GET["type"] : '';

    $obj = new MovieComment($_id, $_page, $_status, $_sort, $_type);
    if ($obj->isId()) {
        echo $obj->getComments();
    } else {
        echo $obj->Json(['Check whether the ID(' . $_id . ') is correct!']);
    }
} elseif ($_type == 'review_context') {
    $_id = isset($_GET["id"]) ? $_GET["id"] : '';
    $obj = new MovieReviewContext($_id);
    if ($obj->isId()) {
        echo $obj->getAll();
    } else {
        echo $obj->Json(['Check whether the ID(' . $_id . ') is correct!']);
    }
} elseif ($_type == 'celebrity') {
    $_id = isset($_GET["id"]) ? $_GET["id"] : '';
    $obj = new Celebrity($_id);
    if ($obj->isId()) {
        echo $obj->getAll();
    } else {
        echo $obj->Json(['Check whether the ID(' . $_id . ') is correct!']);
    }
} elseif ($_type == 'tag') {
    $_tags = isset($_GET["tags"]) ? $_GET["tags"] : '';
    $_page = isset($_GET["page"]) ? $_GET["page"] : 0;
    $_sort = isset($_GET["sort"]) ? $_GET["sort"] : 'U';
    $_genres = isset($_GET["genres"]) ? $_GET["genres"] : '';
    $_country = isset($_GET["country"]) ? $_GET["country"] : '';
    $_year_range = isset($_GET["year_range"]) ? $_GET["year_range"] : '';
    $obj = new MovieTag($_tags, $_page, $_sort, $_genres, $_country, $_year_range);
    echo $obj->getTag();
} elseif ($_type == 'top250') {
    $_page = isset($_GET["page"]) ? $_GET["page"] : 0;
    echo MovieTag::getTop250($_page);
} elseif ($_type == 'indexM') {
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
} elseif ($_type == 'indexT') {
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
} elseif ($_type == 'nowplaying') {
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
} elseif ($_type == 'carousel') {
    $jsonstr = '
{
    "xs":[
        {
            "id":"30228394",
            "img":"//liangcang-material.alicdn.com/prod/upload/a9840124460f47d2893b4db914aa60a6.jpg?x-oss-process=image/resize,w_1125/format,webp/interlace,1",
            "name":"【觉醒年代】"
        },
        {
            "id":"27073752",
            "img":"//liangcang-material.alicdn.com/prod/upload/aff6c84d4f9649d0b3bbd45c33a17e41.jpg?x-oss-process=image/resize,w_1125/format,webp/interlace,1",
            "name":"【神奇女侠1984】"
        },
        {
            "id":"27592484",
            "img":"//liangcang-material.alicdn.com/prod/upload/fe453a9184904dcdac569898cd9046d9.jpg?x-oss-process=image/resize,w_1125/format,webp/interlace,1",
            "name":"【热气球飞行家】"
        },
        {
            "id":"1652587",
            "img":"//puui.qpic.cn/tv/0/1229644004_1080607/0?max_age=7776000",
            "name":"【阿凡达】"
        }
    ],
    "md":[
        {
            "id":"30228394",
            "img":"//liangcang-material.alicdn.com/prod/upload/95b27ac0cfb94784bb6eba930d7c183e.jpg?x-oss-process=image/resize,w_2074/interlace,1/quality,Q_80/sharpen,100",
            "name":"【觉醒年代】"
        },
        {
            "id":"35390421",
            "img":"//liangcang-material.alicdn.com/prod/upload/d767cc0e46134eeb9f230b2751b0c52e.jpg?x-oss-process=image/resize,w_2074/interlace,1/quality,Q_80/sharpen,100",
            "name":"【巨鲨之夺命鲨滩】"
        },
        {
            "id":"27605542",
            "img":"//liangcang-material.alicdn.com/prod/upload/3af5e9bbcf5c4eeb95a4bcf4f139dc6b.jpg?x-oss-process=image/resize,w_2074/interlace,1/quality,Q_80/sharpen,100",
            "name":"【司藤】"
        },
        {
            "id":"35172526",
            "img":"//liangcang-material.alicdn.com/prod/upload/753576b361b545c7b01f19bb726063f9.jpg?x-oss-process=image/resize,w_2074/interlace,1/quality,Q_80/sharpen,100",
            "name":"【同一屋檐下】"
        }
    ],
    "ad":"目前无限期测试，考虑上线只有后端IP限制成功后。"
}';
    echo $jsonstr;
} else {
    $url = isset($_GET['url']) ? $_GET['url'] : '';
    if (empty($url)) {
        echo '请选择播放链接';
    } else {
        //https://17kyun.com/api.php?url=
        header('Location: https://panguapi.ntryjd.net/jiexi/?url=' . $url);
    }
}
