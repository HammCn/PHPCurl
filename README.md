# PHPCurl操作类
PHPCurl操作类是一个对Curl库的常用操作进行高度封装的操作对象类。

## 使用说明

1. 引入Curl类，实例化一个操作对象：
```php
<?php 
require_once("Curl.php");
$url = 'https://hamm.cn';
$curl = new Curl($url);
// 除去do**开头的方法之外，都支持链式操作
// $curl->set***()->set***()->set***()
```

2.请求前的相关设置
```php
setRequestAuthorize('basic abcdefghijklmn')              //通过Header传递身份验证参数
addRequestHeader('accept-language: zh-CN')               //设置请求头 字符串方式
addRequestHeader('accept-language','zh-CN')              //设置请求头 多参数方式
setResponseHeader()                                      //设置请求是否返回Header部分
addRequestCookie('access_token=abcdefj')                 //设置请求Cookie 字符串方式
addRequestCookie('access_token','abcdefj')               //设置请求Cookie 多参数方式
setRequestContentType($curl::CONTENT_TYPE_JSON)          //设置请求ContentType 枚举常量
setRequestReferer('https://hamm.cn')                     //设置请求的Referer
setRequestSSL()                                          //设置检查TLS证书 默认不检查
setRequestTimeout(10)                                    //设置超时时间为10秒
setRequestUserAgent('Chrome')                            //设置请求UserAgen
setRequestRedirect(3)                                    //设置请求将自动重定向 最多3次
setRequestGzip()                                         //设置允许Gzip压缩
setRequestProxy('10.0.10.80',8080)                       //设置请求代理IP和端口
```

3.发起请求
```php
doPost()                         //发起Post请求
doPatch()                        //发起Patch请求
doPut()                          //发起Put请求
doDelete()                       //发起Delete请求
doOptions()                      //发起Options请求
doHead()                         //发起Head请求
doTrace()                        //发起Trace请求
doConnect()                      //发起Connect请求
doGet()                          //发起Get请求
```

4.获取Response信息
```php
getResponseBody()                    //获取返回的Body数据
getResponseHeader()                  //获取返回的header字符串
getResponseHeader('Content-Type')    //获取返回的指定header数据
getResponseDetail()                  //获取返回的请求详情数组
getResponseDetail('http_code')       //根据key获取返回的请求详情
getResponseCookies()                 //获取返回的Cookies数组
getResponseCookie('access_token')    //获取返回的指定cookie
getResponseRedirect()                //获取重定向的URL

```