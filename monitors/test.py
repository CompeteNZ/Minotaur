
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

import sys
import socket
import errno

from ipaddress import ip_address, IPv4Address 

address = "2607:b400:92:26:0:97:1e7:39471"

# Check if the address is ip4 or ip6 otherwise treat as a hostname
try: 
   socket_type = socket.AF_INET if type(ip_address(address)) is IPv4Address else socket.AF_INET6
except ValueError: 
   socket_type = socket.AF_INET

print(socket_type)

a_socket = socket.socket(socket_type, socket.SOCK_STREAM)

location = (address, 443)

try:
   result = a_socket.connect_ex(location)
except socket.error as err:
   result = err.errno
   print(err.errno)

print(result)

if result == 11001:
   print("UNKNOWN HOST")
elif result == 0:
   print("HOST IS UP")
else:
   print("HOST IS DOWN")

a_socket.close()