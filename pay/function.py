import time
import random

def subStr(string,left,right):
    
    lpos=string.find(left)+len(left)
    rpos=string.find(right,lpos)
    return str(string)[lpos:rpos]


def Timestamp():
    return str(time.time()).replace('.','')

def getDeviceID():
    return 'e'+getRand(15)

def getRand(lenght):
    retStr=''
    for n in range(lenght):
        retStr+=str(random.randint(0,9))
    return retStr


def synckey(json):
    ret=''
    for data in json:
        ret+=str(data['Key'])+'_'+str(data['Val'])+'%7C'
    ret=ret[0:len(ret)-3]
    return ret