
# simple ping test using os.popen

#import os

#hostname = "propertyconsultants.nz"

#cmd = 'timeout 1 ping -c 1 ' + hostname

#response = os.popen(cmd).read()

#if 'time=' in response and 'ms' in response:
#  ms = response.split('time=')[1].split(' ms')[0]
#  print(ms)

#import nmap

#nm = nmap.PortScanner()

#nm.scan('propertyconsultants.nz', '80')
#info = nm.scaninfo()

#print(info)

import socket

a_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)

location = ("propertyconsultants.nz", 80)
result_of_check = a_socket.connect_ex(location)

if result_of_check == 0:
   print("Port is open")
else:
   print("Port is not open")

a_socket.close()