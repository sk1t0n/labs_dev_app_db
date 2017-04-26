# coding: utf-8

import pymysql.cursors

connection = pymysql.connect(host='localhost',
                             user='user_lab1',
                             password='12345',
                             db='lab1',
                             charset='utf8mb4',
                             cursorclass=pymysql.cursors.DictCursor)