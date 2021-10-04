<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


return [
    '__pattern__' => [
        'id' => '\d+',
        'pid' => '\d+',
        'search_suggest' => '(.*?)',
        'name' => '(.*?)',
        'page' => '\d+',
    ],
    '[subject]' => [
        ':id$' => ['movie/info/getMovieInfo', ['method' => 'get']],
        ':id/comments/[:page]/[:sort]$' => ['movie/info/getComments', ['method' => 'get']],
        ':id/reviews/[:page]/[:sort]$' => ['movie/info/getReviews', ['method' => 'get']],
        ':id/celebrities$' => ['movie/info/getCelebrities', ['method' => 'get']],
        ':id/all_photos$' => ['movie/info/getAllPhotos', ['method' => 'get']],
        ':id/photos/[:page]/[:type]$' => ['movie/info/getPhotos', ['method' => 'get']],
    ],
    '[search]' => [
        'key/:search_suggest$' => ['movie/search/getSearchSuggest', ['method' => 'get']],
        ':name/:page$' => ['movie/search/getSearch', ['method' => 'get']],
    ],
    '[tag]' => [
        '[:tags]/[:page]/[:sort]/[:genres]/[:country]/[:year_range]$' => ['movie/tag/getTag', ['method' => 'get']],
    ],
    '[celebrity]' => [
        ':id$' => ['movie/celebrity/getCelebrityInfo', ['method' => 'get']],
        ':id/photos/[:page]/[:sort]' => ['movie/celebrity/getCelebrityPhotos', ['method' => 'get']],
        ':id/photo/[:pid]/[:page]' => ['movie/celebrity/getCelebrityPhoto', ['method' => 'get']],
        ':id/awards$' => ['movie/celebrity/getCelebrityAwards', ['method' => 'get']],
        ':id/movies/[:page]/[:sort]$' => ['movie/celebrity/getCelebrityMovies', ['method' => 'get']],
        ':id/partners/[:page]' => ['movie/celebrity/getCelebrityPartners', ['method' => 'get']],
    ],
    'photos/photo/[:id]' => ['movie/photos/getPhoto', ['method' => 'get']],
    'top250/[:page]' => ['movie/movies/getTop250', ['method' => 'get']],


];
