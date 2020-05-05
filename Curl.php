<?php

/**
 * Curl操作类 https://gitee.com/hamm/PHPCurl
 */
class Curl
{
    /**
     * 当前的CURL实体对象
     *
     * @var object
     */
    private $curl = null;
    /**
     * 本次请求的Headers
     *
     * @var array
     */
    private $headers = [];
    /**
     * 本次请求的Cookies
     *
     * @var string
     */
    private $cookies = "";
    /**
     * 请求返回的实体数据
     *
     * @var string
     */
    private $response = [];
    /**
     * 本次请求是否超时
     *
     * @var bool
     */
    private $isTimeout = false;
    /**
     * 请求是否返回Header
     *
     * @var bool
     */
    private $isReponseHeader = false;
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_XML = 'application/xml';
    const CONTENT_TYPE_FORM = 'x-www-form-urlencoded';
    const CONTENT_TYPE_FORM_DATA = 'multipart/form-data';
    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_TEXT = 'text/plain';
    /**
     * 构造方法 返回当前CURL对象
     *
     * @param  string 请求的URL地址
     * @return this
     */
    public function __construct($url)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        $this->headers['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.113 Safari/537.36';
        $this->headers['referer'] = $url;
    }
    /**
     * 返回Reponse的实体数据
     *
     * @return string
     */
    public function getResponseBody()
    {
        return $this->response['body'];
    }
    /**
     * 返回Response的header
     *
     * @param  string 可选项 获取指定的header数据
     * @return mixed 返回的数据
     */
    public function getResponseHeader($key = null)
    {
        if ($key) {
            if (preg_match('/' . $key . ': (.*?)' . PHP_EOL . '/', $this->response['header'], $matches)) {
                return $matches[1];
            } else {
                return false;
            }
        } else {
            return $this->response['header'];
        }
    }
    /**
     * 返回Response的详细信息
     *
     * @param  string 可选项 获取指定的Detail数据
     * @return mixed
     */
    public function getResponseDetail($key = null)
    {
        if ($key) {
            if (array_key_exists($key, (array) ($this->response['detail']))) {
                return $this->response['detail'][$key];
            } else {
                return false;
            }
        } else {

            return $this->response['detail'];
        }
    }
    /**
     * 需要Header传递授权信息
     *
     * @param  string $access_token
     * @return this
     */
    public function setRequestAuthorize($access_token)
    {
        $this->headers['authorization'] = $access_token;
        return $this;
    }
    /**
     * 设置Request请求头
     * $value为空时 $key可为 a:b形式
     * $value不为空 $key为a $value为b
     *
     * @param  string $key
     * @param  string $value
     * @return this
     */
    public function addRequestHeader($key, $value = null)
    {
        if ($value) {
            $this->headers[$key] = $value;
        } else {
            $_arr = explode(":", $key);
            $this->headers[strtolower(str_replace(" ", '', $_arr[0]))] = count($_arr) > 1 ? $_arr[1] : "";
        }
        return $this;
    }
    /**
     * 获取返回的301重定向地址
     *
     * @return mixed
     */
    public function getResponseRedirect()
    {
        if (preg_match('/location: (.*?)' . PHP_EOL . '/i', $this->response, $matches)) {
            return $matches[1];
        } else {
            return false;
        }
    }
    /**
     * 获取Reponse返回的所有cookie数组
     *
     * @return mixed
     */
    public function getResponseCookies()
    {
        if (preg_match_all('/set-cookie: (.*?);/i', $this->response, $matches)) {
            $arr = [];
            foreach ($matches[1] as $item) {
                $_temp = explode('=', $item);
                $arr[$_temp[0]] = (count($_temp) > 1) ? $_temp[1] : "";
            }
            return $arr;
        } else {
            return false;
        }
    }
    /**
     * 根据Key获取返回的Cookie值
     *
     * @param  string 指定Cookie的key
     * @return string 返回cookie的值
     */
    public function getResponseCookie($key)
    {
        if (preg_match_all('/set-cookie: (.*?);/i', $this->response, $matches)) {
            foreach ($matches[1] as $item) {
                $_arr = explode('=', $item);
                if (count($_arr) > 1 && $_arr[0] == $key) {
                    return $_arr[1];
                }
            }
        }
        return false;
    }
    /**
     * 设置是否返回Header
     *
     * @return this
     */
    public function setResponseHeader()
    {
        $this->isReponseHeader = true;
        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        return $this;
    }
    /**
     * 设置请求cookie
     * $value为空时 $key可为 a=b形式
     * $value不为空 $key为a $value为b
     *
     * @param  string $key
     * @param  string $value
     * @return this
     */
    public function addRequestCookie($key, $value = null)
    {
        if ($value) {
            $this->cookies .= $key . "=" . $value . ";";
        } else {
            $this->cookies .= $key . ";";
        }
        curl_setopt($this->curl, CURLOPT_COOKIE, $this->cookies);
        return $this;
    }
    /**
     * 设置ContentType 可使用定义常量
     * 默认使用HTML
     *
     * @param  string
     * @return this
     */
    public function setRequestContentType($contentType)
    {
        $this->headers['content-type'] = $contentType;
        return $this;
    }
    /**
     * 添加Referer来源地址
     *
     * @param  mixed $referer
     * @return this
     */
    public function setRequestReferer($referer)
    {
        $this->headers['referer'] =  $referer;
        return $this;
    }
    /**
     * 检查SSL证书 默认不检查
     *
     * @return this
     */
    public function setRequestSSL()
    {
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, true);
        return $this;
    }
    /**
     * 设置请求超时时间
     *
     * @param  int 秒数
     * @return this
     */
    public function setRequestTimeout($timeout = 0)
    {
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $timeout);
        return $this;
    }
    /**
     * 设置UserAgent
     *
     * @param  string UserAgent字符串
     * @return this
     */
    public function setRequestUserAgent($userAgent)
    {
        $this->headers['user-agent'] = $userAgent;
        return $this;
    }
    /**
     * 自动跟随重定向
     *
     * @param  mixed 最大允许重定向次数
     * @return this
     */
    public function setRequestRedirect($maxRedirect = 3)
    {
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->curl, CURLOPT_MAXREDIRS, $maxRedirect);
        return $this;
    }
    /**
     * 是否开启Gzip压缩
     *
     * @return this
     */
    public function setRequestGzip()
    {
        curl_setopt($this->curl, CURLOPT_ENCODING, 'gzip');
        return $this;
    }
    /**
     * 设置Proxy代理服务器
     *
     * @param  string 代理服务器地址
     * @param  string 端口号
     * @return this
     */
    public function setRequestProxy($ip, $port)
    {
        curl_setopt($this->curl, CURLOPT_PROXY, $ip);
        curl_setopt($this->curl, CURLOPT_PROXYPORT, $port);
        curl_setopt($this->curl, CURLOPT_PROXYUSERPWD, "taras:taras-ss5");
        return $this;
    }
    /**
     * 执行POST操作并返回当前CURL对象
     *
     * @param  mixed $data
     * @return this
     */
    public function doPost($data = null)
    {
        if ($data) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($this->curl, CURLOPT_POST, 1);
        return $this->request();
    }
    /**
     * 执行PATCH操作并返回当前CURL对象
     *
     * @param  mixed $data
     * @return this
     */
    public function doPatch($data = null)
    {
        if ($data) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PATCH");
        return $this->request();
    }
    /**
     * 执行PUT操作并返回当前CURL对象
     *
     * @param  mixed $data
     * @return this
     */
    public function doPut($data = null)
    {
        if ($data) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
        return $this->request();
    }
    /**
     * 执行DELETE操作并返回当前CURL对象
     *
     * @param  mixed $data
     * @return this
     */
    public function doDelete($data = null)
    {
        if ($data) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        return $this->request();
    }
    /**
     * 执行OPTIONS操作并返回当前CURL对象
     *
     * @param  mixed $data
     * @return this
     */
    public function doOptions($data = null)
    {
        if ($data) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "OPTIONS");
        return $this->request();
    }
    /**
     * 执行HEAD操作并返回当前CURL对象
     *
     * @param  mixed $data
     * @return this
     */
    public function doHead($data = null)
    {
        if ($data) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "HEAD");
        return $this->request();
    }
    /**
     * 执行TRACE操作并返回当前CURL对象
     *
     * @param  mixed $data
     * @return this
     */
    public function doTrace($data = null)
    {
        if ($data) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "TRACE");
        return $this->request();
    }
    /**
     * 执行CONNECT操作并返回当前CURL对象
     *
     * @param  mixed $data
     * @return this
     */
    public function doConnect($data = null)
    {
        if ($data) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "CONNECT");
        return $this->request();
    }
    /**
     * 发起GET请求并返回当前CURL对象
     *
     * @return this
     */
    public function doGet()
    {
        return $this->request();
    }
    /**
     * 发起请求实体操作方法
     *
     * @return this
     */
    private function request()
    {
        $headerArray = [];
        foreach ($this->headers as $key => $value) {
            $headerArray[] = $key . ": " . $value;
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headerArray);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,  1);
        $response = curl_exec($this->curl);
        if (curl_errno($this->curl) == CURLE_OPERATION_TIMEOUTED) {
            //如果访问超时 这里将标记
            $this->isTimeout = true;
        }
        if ($this->isReponseHeader) {
            // 获得响应结果里的：头大小
            $headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
            // 根据头大小去获取头信息内容
            $this->response['header'] = substr($response, 0, $headerSize);
            $this->response['body'] = substr($response, $headerSize, strlen($response) - $headerSize);
        } else {
            $this->response['header'] = '';
            $this->response['body'] = $response;
        }
        $this->response['detail'] =  curl_getinfo($this->curl);
        curl_close($this->curl);
        return $this;
    }
}
