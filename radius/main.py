#!/usr/bin/python
# -*- coding: UTF-8 -*-

from socket import socket, AF_INET, SOCK_DGRAM
import threading
import struct
import md5
import MySQLdb
import time
import json
global db
import random

DB_IP = '127.0.0.1'
DB_USER = 'root'
DB_PWD = ''
DB_DATABASE = 'stushare'
DB_PREFIX = 'share_'
secretKey = 'testing123'
db = MySQLdb.connect(DB_IP, DB_USER, DB_PWD, DB_DATABASE, charset='utf8')
db.autocommit(True)


def ping():
    global db
    try:
        db.ping()  # cping 校验连接是否异常
    except:
        db = MySQLdb.connect(DB_IP, DB_USER, DB_PWD,
                             DB_DATABASE, charset='utf8')
        # time.sleep(3)      #连接不成功,休眠3秒钟,继续循环，知道成功或重试次数结束


def execute(sql, param):
    global db
    cursor = db.cursor()
    try:
        cursor.execute(sql, param)
        db.commit()
    except:
        db.rollback()
        print 'DB ERROR sql:' + sql
    cursor.close()


def fetchone(sql, param):
    global db
    cursor = db.cursor()
    try:
        cursor.execute(sql, param)
        result = cursor.fetchone()
        db.commit()
    except:
        result = []
        print 'DB ERROR sql:' + sql
    cursor.close()
    return result


def query(sql, param):
    global db
    cursor = db.cursor()
    try:
        cursor.execute(sql, param)
        db.commit()
    except:
        result = []
        print 'DB ERROR sql:' + sql
    cursor.close()
    return cursor


def bin2ip(bin):
    ret = ''
    for s in bin:
        ret += '.' + str(ord(s))
    return ret[1:]


class STRadius():

    ctrlServer = object

    def __init__(self):
        # 初始化
        self.udpRadiusServer = socket(AF_INET, SOCK_DGRAM)  # radius认证服务器 1812
        self.udpAccountServer = socket(AF_INET, SOCK_DGRAM)  # 计费服务器 1813
        STRadius.ctrlServer = socket(AF_INET, SOCK_DGRAM)  # 服务器控制udp 1364
        self.udpRadiusServer.bind(('', 1812))
        self.udpAccountServer.bind(('', 1813))
        STRadius.ctrlServer.bind(('', 1365))
        STRadius.ctrlServer.settimeout(2)
        # 清除计费在线
        execute('update ' + DB_PREFIX + 'accounting set logout_time=' +
                str(time.time()) + ' where logout_time=-1', ())

    def listen(self):
        print '开始监听'
        thread_radius = threading.Thread(
            target=STRadius.dealThread, args=(self.udpAccountServer,))
        thread_radius.start()
        thread_account = threading.Thread(
            target=STRadius.dealThread, args=(self.udpRadiusServer,))
        thread_account.start()
        thread_account.join()
        thread_radius.join()

    @staticmethod
    def dealThread(obj):
        # 处理线程
        while True:
            data, addr = obj.recvfrom(1024)
            ping()
            # radius结构
            try:
                radius = struct.Struct('!2ch16s')
                tmpStruct = radius.unpack_from(data, 0)
            except:
                print 'error data'
                continue
                pass
            Code = tmpStruct[0]
            Identifier = tmpStruct[1]
            Length = tmpStruct[2]
            Authenticator = tmpStruct[3]
            Attributes = data[20:]
            AuthDict = STRadius.dealAttributes(
                Attributes, Authenticator, secretKey)
            retCode = '\x03'
            if Code == '\x01':  # Access-Request 登陆验证请求
                if AuthDict['User-Name'] != '' and AuthDict['User-Password'] != '':
                    userMsg = STRadius.vUser(
                        AuthDict['User-Name'], AuthDict['User-Password'], AuthDict['Acct-Session-Id'])
                    if userMsg != False:
                        retCode = '\x02'
                print '用户接入'
            elif Code == '\x04':  # Accounting-Request 计费请求
                if AuthDict['Acct-Status-Type'] == '\x01':
                    userMsg = STRadius.gUser(
                        AuthDict['User-Name'])
                    if userMsg != False:
                        try:
                            execute('insert into ' + DB_PREFIX +
                                    'accounting(`uid`,`login_time`,`nas_ip`,`allot_ip`,`session_id`)' +
                                    ' values(%s,%s,%s,%s,%s)', (str(userMsg[0]), str(time.time()), AuthDict['NAS-ip'],
                                                                AuthDict['Framed-IP'], AuthDict['Acct-Session-Id']))
                            row = fetchone('select * from ' + DB_PREFIX + 'usergroup as a join ' + DB_PREFIX +
                                           'set_meal as b on a.group_id=b.group_id' +
                                           ' where uid=%s', [str(userMsg[0])])
                            sendJson = {}
                            sendJson['type'] = 'flow'
                            sendJson['width'] = row[5]
                            sendJson['ip'] = AuthDict['Framed-IP']
                            STRadius.ctrlServer.sendto(json.dumps(
                                sendJson), (AuthDict['NAS-ip'], 1364))
                        except:
                            print '权限错误'

                    print '开始计费'

                elif AuthDict['Acct-Status-Type'] == '\x02':
                    try:
                        execute('update ' + DB_PREFIX + 'accounting set out_byte=%s,input_byte=%s,online_time=%s,logout_time=' +
                                str(time.time()) + ' where session_id=%s', (AuthDict['Acct-Output-Octets'], AuthDict['Acct-Input-Octets'],
                                                                            AuthDict['Acct-Session-Time'], AuthDict['Acct-Session-Id']))
                    except Exception, e:
                        print 'error!!!'
                    print '结束计费'
                retCode = '\x05'
            print AuthDict
            sendData = retCode + Identifier + '\x00\x14'
            sendAuthenticator = sendData + Authenticator
            m1 = md5.new()
            m1.update(sendAuthenticator + secretKey)
            sendAuthenticator = m1.digest()
            sendData = retCode + Identifier + '\x00\x14' + sendAuthenticator
            obj.sendto(sendData, addr)

    @staticmethod
    def vUser(user, pwd, sid):
        results = query('select * from ' + DB_PREFIX +
                        'user as a JOIN ' + DB_PREFIX + 'usergroup AS b ON a.uid = b.uid' +
                        ' JOIN ' + DB_PREFIX + 'groupauth AS c ON c.group_id = b.group_id' +
                        ' JOIN ' + DB_PREFIX + 'auth AS d ON d.auth_id = c.auth_id' +
                        ' where `user`=%s or `email`=%s', [user, user])
        for row in results:
            if row[12] == 'radius' and (row[8] == -1 or row[8] > time.time()):
                if row != None:
                    if row[2] == pwd or True:  # 认证密码
                        # 判断session_id是否存在
                        sid_res = fetchone('select * from ' + DB_PREFIX +
                                           'user as a JOIN ' + DB_PREFIX + 'accounting AS d ON a.uid = d.uid' +
                                           ' where (`user`=%s or `email`=%s) and `session_id`=%s and `logout_time`=-1', [user, user, sid])
                        if sid_res == None:
                            # 查询同用户名有几台在线
                            data = fetchone('select * from ' + DB_PREFIX + 'accounting where uid=' +
                                            str(row[0]) + ' and logout_time=-1', ())
                            if data != None:
                                if data[2] > time.time() - 120:  # 如果连接时间没有超过120直接否决
                                    return False
                                # 检测是否真正在线
                                sendJson = {}
                                sendJson['type'] = 'verify'
                                ip = data[8]
                                sid = data[9]
                                sendJson['ip'] = data[8]
                                STRadius.ctrlServer.sendto(
                                    json.dumps(sendJson), (data[7], 1364))
                                try:
                                    data, addr = STRadius.ctrlServer.recvfrom(
                                        1024)
                                except Exception, e:
                                    if e[0] == 'timed out':
                                        data = None
                                if data == None:  # 超时
                                    print ip + ' ctrl error'
                                    return False
                                try:
                                    row = json.loads(data)
                                    print row
                                    if not('type' in row):
                                        return False
                                    if row['type'] == 'verify':
                                        if row['online'] == 'true' and row['ip'] == ip:
                                            return False
                                        execute('update ' + DB_PREFIX + 'accounting set logout_time=' + str(
                                            time.time()) + ' where session_id=%s', (sid))
                                        return row
                                except:
                                    print "json error:" + data
                                    pass
                                return False
                        return row
        return False

    @staticmethod
    def gUser(user):
        row = fetchone('select * from ' + DB_PREFIX +
                       'user where `user`=%s or `email`=%s', [user, user])
        if row != None:
            return row
        return False

    @staticmethod
    def dealAttributes(byte, Ra, Key):
        # 对Attributes进行处理
        # 1 for User-Name
        retDict = {}
        while True:
            lenght = ord(byte[1])
            if byte[0] == '\x01':  # 1 for User-Name
                retDict['User-Name'] = byte[2:lenght]
            elif byte[0] == '\x02':  # 2 for User-Password
                tmpPwd = byte[2:lenght]
                retDict['User-Password'] = STRadius.PwdDecrypted(
                    tmpPwd, Ra, Key)
            elif byte[0] == '\x28':  # 40 for Acct-Status-Type
                retDict['Acct-Status-Type'] = byte[5]
            elif byte[0] == '\x04':  # 4 for NAS-ip
                retDict['NAS-ip'] = bin2ip(byte[2:lenght])
            elif byte[0] == '\x08':  # 8 for Framed-IP
                retDict['Framed-IP'] = bin2ip(byte[2:lenght])
            elif byte[0] == '\x2C':  # 44 for Acct-Session-Id
                retDict['Acct-Session-Id'] = byte[2:lenght]
            elif byte[0] == '\x2A':
                tmpStruct = struct.Struct('L')
                tmpStruct = tmpStruct.unpack_from(byte[2:lenght][::-1], 0)
                retDict['Acct-Input-Octets'] = tmpStruct[0]
            elif byte[0] == '\x2B':
                tmpStruct = struct.Struct('L')
                tmpStruct = tmpStruct.unpack_from(byte[2:lenght][::-1], 0)
                retDict['Acct-Output-Octets'] = tmpStruct[0]
            elif byte[0] == '\x2E':
                tmpStruct = struct.Struct('L')
                tmpStruct = tmpStruct.unpack_from(byte[2:lenght][::-1], 0)
                retDict['Acct-Session-Time'] = tmpStruct[0]
            elif byte[0] == '\x34':  # Acct-Input-Octets溢出次数
                tmpStruct = struct.Struct('L')
                tmpStruct = tmpStruct.unpack_from(byte[2:lenght][::-1], 0)
                retDict['Acct-Input-Gigawords'] = tmpStruct[0]
            elif byte[0] == '\x35':  # Acct-Output-Octets溢出次数
                tmpStruct = struct.Struct('L')
                tmpStruct = tmpStruct.unpack_from(byte[2:lenght][::-1], 0)
                retDict['Acct-Output-Gigawords'] = tmpStruct[0]
            byte = byte[lenght:]
            if byte == '':
                break
        return retDict

    @staticmethod
    def PwdDecrypted(Ciphertext, Ra, Key):
        m1 = md5.new()
        m1.update(Key + Ra)
        pwd = m1.digest()
        retPwd = ''
        for i in range(0, len(pwd)):
            ch = chr(ord(pwd[i]) ^ ord(Ciphertext[i]))
            if ch == '\x00':
                break
            retPwd += ch
        return retPwd


if __name__ == '__main__':
    radius = STRadius()
    radius.listen()
    print 'End'
