# coding: utf-8

from flask import Flask, render_template
from connect import connection
import math

app = Flask(__name__)
cursor = connection.cursor()
app.secret_key = 'asd83212j34'

@app.route('/')
def index():
    cursor.execute('SHOW DATABASES')
    databases = cursor.fetchall()
    return render_template('index.html', databases=databases)

@app.route('/db/<dbname>')
def db(dbname):
    cursor.execute('use {0}'.format(dbname))
    cursor.execute('SHOW TABLES')
    tables = cursor.fetchall()
    key = 'Tables_in_{0}'.format(dbname)
    return render_template('db.html', dbname=dbname, tables=tables, key=key)

@app.route('/db/<dbname>/table/<tablename>')
def table(dbname, tablename):
    path = '/db/{0}/table/{1}'.format(dbname, tablename)
    cursor.execute('DESCRIBE `{0}`'.format(tablename))
    rows = cursor.fetchall()
    return render_template('table_structure.html', tablename=tablename, rows=rows, path=path)

@app.route('/db/<dbname>/table/<tablename>/rows/<int:number>')
def rows(dbname, tablename, number):
    query = 'SELECT count(*) FROM `{0}`'.format(tablename)
    cursor.execute(query)
    count_rows = cursor.fetchone()['count(*)']
    count_rows_in_page = 10
    count_pages = math.ceil(count_rows / count_rows_in_page)
    query = 'SELECT * FROM `{0}` LIMIT {1},{2}'.format(tablename, 
        (number-1) * count_rows_in_page, count_rows_in_page)
    cursor.execute(query)
    rows = cursor.fetchall()
    cursor.execute('DESCRIBE `{0}`'.format(tablename))
    rows2 = cursor.fetchall()
    fields = []
    for row in rows2:
        fields.append(row['Field'])
    return render_template('rows.html', dbname=dbname, tablename=tablename, 
        rows=rows, fields=fields, count_pages=count_pages)

if __name__ == '__main__':
    app.run(debug=True)