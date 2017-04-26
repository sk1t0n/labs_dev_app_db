#coding: utf-8

import pymysql.cursors
import math
from flask import Flask, render_template, request, url_for
from datetime import datetime, timedelta

conn = pymysql.connect(host='localhost',
                             user='root',
                             password='12345',
                             db='lab5',
                             charset='utf8mb4',
                             cursorclass=pymysql.cursors.DictCursor)

cursor = conn.cursor()

app = Flask(__name__)
app.secret_key = 'vdfsd232gs_+1df'

@app.route('/info')
def info():
    query = 'SELECT `name` FROM `pers` LIMIT 50'
    cursor.execute(query)
    pers = cursor.fetchall()
    query = "SELECT `id_winner` FROM `wars` WHERE `id_winner` <> '' LIMIT 50"
    cursor.execute(query)
    clans = cursor.fetchall()
    return render_template('info.html', pers=pers, clans=clans)

def task1_data_append(data, rows, idx):
    data.append((str(rows[idx]['date']), rows[idx]['health'],rows[idx]['power'], 
        rows[idx]['agility'], rows[idx]['stamina'], rows[idx]['cunning'], 
        rows[idx]['attention'], rows[idx]['charism'], rows[idx]['health0'], 
        rows[idx]['power0'], rows[idx]['agility0'], rows[idx]['stamina0'], 
        rows[idx]['cunning0'], rows[idx]['attention0'], rows[idx]['charism0']))

def get_attrs_pers(data):
    healths0 = ','.join([str(data[i][1]) for i in range(len(data))])
    powers0 = ','.join([str(data[i][2]) for i in range(len(data))])
    agilitys0 = ','.join([str(data[i][3]) for i in range(len(data))])
    staminas0 = ','.join([str(data[i][4]) for i in range(len(data))])
    cunnings0 = ','.join([str(data[i][5]) for i in range(len(data))])
    attentions0 = ','.join([str(data[i][6]) for i in range(len(data))])
    charisms0 = ','.join([str(data[i][7]) for i in range(len(data))])
    healths = ','.join([str(data[i][8]) for i in range(len(data))])
    powers = ','.join([str(data[i][9]) for i in range(len(data))])
    agilitys = ','.join([str(data[i][10]) for i in range(len(data))])
    staminas = ','.join([str(data[i][11]) for i in range(len(data))])
    cunnings = ','.join([str(data[i][12]) for i in range(len(data))])
    attentions = ','.join([str(data[i][13]) for i in range(len(data))])
    charisms = ','.join([str(data[i][14]) for i in range(len(data))])

    data = {
        'healths0': healths0,
        'powers0': powers0,
        'agilitys0': agilitys0,
        'staminas0': staminas0,
        'cunnings0': cunnings0,
        'attentions0': attentions0,
        'charisms0': charisms0,
        'healths': healths,
        'powers': powers,
        'agilitys': agilitys,
        'staminas': staminas,
        'cunnings': cunnings,
        'attentions': attentions,
        'charisms': charisms
    }

    return data

@app.route('/task1/<pers_name>')
def task1(pers_name):
    data = []
    query = """
            SELECT WEEK(`date`, 1) AS week, `date`, `health`, `power`, `agility`, `stamina`, 
                `cunning`, `attention`, `charism`, `health0`, `power0`, `agility0`, 
                `stamina0`, `cunning0`, `attention0`, `charism0` FROM `pers` WHERE `name` = '{0}'
            """.format(pers_name)
    cursor.execute(query)
    rows = cursor.fetchall()

    if rows:
        for i in range(len(rows)-1):
            if rows[i]['week'] != rows[i+1]['week']:
                task1_data_append(data, rows, i)
        if len(rows) > 1:
            if rows[-2]['week'] != rows[-1]['week']:
                task1_data_append(data, rows, -1)
        else:
            task1_data_append(data, rows, -1)

        dates = ','.join(['"'+str(data[i][0])+'"' for i in range(len(data))])
        data = get_attrs_pers(data)
        data['dates'] = dates

    return render_template('task1.html', pers_name=pers_name, data=data)

@app.route('/task2')
def task2():
    data = []
    
    query = "SELECT WEEK(`date`, 1) AS week FROM `pers` ORDER BY `week`  DESC LIMIT 1"
    cursor.execute(query)
    row = cursor.fetchone()
    if row:
        max_week = row['week']
    else:
        return render_template('task2.html', data=data)

    query = "SELECT MAX(`level`) AS max_level FROM `pers`"
    cursor.execute(query)
    max_level = cursor.fetchone()['max_level']

    query = """
            SELECT `date`,
            MAX(`health`), MAX(`power`), MAX(`agility`), MAX(`stamina`), MAX(`cunning`), 
            MAX(`attention`), MAX(`charism`), MAX(`health0`), MAX(`power0`), 
            MAX(`agility0`), MAX(`stamina0`), MAX(`cunning0`), MAX(`attention0`), MAX(`charism0`) 
            FROM 
                (SELECT WEEK(`date`, 1) AS week, `level`, `date`, `health`, `power`, 
                    `agility`, `stamina`, `cunning`, `attention`, `charism`, `health0`, 
                    `power0`, `agility0`, `stamina0`, `cunning0`, `attention0`, `charism0` 
                FROM `pers` WHERE WEEK(`date`, 1) = {0} AND `level` = {1} 
                ORDER BY `week`, `level` ASC) as q 
            GROUP BY `date` ORDER BY `date` DESC LIMIT 1
            """
    
    for num_week in range(1, 3):
        for num_level in range(1, max_level+1):
            cursor.execute(query.format(num_week, num_level))
            row_week_level = cursor.fetchone()
            if row_week_level:
                data.append((
                    str(row_week_level['date']), row_week_level['MAX(`health`)'], 
                    row_week_level['MAX(`power`)'], row_week_level['MAX(`agility`)'], 
                    row_week_level['MAX(`stamina`)'], row_week_level['MAX(`cunning`)'], 
                    row_week_level['MAX(`attention`)'], row_week_level['MAX(`charism`)'], 
                    row_week_level['MAX(`health0`)'], row_week_level['MAX(`power0`)'], 
                    row_week_level['MAX(`agility0`)'], row_week_level['MAX(`stamina0`)'], 
                    row_week_level['MAX(`cunning0`)'], row_week_level['MAX(`attention0`)'], 
                    row_week_level['MAX(`charism0`)'], num_level
                ))

    if len(data) > 0:
        dates = ','.join(['"Уровень {0} - '.format(data[i][-1]) + 
            str(data[i][0]) + '"' for i in range(len(data))])
        data = get_attrs_pers(data)
        data['dates'] = dates

    return render_template('task2.html', data=data)

def task3_data_append(data, rows, idx, sum_tugr, sum_ruda, sum_oil):
    data.append((str(rows[idx]['first_date']), str(rows[idx]['last_date']), 
                    sum_tugr, sum_ruda, sum_oil))

@app.route('/task3/<clan_name>')
def task3(clan_name):
    data = []
    query = """
            SELECT WEEK(`date`, 1) AS week, DATE(`date`) AS date, `tugr`, `ruda`, `oil`,
                DATE(DATE_ADD(`date`, INTERVAL(0-WEEKDAY(`date`)) DAY)) AS first_date, 
                DATE(DATE_ADD(`date`, INTERVAL(6-WEEKDAY(`date`)) DAY)) AS last_date
            FROM `wars` WHERE `id_winner` = '/clan/{0}/' ORDER BY first_date ASC
            """.format(clan_name)
    cursor.execute(query)
    rows = cursor.fetchall()
    
    if rows:
        sum_tugr = rows[0]['tugr']
        sum_ruda = rows[0]['ruda']
        sum_oil = rows[0]['oil']

        for i in range(len(rows)-1):
            if rows[i]['week'] == rows[i+1]['week']:
                sum_tugr += rows[i+1]['tugr']
                sum_ruda += rows[i+1]['ruda']
                sum_oil += rows[i+1]['oil']
            else:
                task3_data_append(data, rows, i, sum_tugr, sum_ruda, sum_oil)
                sum_tugr = rows[i+1]['tugr']
                sum_ruda = rows[i+1]['ruda']
                sum_oil = rows[i+1]['oil']

        if len(rows) > 1:
            if rows[-2]['week'] == rows[-1]['week']:
                task3_data_append(data, rows, -1, sum_tugr, sum_ruda, sum_oil)
            else:
                sum_tugr = rows[-1]['tugr']
                sum_ruda = rows[-1]['ruda']
                sum_oil = rows[-1]['oil']
                task3_data_append(data, rows, -1, sum_tugr, sum_ruda, sum_oil)
        else:
            task3_data_append(data, rows, -1, sum_tugr, sum_ruda, sum_oil)

        dates = ','.join(['"'+str(data[i][1])+'"' for i in range(len(data))])
        tugrs = ','.join([str(data[i][2]) for i in range(len(data))])
        ruda = ','.join([str(data[i][3]) for i in range(len(data))])
        oils = ','.join([str(data[i][4]) for i in range(len(data))])

        data = {
            'dates': dates,
            'tugrs': tugrs,
            'ruda': ruda,
            'oils': oils
        }

    return render_template('task3.html', clan_name=clan_name, data=data)

if __name__ == '__main__':
    app.run(debug=True)