#!/usr/bin/python
#coding:utf-8
import urllib2,urllib
import cookielib
import threading
import re
import string
import function
from function import subStr
from function import Timestamp
from function import getDeviceID
from function import synckey
from function import getRand
import json
global sykey
global dataJson
lhurl='127.0.0.1/stushare'
cookie = cookielib.MozillaCookieJar()
handler = urllib2.HTTPCookieProcessor(cookie)
opear = urllib2.build_opener(handler)
f=open('cookie.txt','r')
cookieFile=f.read()
f.close()
uin=subStr(str(cookieFile),'wxuin=',';')
sid=subStr(str(cookieFile),'wxsid=',';')
post='{"BaseRequest":{"Uin":"'+uin+'","Sid":"'+sid+'","Skey":"","DeviceID":"'+getDeviceID()+'"}}'
cookieFile=cookieFile.replace('\n','')
req = urllib2.Request('https://wx2.qq.com/cgi-bin/mmwebwx-bin/webwxinit?r=1346484012', data=post, headers={
    'Cookie': cookieFile,
})
res = opear.open(req)
data = res.read()
skey=subStr(data,'SKey": "','"')
dataJson=json.loads(data)
sykey=synckey(dataJson['SyncKey']['List'])

def GetHttp(url):
    data=''
    try:
        handler = urllib2.HTTPCookieProcessor(cookie)
        opear = urllib2.build_opener(handler)
        req = urllib2.Request(url, data=None, headers={
            'Accept': '*/*',
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393',
            'Accept-Language': 'zh-CN,zh;q=0.8,en-US;q=0.6,en;q=0.4',
            'Connection':'keep-alive',
            'Cache-Control':'no-cache',
            'Referer':'https://wx2.qq.com/',
            'Cookie':cookieFile})
        res = opear.open(req)
        data = res.read()
    except:
        print url
    return data


def PostHttp(url,postData):
    data=''
    try:
        handler = urllib2.HTTPCookieProcessor(cookie)
        opear = urllib2.build_opener(handler)
        req = urllib2.Request(url, data=postData, headers={
            'Accept': '*/*',
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393',
            'Accept-Language': 'zh-CN,zh;q=0.8,en-US;q=0.6,en;q=0.4',
            'Connection':'keep-alive',
            'Cache-Control':'no-cache',
            'Referer':'https://wx2.qq.com/',
            'Cookie':cookieFile})
        res = opear.open(req)
        data = res.read()
    except:
        print url
    else:
        pass
    return data

def dealOrder(html):
    html=html.encode('utf8')
    orderList = re.findall('二维码收款到账(.*?)元.*?付款方留言：(.*?)<br/>.*?pay\_outtradeno&gt;&lt;!\[CDATA\[(.*?)\]\]', html)
    print orderList
    for arr in orderList:
        if arr.count >= 3:
            GetHttp('http://'+lhurl+'/admin/money/pay_call?order=' +
                    arr[2] + '&money=' + arr[0] + '&remarks=' + arr[1])

def monitor():
    i = 0
    global sykey
    global dataJson
    while True:
        url='https://webpush.wx2.qq.com/cgi-bin/mmwebwx-bin/synccheck?r='+Timestamp()+'3'
        url+='&'+urllib.urlencode({'skey':skey})
        url+='&'+urllib.urlencode({'sid':sid})+'&uin='+uin+'&deviceid='+getDeviceID()
        url+='&synckey='+sykey
        url+='&_='+Timestamp()+'5'
        data=GetHttp(url)
        if data.find('"2"')>0:
            post='{"BaseRequest":{"Uin":'+uin+',"Sid":"'+sid+'","Skey":"'+skey+'",'
            post+='"DeviceID":"'+getDeviceID()+'"},"SyncKey":{"Count":'+str(dataJson['SyncKey']['Count'])+',"List":'
            post+=json.dumps(dataJson['SyncKey']['List'])
            post+='},"rr":'+getRand(10)+'}'
            post=post.replace(' ','')
            url='https://wx2.qq.com/cgi-bin/mmwebwx-bin/webwxsync?sid='+sid+'&'+urllib.urlencode({'skey':skey})
            try:
                data=PostHttp(url,post)
                dataJson=json.loads(data)
                print data
                print dataJson['AddMsgList'][0]['Content'] 
                if dataJson['AddMsgList'][0]['MsgType']==49:
                    dealOrder(dataJson['AddMsgList'][0]['Content'])
            except:
                print 'json error'
                print post
            sykey=synckey(dataJson['SyncKey']['List'])
        elif data.find('"1101"')>0:
            print 'cookie not'
            exit(0)
        elif sykey=='':
            try:
                data=GetHttp('https://wx2.qq.com/cgi-bin/mmwebwx-bin/webwxinit?r=1346484012')
                dataJson=json.loads(data)
                sykey=synckey(dataJson['SyncKey']['List'])
            except:
                print 'sykey error'
        GetHttp('http://'+lhurl+'/admin/money/pay_call')

thread = threading.Thread(target=monitor)
thread.start()
thread.join()
print 'END'
