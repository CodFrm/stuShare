<?php
/**
 *============================
 * author:Farmer
 * time:2017/6/19 14:33
 * blog:blog.icodef.com
 * function:
 *============================
 */

namespace icf\lib;


class http {
    private $curl;

    public function __construct($url='') {
        $this->curl = curl_init($url);
        curl_setopt($this->curl, CURLOPT_HEADER, 0); //不返回header部分
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true); //返回字符串，而非直接输出
        curl_setopt($this->curl, CURLOPT_TIMEOUT,10);
    }
    public function setopt($key,$value){
        curl_setopt($this->curl,$key,$value);
    }
    public function setRedirection($value=1){
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $value);
    }
    public function __destruct() {
        // TODO: Implement __destruct() method.
        curl_close($this->curl);
    }

    public function setCookie($cookie) {
        curl_setopt($this->curl, CURLOPT_COOKIE, $cookie);
    }

    public function setHeader($header) {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
    }

    public function setUrl($url) {
        curl_setopt($this->curl, CURLOPT_URL, $url);
    }

    public function https(){
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
    }
    public function get() {
        $response = curl_exec($this->curl);
        return $response;
    }
}