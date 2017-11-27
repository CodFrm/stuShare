#!/usr/bin/python
# -*- coding: UTF-8 -*-
from socket import socket, AF_INET, SOCK_DGRAM
import json
import os
import threading
import time
# 控制服务器ip
serverIP = ""
#  定义进出设备(eth0 内网，eth1外网)
IDEV = "tun0"
ODEV = "ens33"
#  定义总的上下带宽
UP = "1000mbit"
DOWN = "1000mbit"
#  定义每个受限制的IP上下带宽
# rate 起始带宽
UPLOAD = "1kbit"
DOWNLOAD = "1kbit"
# ceil 最大带宽
MUPLOAD = "1mbit"
MDOWNLOAD = "8mbit"


def exec_shell(command):
    print command
    os.system(command)


exec_shell("iptables -t nat -A POSTROUTING -s 10.8.0.0/24 -j MASQUERADE")
# 清除网卡原有队列规则
exec_shell("tc qdisc del dev " + ODEV + " root 2>/dev/null")
exec_shell("tc qdisc del dev " + IDEV + " root 2>/dev/null")
# 定义最顶层(根)队列规则，并指定 default 类别编号
exec_shell("tc qdisc add dev " + ODEV + " root handle 10: htb default 1")
exec_shell("tc qdisc add dev " + IDEV + " root handle 10: htb default 1")

# # 定义第一层的 10:1 类别 (上行/下行 总带宽)
exec_shell("tc class add dev " + ODEV +
           " parent 10: classid 10:1 htb rate " + UP + " ceil " + UP)
exec_shell("tc class add dev " + IDEV +
           " parent 10: classid 10:1 htb rate " + DOWN + " ceil " + DOWN)


def flowCtrl(ip, down):
    down = str(down)
    number = ip[ip.rfind(".") + 1:]
    exec_shell("tc class replace dev " + ODEV + " parent 10:1 classid 10:2" +
               number + " htb rate " + MUPLOAD + " ceil " + MUPLOAD + " prio 1")
    exec_shell("tc qdisc replace dev " + ODEV + " parent 10:2" +
               number + " handle 100" + number + ": pfifo")
    exec_shell("tc filter replace dev " + ODEV +
               " parent 10: protocol ip prio 100 handle 2" + number + " fw classid 10:2" + number)
    # tc filter add dev $IDEV parent 10: protocol ip prio 1 u32 match ip dst $INET$i/32 flowid 10:2$i
    exec_shell("tc class replace dev " + IDEV + " parent 10:1 classid 10:2" +
               number + " htb rate " + down + "mbit ceil " + down + "mbit prio 1")
    exec_shell("tc qdisc replace dev " + IDEV + " parent 10:2" +
               number + " handle 100" + number + ": pfifo")
    exec_shell("tc filter replace dev " + IDEV +
               " parent 10: protocol ip prio 100 handle 2" + number + " fw classid 10:2" + number)
    exec_shell("iptables -t mangle -A PREROUTING -s " +
               ip + " -j MARK --set-mark 2" + number)
    exec_shell("iptables -t mangle -A PREROUTING -s " + ip + " -j RETURN")
    exec_shell("iptables -t mangle -A POSTROUTING -d " +
               ip + " -j MARK --set-mark 2" + number)
    exec_shell("iptables -t mangle -A POSTROUTING -d " + ip + " -j RETURN")


cache_list={}

def ctrl():
    flow = socket(AF_INET, SOCK_DGRAM)
    flow.bind(('', 1364))
    while True:
        data, addr = flow.recvfrom(1024)
        print data
        try:
            row = json.loads(data)
            if not('type' in row):
                continue
            if row['type']=='flow':
                if not(row['ip'] in cache_list):
                    flowCtrl(row['ip'], row['width'])
                elif cache_list[row['ip']]!=row['width']:
                    flowCtrl(row['ip'], row['width'])
                    pass
                cache_list[row['ip']]=row['width']
            elif row['type']=='verify':
                
                pass

        except:
            print "json error:" + data


if __name__ == '__main__':
    thread_ctrl = threading.Thread(
        target=ctrl)
    thread_ctrl.start()
    thread_ctrl.join()
    print 'End'
