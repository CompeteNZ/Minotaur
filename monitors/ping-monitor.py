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

#!/usr/bin/env python3
import os
import sys
import ping3
import socket
import errno
import datetime
import mysql.connector

from dotenv import load_dotenv, find_dotenv
load_dotenv(find_dotenv())

from ipaddress import ip_address, IPv4Address 

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
    sql = "SELECT monitor_id,monitor_type,monitor_source,monitor_port FROM monitors WHERE monitor_type=%s AND monitor_state=%s"
    val = ('ping', 1)
    cursor.execute(sql, val)
except mysql.connector.Error as err:
    print(err)
    sys.exit(1)

results = cursor.fetchall()

for (monitor_id, monitor_type, monitor_source, monitor_port) in results:

    #response = ping3.ping(monitor_source, unit='ms', timeout=10)

    # Check if the address is ip4 or ip6 otherwise treat as a hostname
    try: 
        socket_type = socket.AF_INET if type(ip_address(monitor_source)) is IPv4Address else socket.AF_INET6
    except ValueError: 
        socket_type = socket.AF_INET

    a_socket = socket.socket(socket_type, socket.SOCK_STREAM)

    location = (monitor_source, monitor_port)

    try:
        response = a_socket.connect_ex(location)
    except socket.error as err:
        response = err.errno

    a_socket.close()

    if response == 11001:
        # host unknown (e.g. domain name lookup error)
        # store result in the db as -1   
        try:
            sql = "INSERT INTO monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (%s, %s, %s, %s)"
            val = (monitor_id, monitor_type, monitor_source, -1)
            cursor.execute(sql, val)
        except mysql.connector.Error as err:
            print(err)
        continue

    if response == 0:
        # no error code
        # store result in the db as 1    
        try:
            sql = "INSERT INTO monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (%s, %s, %s, %s)"
            val = (monitor_id, monitor_type, monitor_source, 1)
            cursor.execute(sql, val)
        except mysql.connector.Error as err:
            print(err)
        continue

    else:
        # store result in the db 0
        try:
            sql = "INSERT INTO monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (%s, %s, %s, %s)"
            val = (monitor_id, monitor_type, monitor_source, 0)
            cursor.execute(sql, val)
        except mysql.connector.Error as err:
            print(err)

# commit db transaction and close conection
conn.commit()
conn.close()