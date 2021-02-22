# DESCRIPTION
# Run ping check and store the results in the db
# monitor_source = host address

# DEPENDENCIES
# Install python
# Install ping3 "python -m pip install ping3"
# Install mysql.connector "python -m pip install mysql-connector-python"
# Install dotenv "python -m pip install python-dotenv"

# HOW TO RUN
# run cmd "python <script>"
# automate on windows using a bat file with command "python <script>" see batch folder for batch files
# automate on linux using cron with command "python <script>"

# TODO

import ping3
import mysql.connector
import sys
import datetime
import os

from dotenv import load_dotenv, find_dotenv
load_dotenv(find_dotenv())

try:
    conn = mysql.connector.connect(
        user=os.getenv("DB_USERNAME"),
        password=os.getenv("DB_PASSWORD"),
        host=os.getenv("DB_HOST"),
        port=int(os.getenv("DB_PORT")),
        database=os.getenv("DB_DATABASE")
    )
except mysql.connector.Error as err:
    print(err)
    sys.exit(1)

# get db connection cursor
cursor = conn.cursor()

# get list of ping monitors from the db
try:
    sql = "SELECT monitor_id,monitor_type,monitor_source FROM monitors WHERE monitor_type=%s AND monitor_state=%s"
    val = ('ping', 1)
    cursor.execute(sql, val)
except mysql.connector.Error as err:
    print(err)
    sys.exit(1)

results = cursor.fetchall()

for (monitor_id, monitor_type, monitor_source) in results:

    response = ping3.ping(monitor_source)
    if response == False:
        # host unknown (e.g. domain name lookup error)
        # store result in the db as -1   
        try:
            sql = "INSERT INTO monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (%s, %s, %s, %s)"
            val = (monitor_id, monitor_type, monitor_source, -1)
            cursor.execute(sql, val)
        except mysql.connector.Error as err:
            print(err)
        continue

    if response == None:
        # host timed out (e.g. resolved IP address but no response)
        # store result in the db as 0    
        try:
            sql = "INSERT INTO monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (%s, %s, %s, %s)"
            val = (monitor_id, monitor_type, monitor_type, monitor_source, 0)
            cursor.execute(sql, val)
        except mysql.connector.Error as err:
            print(err)
        continue

    else:
        # response recieved in microseconds
        responseTimeMilliseconds = str(round(response * 1000))
        # store result in the db as response time in microseconds
        try:
            sql = "INSERT INTO monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (%s, %s, %s, %s)"
            val = (monitor_id, monitor_type, monitor_source, responseTimeMilliseconds)
            cursor.execute(sql, val)
        except mysql.connector.Error as err:
            print(err)

# commit db transaction and close conection
conn.commit()
conn.close()