import pymssql
import numpy as np
import requests
import json

host = '127.0.0.1'  # 数据库地址
user = ''  # 数据库用户名
password = ''  # 数据库用户名密码
database = 'Douban'  # 数据库的模式
sql = "SELECT ID FROM MOVIES WHERE title IS NULL"  # 所查询更新的电影 ID 语句

conn = pymssql.connect(host=host, user=user,
                       password=password, database=database, charset="utf8")
cur = conn.cursor()
cur.execute(sql)
arr = np.array(cur.fetchall())


'''
清除 SQL 中包含的单引号所引起的插入失败
'''


def clearStr(a):
    a = a.replace("'", "''")
    return a


'''
清除 SQL 中包含的 None 所引起的插入失败
'''


def clearNone(a):
    if(a == None):
        a = '0'
    return a


'''
循环遍历 SQL 搜索结果的电影 ID 
'''
for i in arr:
    url = 'http://localhost/movie/info.php?id=' + str(int(i))  # 请求网址
    while(True):
        res = requests.get(url)
        jsonStr = json.loads(res.text)
        if(jsonStr['info']['Name']):
            break

    ID = jsonStr['info']['Id']
    Name = jsonStr['info']['Name']
    PlayDesc = clearStr(jsonStr['info']['PlayDesc'])
    DatePublished = jsonStr['info']['DatePublished']

    Genre = np.array([])
    GenreStr = jsonStr['info']['Genre']
    if(GenreStr):
        GenreArr = GenreStr.values()
        for i in GenreArr:
            Genre = np.append(Genre, i)
    Genre = ','.join(Genre)

    Rating = clearNone(jsonStr['info']['Rating'])
    PlayVotes = clearNone(jsonStr['info']['PlayVotes'])
    PlayImg = jsonStr['info']['PlayImg']
    PlayYear = clearNone(jsonStr['info']['PlayYear'])

    Actor = np.array([])
    PlayActor = jsonStr['info']['PlayActor']
    if(PlayActor):
        for i in PlayActor:
            Actor = np.append(Actor, PlayActor[i]['Name'])
    Actor = ','.join(Actor)

    Director = np.array([])
    PlayDirector = jsonStr['info']['Director']
    if(PlayDirector):
        for i in PlayDirector:
            Director = np.append(Director, PlayDirector[i]['Name'])
    Director = ','.join(Director)

    # 更新 SQL 语句
    sql = "UPDATE MOVIES SET title='{Name}',describe='{PlayDesc}',\
datepublished='{DatePublished}',genre='{Genre}',\
rating={Rating},votes={PlayVotes},\
img='{PlayImg}',years='{PlayYear}',\
actor='{Actor}',director='{Director}'\
WHERE ID = {ID}".\
        format(Name=Name, PlayDesc=PlayDesc, ID=ID,
               DatePublished=DatePublished,
               Genre=Genre, Rating=Rating,
               PlayVotes=PlayVotes, PlayImg=PlayImg,
               PlayYear=PlayYear, Actor=Actor, Director=Director)
    print(Name)
    try:
        cur.execute(sql)
        conn.commit()
    except:
        print(sql)  # 失败打印 SQL 语句
