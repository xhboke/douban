import requests
import pymssql
import json
import time
'''
1.连接本地数据库
'''
conn = pymssql.connect(host='127.0.0.1', user='', password='', database='DouBan',charset="utf8")
cur = conn.cursor()
headers = { 'User-Agent':'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36 QIHU 360SE'}
year_range = ['1,1959','1960,1969','1970,1979','1980,1989','1990,1999','2000,2009','2010,2019','2020,2020']

'''
2.遍历年份页数
'''
for _year in year_range:
    for _page in range(0,499):
        ### 一页20个一共500页，理论一共1W个
        while(True):
            url = 'http://localhost/movie/tag.php?year_range=' + _year + '&page=' + str(_page)
            res = requests.get(url,headers=headers)
            jsonStr = json.loads(res.text)
            if(jsonStr['status'] == 0):
                for i in jsonStr['data']:
                    ID = jsonStr['data'][i]['id']
                    sql = "INSERT INTO movies values("+str(ID)+",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)"
                    try:
                        cur.execute(sql)
                        conn.commit()
                        print("[" + str(ID) +"]第" + str(_page) + "页 / 第" + str(i) +"个插入SQL成功！")
                    except:
                        print(sql)
                        print("[" + str(ID) +"]第" + str(_page) + "页 / 第" + str(i) +"个插入SQL失败！正在重试！！！")
                print("=====================")
                #time.sleep(1)
                break







