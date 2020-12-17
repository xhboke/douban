<?php
header("Access-Control-Allow-Origin:*");
header("Content-type: text/json; charset=utf-8");

$jsonstr = '
{
    "xs": [
        {
            "id": "34894508",
            "img":"https://puui.qpic.cn/tv/0/1224310666_1080607/0?max_age=7776000",
            "name":"【斗笑社】谁是德云一哥？郭德纲操纵“黑幕”"
        },
        {
            "id": "30313969",
            "img":"//puui.qpic.cn/tv/0/1224330258_1080607/0?max_age=7776000",
            "name":"【斗罗大陆·更新】最强辅助，战力超凡！"
        },
        {
            "id": "27195027",
            "img":"//puui.qpic.cn/tv/0/1224201947_1080607/0?max_age=7776000",
            "name":"【使徒行者3】卧底与黑道少爷的爱情终BE"
        }
    ],
    "md": [
        {
            "id": "25907124",
            "img":"//puui.qpic.cn/media_img/lena/PICcldivb_580_1680/0",
            "name":"【姜子牙】 姜子牙大战九尾妖狐"
        },
        {
            "id": "27663998",
            "img":"//puui.qpic.cn/media_img/lena/PICbtcw79_580_1680/0",
            "name":"【隐秘而伟大】李易峰金晨乱世蜕变"
        },
        {
            "id": "26357307",
            "img":"//puui.qpic.cn/media_img/lena/PIC9v8c98_580_1680/0",
            "name":"【花木兰】 刘亦菲超飒演绎巾帼女英雄"
        }
    ]
    
}';
print_r($jsonstr);