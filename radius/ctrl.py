#!/usr/bin/python
# -*- coding: UTF-8 -*-
from socket import socket, AF_INET, SOCK_DGRAM
import json
import os
import threading
import time
import urllib2
import urllib
#  openvpn-status 文件路径
OVSPATH = '/etc/openvpn/openvpn-status.log'
U = '201617370142'
P = ''
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
    return os.system(command)


exec_shell("iptables -t nat -A POSTROUTING -s 10.8.0.0/24 -j MASQUERADE")
# 清除网卡原有队列规则
exec_shell("tc qdisc del dev " + ODEV + " root 2>/dev/null")
exec_shell("tc qdisc del dev " + IDEV + " root 2>/dev/null")
# 定义最顶层(根)队列规则，并指定 default 类别编号
exec_shell("tc qdisc add dev " + ODEV + " root handle 10: htb default 1")
exec_shell("tc qdisc add dev " + IDEV + " root handle 10: htb default 1")

# # 定义第一层的 10:1 类别 (上行/下行 总带宽)
exec_shell("tc class add dev " + ODEV + " parent 10: classid 10:1 htb rate " +
           UP + " ceil " + UP)
exec_shell("tc class add dev " + IDEV + " parent 10: classid 10:1 htb rate " +
           DOWN + " ceil " + DOWN)


def flowCtrl(ip, down):
    down = str(down)
    number = ip[ip.rfind(".") + 1:]
    exec_shell(
        "tc class replace dev " + ODEV + " parent 10:1 classid 10:2" + number +
        " htb rate " + MUPLOAD + " ceil " + MUPLOAD + " prio 1")
    exec_shell("tc qdisc replace dev " + ODEV + " parent 10:2" + number +
               " handle 100" + number + ": pfifo")
    exec_shell("tc filter replace dev " + ODEV +
               " parent 10: protocol ip prio 100 handle 2" + number +
               " fw classid 10:2" + number)
    # tc filter add dev $IDEV parent 10: protocol ip prio 1 u32 match ip dst $INET$i/32 flowid 10:2$i
    exec_shell(
        "tc class replace dev " + IDEV + " parent 10:1 classid 10:2" + number +
        " htb rate " + down + "mbit ceil " + down + "mbit prio 1")
    exec_shell("tc qdisc replace dev " + IDEV + " parent 10:2" + number +
               " handle 100" + number + ": pfifo")
    exec_shell("tc filter replace dev " + IDEV +
               " parent 10: protocol ip prio 100 handle 2" + number +
               " fw classid 10:2" + number)
    exec_shell("iptables -t mangle -A PREROUTING -s " + ip +
               " -j MARK --set-mark 2" + number)
    exec_shell("iptables -t mangle -A PREROUTING -s " + ip + " -j RETURN")
    exec_shell("iptables -t mangle -A POSTROUTING -d " + ip +
               " -j MARK --set-mark 2" + number)
    exec_shell("iptables -t mangle -A POSTROUTING -d " + ip + " -j RETURN")


def verifyIpOnline(ip):
    try:
        file_object = open(OVSPATH)
        all_the_text = file_object.read()
        file_object.close()
        if all_the_text.find(ip) >= 0:
            return True
        return False
    except:
        print 'error'

    return False


print 'connect:' + str(verifyIpOnline('127.0.0.1'))

cache_list = {}


def ctrl():
    flow = socket(AF_INET, SOCK_DGRAM)
    flow.bind(('', 1364))
    while True:
        data, addr = flow.recvfrom(1024)
        print data
        try:
            row = json.loads(data)
            if not ('type' in row):
                continue
            if row['type'] == 'flow':
                if not (row['ip'] in cache_list):
                    flowCtrl(row['ip'], row['width'])
                elif cache_list[row['ip']] != row['width']:
                    flowCtrl(row['ip'], row['width'])
                    pass
                cache_list[row['ip']] = row['width']
            elif row['type'] == 'verify':
                if verifyIpOnline(row['ip']):
                    row['online'] = 'true'
                else:
                    row['online'] = 'false'
                    pass
                flow.sendto(json.dumps(row), addr)
                pass
        except Exception, e:
            print "json error:" + data
            print "error"


class MyHTTPErrorProcessor(urllib2.HTTPErrorProcessor):

    def http_response(self, request, response):
        code, msg, hdrs = response.code, response.msg, response.info()

        # only add this line to stop 302 redirection.
        if code == 302:
            return response

        if not (200 <= code < 300):
            response = self.parent.error(
                'http', request, response, code, msg, hdrs)
        return response

    https_response = http_response


def monitor():
    while True:
        try:
            url = "http://www.icodef.com"
            # req = urllib2.Request(url)
            req = urllib2.build_opener(MyHTTPErrorProcessor)
            response = req.open(url)
            if response.code == 302:
                if 'location' in response.headers:
                    url = response.headers['location']
                else:
                    url = response.headers['Location']
                pass
            print url
            if url == 'http://10.253.0.1':
                url = 'http://10.253.0.1/a70.htm'
                response = urllib.urlopen(
                    url, 'DDDDD='+U+'&upass='+P+'&R1=0&R2=&R6=0&para=00&0MKKey=123456')
                print response.read()
            time.sleep(20)
            pass
        except Exception, e:
            print "error2"
        pass
    pass


if __name__ == '__main__':
    thread_ctrl = threading.Thread(target=ctrl)
    thread_ctrl.start()
    thread_monitor = threading.Thread(target=monitor)
    thread_monitor.start()
    thread_ctrl.join()
    thread_monitor.join()
    print 'End'
