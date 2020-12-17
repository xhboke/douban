import requests
import pymssql
import json
import time

'''
基本信息
'''
host = '127.0.0.1'  # 数据库地址
user = ''  # 数据库用户名
password = ''  # 数据库用户名密码
database = 'Douban'  # 数据库的模式
year_range = ['1,1959', '1960,1969', '1970,1979', '1980,1989',
              '1990,1999', '2000,2009', '2010,2019', '2020,2020']  # 遍历年份数据


conn = pymssql.connect(host=host, user=user,
                       password=password, database=database, charset="utf8")
cur = conn.cursor()


'''
遍历页数中年份
'''
for _year in year_range:
    for _page in range(0, 499):
        # 一页20个一共500页，理论一页共1W个
        while(True):
            url = 'http://localhost/movie/tag.php?year_range=' + \
                _year + '&page=' + str(_page)
            res = requests.get(url)
            jsonStr = json.loads(res.text)
            if(jsonStr['status'] == 0):
                for i in jsonStr['data']:
                    ID = jsonStr['data'][i]['id']
                    sql = "INSERT INTO movies values("+str(
                        ID)+",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)"
                    try:
                        cur.execute(sql)
                        conn.commit()
                        print("[" + str(ID) + "]第" + str(_page) +
                              "页 / 第" + str(i) + "个插入SQL成功！")
                    except:
                        print(sql)
                        print("[" + str(ID) + "]第" + str(_page) +
                              "页 / 第" + str(i) + "个插入SQL失败！正在重试！！！")
                print("=====================")
                # time.sleep(1)
                break
