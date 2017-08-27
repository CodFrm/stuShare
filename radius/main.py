#!/usr/bin/python
# -*- coding: UTF-8 -*-

from socket import socket, AF_INET, SOCK_DGRAM
import threading
import struct
import md5
import MySQLdb
import time

DB_IP = '127.0.0.1'
DB_USER = 'root'
DB_PWD = ''
DB_DATABASE = 'stushare'
DB_PREFIX = 'share_'
secretKey = 'testing123'
db = MySQLdb.connect(DB_IP, DB_USER, DB_PWD, DB_DATABASE, charset='utf8')


def execute(sql, param):
    cursor = db.cursor()
    try:
        cursor.execute(sql, param)
        db.commit()
    except:
        db.rollback()
        print 'DB ERROR sql:' + sql


def fetchone(sql, param):
    cursor = db.cursor()
    try:
        cursor.execute(sql, param)
        result = cursor.fetchone()
    except:
        result = []
        print 'DB ERROR sql:' + sql
    return result


def query(sql, param):
    cursor = db.cursor()
    try:
        cursor.execute(sql, param)
    except:
        result = []
        print 'DB ERROR sql:' + sql
    return cursor


def bin2ip(bin):
    ret = ''
    for s in bin:
        ret += '.' + str(ord(s))
    return ret[1:]


class STRadius():

    def __init__(self):
        # 初始化
        self.udpRadiusServer = socket(AF_INET, SOCK_DGRAM)  # radius认证服务器 1812
        self.udpAccountServer = socket(AF_INET, SOCK_DGRAM)  # 计费服务器 1813
        self.udpRadiusServer.bind(('', 1812))
        self.udpAccountServer.bind(('', 1813))
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
            # radius结构
            radius = struct.Struct('!2ch16s')
            tmpStruct = radius.unpack_from(data, 0)
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
                        AuthDict['User-Name'], AuthDict['User-Password'])
                    if userMsg != False:
                        retCode = '\x02'
                print '用户接入'
            elif Code == '\x04':  # Accounting-Request 计费请求
                if AuthDict['Acct-Status-Type'] == '\x01':
                    userMsg = STRadius.gUser(
                        AuthDict['User-Name'])
                    if userMsg != False:
                        execute('insert into ' + DB_PREFIX +
                                'accounting(`uid`,`login_time`,`nas_ip`,`allot_ip`,`session_id`)' +
                                ' values(%s,%s,%s,%s,%s)', (str(userMsg[0]), str(time.time()), AuthDict['NAS-ip'],
                                                            AuthDict['Framed-IP'], AuthDict['Acct-Session-Id']))
                    print '开始计费'

                elif AuthDict['Acct-Status-Type'] == '\x02':
                    execute('update '+DB_PREFIX+'accounting set logout_time='+str(time.time())+' where session_id=%s',(AuthDict['Acct-Session-Id']))
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
    def vUser(user, pwd):
        results = query('select * from ' + DB_PREFIX +
                        'user as a JOIN ' + DB_PREFIX + 'usergroup AS b ON a.uid = b.uid' +
                        ' JOIN ' + DB_PREFIX + 'groupauth AS c ON c.group_id = b.group_id' +
                        ' JOIN ' + DB_PREFIX + 'auth AS d ON d.auth_id = c.auth_id' +
                        ' where `user`=%s or `email`=%s', [user,user])
        for row in results:
            if row[12] == 'radius' and (row[8] == -1 or row[8] > time.time()):
                if row != None:
                    if row[2] == pwd:  # 认证密码
                        # 查询同用户名有几台在线
                        data = fetchone('select count(*) from ' + DB_PREFIX + 'accounting where uid=' +
                                        str(row[0]) + ' and logout_time=-1',())
                        if data[0] > 0:
                            return False
                        return row
        return False

    @staticmethod
    def gUser(user):
        row = fetchone('select * from ' + DB_PREFIX +
                       'user where `user`=%s or `email`=%s', [user,user])
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
