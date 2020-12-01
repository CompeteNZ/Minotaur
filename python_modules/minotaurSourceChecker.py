import mariadb
import sys
import numpy as np

# INFO
# This module checks for monitors with incorrect monitor_source i.e. result is host not found
# monitor_source = host address
# to automate on windows setup a bat with command "python <script>""
# to automate on linux setup cron with command "python <script>""

# TODO:

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

# get list of ping monitors from the db with -1 state result
try:
    cursor.execute(
        "SELECT monitor_id,updated_at FROM monitors WHERE monitor_type=? AND monitor_state=?",
        ('ping',1))
except mariadb.Error as e:
    print(f"Error getting result set: {e}")
    sys.exit(1)

monitors = cursor.fetchall()

for (monitor_id,updated_at) in monitors:

    # get the last 3 results for each monitor from the db which are newer than the monitors last updated date
    try:
        cursor.execute(
            "SELECT monitor_id,monitor_result FROM monitor_results WHERE monitor_id=? AND created_at>? ORDER BY id DESC LIMIT 3",
            (monitor_id,updated_at))
    except mariadb.Error as e:
        print(f"Error getting result set: {e}")
        sys.exit(1)

    results = cursor.fetchall()

    # count the results if there is less than three then skip (we want 3 consecutive results to test for a bad source)
    resultCount = len(results)

    if resultCount < 3:
        continue

    sumOfResultsList=[]
    for(monitor_id,monitor_result) in results:
        
        if monitor_result == -1:
            sumOfResultsList.append(0)
        else:
            sumOfResultsList.append(1)

    sumOfResults = np.sum(sumOfResultsList)

    # Check if the last consecutive results were all false
    if sumOfResults == False:

        # update monitor in the db
        try:
            cursor.execute(
                "UPDATE monitors SET monitor_state=? WHERE monitor_id=?", 
                (-1, monitor_id))
        except mariadb.Error as e:
            print(f"Error: {e}")

# commit db transaction and close conection
conn.commit()
conn.close()