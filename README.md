## 安装说明

* 本地测试

下载代码

~~~
git clone https://github.com/xhboke/douban.git
~~~

启动服务

~~~
php think run
~~~

然后就可以在浏览器中访问

~~~
http://localhost:8000
~~~

* 服务器

上传代码，设置 `public` 为运行目录，设置伪静态

~~~
location / {
	if (!-e $request_filename){
		rewrite  ^(.*)$  /index.php?s=$1  last;   break;
	}
}
~~~

## 注意

频繁使用会导致封禁IP，目前可通过更换IP或者使用代理或者是登录获取Cookie的方式避免。

## 使用方法

上方为豆瓣的链接，下方是API的链接，大致相同，涉及页数的有变化，最后请不要加上‘/’

### 一、电影信息

#### 电影基本信息

`https://movie.douban.com/subject/25845392/`

`http://127.0.0.1:8000/subject/25845392`

#### 电影短评

`https://movie.douban.com/subject/25845392/comments?status=P`

`http://127.0.0.1:8000/subject/25845392/comments?page=0&sort=new_score`

#### 电影短评

`https://movie.douban.com/subject/25845392/reviews?&start=0&sort=hotest`

`http://127.0.0.1:8000/subject/25845392/reviews?page=0&sort=hotest`

#### 电影参演人员

`https://movie.douban.com/subject/25845392/celebrities`

`http://127.0.0.1:8000/subject/25845392/celebrities`

#### 影片图片

`https://movie.douban.com/subject/25845392/all_photos`

`http://127.0.0.1:8000/subject/25845392/all_photos`

#### 影片图片

`https://movie.douban.com/subject/25845392/photos?type=S`

`http://127.0.0.1:8000/subject/25845392/photos?type=S`

### 二、影片搜索

#### 关键词

`http://127.0.0.1:8000/search/key/长津湖`

#### 搜索影片

`https://search.douban.com/movie/subject_search?search_text=长&cat=1002&start=0`

`http://127.0.0.1:8000/search/长/0`

### 三、分类影片

#### 分类影片

`http://127.0.0.1:8000/tag?tags=电影,剧情,中国大陆,2021&page=0&sort=U`

## 四、人物信息

#### 人物基本信息

`https://movie.douban.com/celebrity/1023040`

`http://127.0.0.1:8000/celebrity/1023040`

#### 人物图片

`https://movie.douban.com/celebrity/1023040/photos/?type=C&start=0&sortby=like&size=a&subtype=a`

`http://127.0.0.1:8000/celebrity/1023040/photos?page=0&sort=like`

#### 单张图片

`https://movie.douban.com/celebrity/1023040/photo/1247932516`

`http://127.0.0.1:8000/celebrity/1023040/photo/1247932516`

#### 人物获奖

`https://movie.douban.com/celebrity/1023040/awards`

`http://127.0.0.1:8000/celebrity/1023040/awards`

#### 人物参演

`https://movie.douban.com/celebrity/1023040/movies?start=0&format=pic&sortby=time`

`http://127.0.0.1:8000/celebrity/1023040/movies?page=0&sort=time`

#### 人物合作

`https://movie.douban.com/celebrity/1023040/movies?start=0&format=pic&sortby=time`

`http://127.0.0.1:8000/celebrity/1023040/partners?page=0`

### 五、图片信息

#### 图片基本信息及评论

`https://movie.douban.com/photos/photo/2673813691/`

`http://127.0.0.1:8000/photos/photo/2673813691`

### 六、豆瓣250

`https://movie.douban.com/top250?start=25&filter=`

`http://127.0.0.1:8000/top250?page=0`


## 声明

本项目仅供学习交流，禁止用于非法用途。