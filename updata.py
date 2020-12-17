import pymssql
import numpy as np
import requests
import json

conn = pymssql.connect(host='127.0.0.1', user='', password='', database='DouBan',charset="utf8")
cur = conn.cursor()
sql = "SELECT ID FROM MOVIES WHERE title IS NULL"
cur.execute(sql)
arr = np.array(cur.fetchall())
headers = { 'User-Agent':'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36 QIHU 360SE'}

def clearStr(a):
    a = a.replace("'", "''")
    return a
def clearNone(a):
    if(a == None):
        a = '0'
    return a

for i in arr:
    url = 'http://localhost/movie/info.php?id=' + str(int(i))
    while(True):
        res = requests.get(url,headers=headers)
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
            Genre = np.append(Genre,i)
    Genre = ','.join(Genre)

    Rating = clearNone(jsonStr['info']['Rating'])
    PlayVotes = clearNone(jsonStr['info']['PlayVotes'])
    PlayImg = jsonStr['info']['PlayImg']
    PlayYear = clearNone(jsonStr['info']['PlayYear'])

    Actor = np.array([])
    PlayActor = jsonStr['info']['PlayActor']
    if(PlayActor):
        for i in PlayActor:
            Actor = np.append(Actor,PlayActor[i]['Name'])
    Actor = ','.join(Actor)

    Director = np.array([])
    PlayDirector = jsonStr['info']['Director']
    if(PlayDirector):
        for i in PlayDirector:
            Director = np.append(Director,PlayDirector[i]['Name'])
    Director = ','.join(Director)
    
    sql = "UPDATE MOVIES SET title='{Name}',describe='{PlayDesc}',\
datepublished='{DatePublished}',genre='{Genre}',\
rating={Rating},votes={PlayVotes},\
img='{PlayImg}',years='{PlayYear}',\
actor='{Actor}',director='{Director}'\
WHERE ID = {ID}".\
format(Name=Name,PlayDesc=PlayDesc,ID=ID,\
       DatePublished=DatePublished,\
       Genre=Genre,Rating=Rating,\
       PlayVotes=PlayVotes,PlayImg=PlayImg,\
       PlayYear=PlayYear,Actor=Actor,Director=Director)
    try:
        print(Name)
        cur.execute(sql)
        conn.commit()
    except:
        print(Name)
        print(sql)
            
