import ping3
import mariadb
import sys
import datetime

# INFO
# Run monitor ping checks and store results in the db
# monitor_source = host address
# to automate on windows setup a bat with command "python <script>""
# to automate on linux setup cron with command "python <script>""

# TODO

try:
    conn = mariadb.connect(
        user="root",
        password="",
        host="127.0.0.1",
        port=3306,
        database="minotaur_s"
    )
except mariadb.Error as e:
    print(f"Error connecting to MariaDB Platform: {e}")
    sys.exit(1)

# get db connection cursor
cursor = conn.cursor()

# get list of ping monitors from the db
try:
    cursor.execute(
        "SELECT monitor_id,monitor_type,monitor_source FROM monitor WHERE monitor_type=? AND monitor_state=?",
        ('ping',1))
except mariadb.Error as e:
    print(f"Error getting result set: {e}")
    sys.exit(1)

results = cursor.fetchall()

for (monitor_id, monitor_type, monitor_source) in results:

    response = ping3.ping(monitor_source)
    if response == False:
        # host unknown (e.g. domain name lookup error)
        # store result in the db as -1   
        try:
            cursor.execute(
                "INSERT INTO data_monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (?, ?, ?, ?)", 
                (monitor_id, monitor_type, monitor_source, -1))
        except mariadb.Error as e:
            print(f"Error: {e}")
        continue
    if response == None:
        # host timed out (e.g. resolved IP address but no response)
        # store result in the db as 0    
        try:
            cursor.execute(
                "INSERT INTO data_monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (?, ?, ?, ?)", 
                (monitor_id, monitor_type, monitor_type, monitor_source, 0))
        except mariadb.Error as e:
            print(f"Error: {e}")
        continue
    else:
        # response recieved in microseconds
        responseTimeMilliseconds = str(round(response * 1000))
        # store result in the db as response time in microseconds
        try:
            cursor.execute(
                "INSERT INTO data_monitor_results (monitor_id, monitor_type, monitor_source, monitor_result) VALUES (?, ?, ?, ?)", 
                (monitor_id, monitor_type, monitor_source, responseTimeMilliseconds))
        except mariadb.Error as e:
            print(f"Error: {e}")


# commit db transaction and close conection
conn.commit()
conn.close()