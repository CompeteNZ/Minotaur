# DESCRIPTION
# Run dns check and store the results in the db
# monitor_source = host address

# DEPENDENCIES
# Install python
# Install mysql.connector "python -m pip install mysql-connector-python"
# Install dotenv "python -m pip install python-dotenv"
# Install nslookup "python -m pip install nslookup"

# HOW TO RUN
# run cmd "python <script>"
# automate on windows using a bat file with command "python <script>" see batch folder for batch files
# automate on linux using cron with command "python <script>"

# TODO

import os
import sys
import datetime
import mysql.connector

from nslookup import Nslookup

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
    val = ('dns', 1)
    cursor.execute(sql, val)
except mysql.connector.Error as err:
    print(err)
    sys.exit(1)

results = cursor.fetchall()

dns_query = Nslookup()

for (monitor_id, monitor_type, monitor_source) in results:
    
    ips_record = dns_query.dns_lookup(monitor_source)
    #print(ips_record.response_full, ips_record.answer)

    if not ips_record.answer:
        # host unknown (e.g. domain name lookup error)
        # store result in the db as -1   
        try:
            sql = "INSERT INTO monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (%s, %s, %s, %s)"
            val = (monitor_id, monitor_type, monitor_source, -1)
            cursor.execute(sql, val)
        except mysql.connector.Error as err:
            print(err)
        continue

    else:
        # host found (e.g. resolved IP address)
        # store result in the db 
        try:
            sql = "INSERT INTO monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (%s, %s, %s, %s)"
            val = (monitor_id, monitor_type, monitor_source, 1)
            cursor.execute(sql, val)
        except mysql.connector.Error as err:
            print(err)
        continue

# commit db transaction and close conection
conn.commit()
conn.close()