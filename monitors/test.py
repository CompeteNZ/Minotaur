
# simple ping test using os.popen

import os

hostname = "notsomuchwork.nz"

cmd = 'timeout 1 ping -c 1 ' + hostname

response = os.popen(cmd).read()

if 'time=' in response and 'ms' in response:
  ms = response.split('time=')[1].split(' ms')[0]
  print(ms)