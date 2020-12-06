# 一个关于豆瓣的爬虫文件
这只是一个简单的豆瓣爬虫，尽管数据库表结构不符合范式要求，数据插入的审核并不严密，但是一切从简。
## Douban.php
* 电影搜索
* 豆瓣 Top250
* 分类 Tags、year_range、Sort methods
* 电影基本信息、播放链接、剧照以及短评
## Table construct
```SQL
CREATE TABLE Movies
(
id int primary key,
title varchar(255),
describe varchar(8000),
datepublished varchar(255),
genre varchar(255),
rating int,
votes int,
img varchar(255),
years varchar(255),
actor varchar(4028),
director varchar(4028),
)
```
## getid.py
根据年份，分为 8 个 year_range，将对应的影片 ID 存入数据库
## updata.py
将对存入的 ID 进行更新数据
## How to use it?
1. 确保 Python 已安装 pymssql、numpy、requests、json
2. 确保已安装 Microsoft SQL 
3. 确保有 PHP 运行环境
4. 创建一个SQL用户，并给予相应权限（非必须），并配置到 getid.py、updata.py
## 反反爬
目前似乎只有通过不断更换请求 IP 
