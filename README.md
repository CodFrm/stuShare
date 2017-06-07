# stuShare
[校园网分享计划](https://github.com/CodFrm/stuShare)

# 搭建openVPN+FreeRADIUS认证

## 配置openVPN
### 安装openVPN
CentOS

yum install openvpn

### 生成服务器证书
```
wget https://github.com/OpenVPN/easy-rsa/archive/master.zip

unzip master.zip

mv easy-rsa-mater/ easy-rsa/

cp -R easy-rsa/ /etc/openvpn/

cd /etc/openvpn/easy-rsa/easyrsa3/

cp vars.example vars
```
`nano vars 修改下面字段,然后修改,最后wq保存`
```
set_var EASYRSA_REQ_COUNTRY "CN"

set_var EASYRSA_REQ_PROVINCE "BJ"

set_var EASYRSA_REQ_CITY "BeiJing"

set_var EASYRSA_REQ_ORG "stuShare"

set_var EASYRSA_REQ_EMAIL "admin@icodef.com"

set_var EASYRSA_REQ_OU "stuShare"

cd /etc/openvpn/easy-rsa/easyrsa3/

./easyrsa build-ca

./easyrsa gen-req server nopass

./easyrsa sign server server

./easyrsa gen-dh
```
`修改server.conf配置`
```
cp /usr/share/doc/openvpn-2.3.14/sample/sample-config-files/server.conf /etc/openvpn
```

### 安装配置FreeRADIUS
```
yum install freeradius freeradius-mysql -y

wget http://www.nongnu.org/radiusplugin/radiusplugin_v2.1a_beta1.tar.gz

tar xf radiusplugin_v2.1a_beta1.tar.gz 

cd radiusplugin_v2.1a_beta1

yum install libgcrypt-devel -y

make

cp radiusplugin.so /etc/openvpn/

cp radiusplugin.cnf /etc/openvpn/

```

`配置radius文件`


`导入数据库文件`

`开启nat转发和iptables配置`

`firewall不知道配置,推荐关闭firewall开启iptables`

```
iptables -t nat -A POSTROUTING -s 10.8.0.0/24 -j MASQUERADE

iptables -Z

iptables -F

iptables -X
```

git clone https://github.com/CodFrm/stuShare.git

`进行安装,然后就完成啦`
