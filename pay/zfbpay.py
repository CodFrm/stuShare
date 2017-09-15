#!/usr/bin/python
import urllib2
import cookielib
import time
import threading
import re

cookie = cookielib.MozillaCookieJar()
handler = urllib2.HTTPCookieProcessor(cookie)
opear = urllib2.build_opener(handler)
f=open('cookie.txt','r')
cookieFile=f.read()
cookieFile=cookieFile.replace('\n','')
def getMidStr(s,left,right):
    leftPos=s.find(left)
    rightPos=s.find(right,leftPos)
    return s[leftPos+len(left):rightPos]

ctoken=getMidStr(cookieFile,'ctoken=',';')
f.close()
req = urllib2.Request('https://my.alipay.com/portal/i.htm', data=None, headers={
    'Cookie': cookieFile
})
res = opear.open(req,timeout=10)
data = res.read()
f = open('var.html', 'w')
f.write(data)
f.close()
def dealOrder(html):
    orderList = re.findall('<td class="time">([\s\S]*?)</tr>',html)
    for arr in orderList:
        arr=re.findall('bizInNo=(.*?)&createDate[\S\s]+?<p class="memo-info">(.*?)</p>[\S\s]+?<span class="amount-pay">(.*?) (.*?)</span>', arr)
        if arr!=[]:
            arr=arr[0]
            print arr
            if arr[2]=='+':
                print GetHttp('http://127.0.0.1/stushare/user/money/pay_call?order=' +
                        arr[0] + '&money=' + arr[3] + '&remarks=' + arr[1])
                print 'http://127.0.0.1/stushare/user/money/pay_call?order=' +arr[0] + '&money=' + arr[3] + '&remarks=' + arr[1]


def GetHttp(url):
    data=''
    try:
        handler = urllib2.HTTPCookieProcessor(cookie)
        opear = urllib2.build_opener(handler)
        req = urllib2.Request(url, data=None, headers={
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
            'Accept-Language': 'zh-CN,zh;q=0.8,en-US;q=0.6,en;q=0.4',
            'Cookie': cookieFile})
        res = opear.open(req,timeout=10)
        data = res.read()
    except:
        print url
    else:
        pass
    return data


def monitor():
    i = 0
    while True:
        if i >= 5:
            try:
                GetHttp('https://my.alipay.com/portal/i.htm')
                GetHttp('https://kcart.alipay.com/web/bi.do?ref=https%3A%2F%2Fwww.alipay.com%2F&pg=https%3A%2F%2Fmy.alipay.com%2Fportal%2Fi.htm&screen=1536x864&color=-&BIProfile=page&sc=24-bit&utmhn=my.alipay.com&_clnt=windows%2F10.0%7Cwebkit%2F537.36%7Cchrome%2F58.0.3029.110%7Cpc%2F-1&r=0.7953424291980806&v=1.1')
                GetHttp('https://my.alipay.com/m.gif?from=home&t=1496658964626')
                # GetHttp('https://uninav.alipay.com/nav/getUniData.json?_callback=jQuery17202182682149142403_1496658964458&_input_charset=utf-8&ctoken=2H6Eqv-HYcdrllKQ&_output_charset=utf-8&_=1496658964949')
                GetHttp('https://kcart.alipay.com/web/bi.do?ref=https%3A%2F%2Fmy.alipay.com%2Fportal%2Fi.htm&pg=https%3A%2F%2Fmy.alipay.com%2Fportal%2Fi.htm%3Fvalue%3D562%26seed%3DTTI-global-nav&BIProfile=calc&r=0.9370226942544375&v=1.1')
                # GetHttp('https://my.alipay.com/tile/service/portal:recent.tile?t=1496659109827&_input_charset=utf-8&ctoken=2H6Eqv-HYcdrllKQ&_output_charset=utf-8')
                GetHttp('https://my.alipay.com/portal/behavior.json')
                # GetHttp('https://consumeprod.alipay.com/ebill/simpleFinance.json?_callback=jQuery1720002074896630239742_1496659109176&_output_charset=utf-8&_input_charset=utf-8&ctoken=2H6Eqv-HYcdrllKQ&_=1496659109863')
                # GetHttp('https://app.alipay.com/container/queryMyAlipayApp.json?platformKey=110&_callback=jQuery1720002074896630239742_1496659109177&_output_charset=utf-8&_input_charset=utf-8&ctoken=2H6Eqv-HYcdrllKQ&_=1496659109896')
                # GetHttp('https://lab.alipay.com/user/msgcenter/getMsgInfosNew.json?_callback=callback&_input_charset=utf-8&ctoken=2H6Eqv-HYcdrllKQ&_output_charset=utf-8&_=1496659110113')
                GetHttp('https://kcart.alipay.com/web/bi.do?ref=https%3A%2F%2Fmy.alipay.com%2Fportal%2Fi.htm&pg=https%3A%2F%2Fmy.alipay.com%2Fportal%2Fi.htm%3Fseed%3Dmyalipay-search-seed-store_data&BIProfile=clk&r=0.9470722578025235&v=1.1')
            except:
                print 'error'
            else:
                print 'success'
            i = 0
        GetHttp('https://kcart.alipay.com/web/bi.do?ref=https%3A%2F%2Fauthem14.alipay.com%2Flogin%2FloginResultDispatch.htm&pg=https%3A%2F%2Fconsumeprod.alipay.com%2Frecord%2Fstandard.htm&screen=1536x864&color=-&BIProfile=page&sc=24-bit&utmhn=consumeprod.alipay.com&_clnt=windows%2F10.0%7Cwebkit%2F537.36%7Cchrome%2F58.0.3029.110%7Cpc%2F-1&r=0.8121143199420737&v=1.1')
        # GetHttp('https://uninav.alipay.com/nav/data.json?_callback=jQuery17206489527233979464_1496670121316&_input_charset=utf-8&ctoken=4DUq81egwZnAf1Cj&_=1496670121605')
        # GetHttp('https://lab.alipay.com/user/msgcenter/getMsgInfosNew.json?_callback=jQuery17206489527233979464_1496670121317&_input_charset=utf-8&ctoken=4DUq81egwZnAf1Cj&_=1496670121789')
        #data = GetHttp('https://consumeprod.alipay.com/record/standard.htm')
        data=GetHttp('https://my.alipay.com/tile/service/portal:recent.tile?t=1504698796712&_input_charset=utf-8&ctoken='+ctoken+'&_output_charset=utf-8')
        # print 'https://my.alipay.com/tile/service/portal:recent.tile?t=1504698796712&_input_charset=utf-8&ctoken='+ctoken+'&_output_charset=utf-8'
        f = open('var1.html', 'w')
        f.write(data)
        f.close()
        dealOrder(data)
        time.sleep(30)
        i += 1
        print time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(time.time()))


thread = threading.Thread(target=monitor)
thread.start()
thread.join()
print 'END'