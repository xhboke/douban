import requests, json, pymssql, numpy as np, random, time


def curl_get(url):
    return requests.get(url).text


def clear_str(str):
    return str.replace("'", "''")


def change_arr(arr):
    return ','.join(arr)


def str_float(str):
    try:
        _return = float(str)
    except:
        _return = "''"
    return _return


def str_int(str):
    try:
        _return = int(str)
    except:
        _return = "''"
    return _return


def success_tip(id, name):
    return '[success]:' + str(id) + ' ' + name


def fail_tip(id, name):
    return '[fail]:' + str(id) + ' ' + name


def get_data(id):
    _url = 'http://127.0.0.1/douban/?type=info&method=99&id=' + str(id)
    _json = json.loads(curl_get(_url))
    return [_json['Id'], _json['Name'], _json['Image'], _json['Year'], _json['DatePublished'], _json['Genre'].values(),
            _json['Language'], _json['Rating'], _json['Votes'], _json['Description'], _json['Single_episode_length'],
            _json['Movie_length']]


def save_data(conn, id, name, image, year, datepublished, genre, languare, rating, votes, descripition,
              Single_episode_length,
              Movie_length):
    cursor = conn.cursor()
    SQL = "INSERT INTO MOVIES VALUES ({},N'{}','{}',{},'{}','{}','{}',{},{},'{}','{}','{}')"
    SQL = SQL.format(id, clear_str(name), image, str_int(year), datepublished, change_arr(genre), languare,
                     str_float(rating), str_int(votes), clear_str(descripition), Single_episode_length, Movie_length)
    try:
        cursor.execute(SQL)
        conn.commit()
        print(success_tip(id, name))
    except:
        print(fail_tip(id, name))
        f = open('./log/fail_log.txt', 'a+', encoding='utf-8')
        f.write(fail_tip(id, name) + SQL + '\n')
        f.close()
    cursor.close()


if __name__ == '__main__':
    conn = pymssql.connect(server='127.0.0.1', database="Spiders")

    for _yearRange in range(9, -1, -1):
        _URL = 'http://localhost/douban/?type=year&year_range=' + str(_yearRange) + '&page='
        _startPage = 0
        _iData = json.loads(curl_get(_URL + str(_startPage)))
        while (_iData['count'] > 0):
            _iData = json.loads(curl_get(_URL + str(_startPage)))
            for item in _iData['data']:
                _id = _iData['data'][item]['id']
                _arr = get_data(_id)
                save_data(conn, _arr[0], _arr[1], _arr[2], _arr[3], _arr[4], _arr[5], _arr[6], _arr[7], _arr[8],
                          _arr[9], _arr[10], _arr[11])
                # time.sleep(2)
            _startPage += 1

    conn.close()
