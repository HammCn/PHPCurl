<?php
//正在不断完善中
class Curl
{
    protected $curl = null;
    protected $headers = [];
    protected $cookies = "";
    protected $response = '';
    public function __construct($url)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    /**
     * 设置Request请求头
     * $value为空时 $key可为 a:b形式
     * $value不为空 $key为a $value为b
     *
     * @param  string $key
     * @param  string $value
     * @return void
     */
    public function addHeader($key, $value = null)
    {
        if ($value) {
            $this->headers[] = $key . ":" . $value;
        } else {
            $this->headers[] = $key;
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        return $this;
    }
    /**
     * 获取返回的301重定向地址
     *
     * @return string
     */
    public function getRedirect()
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
     * @return void
     */
    public function getCookies()
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
     * @param  string $key
     * @return string $value
     */
    public function getCookie($key)
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
     * 是否返回Header
     *
     * @return this
     */
    public function returnHeader()
    {
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
     * @return void
     */
    public function addCookie($key, $value = null)
    {
        if ($value) {
            $this->cookies .= $key . "=" . $value . ";";
        } else {
            $this->cookies .= $key . ";";
        }
        curl_setopt($this->curl, CURLOPT_COOKIE, $this->cookies);
        return $this;
    }
    public function post()
    {
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,  1);
        $this->response = curl_exec($this->curl);
        curl_close($this->curl);
        return $this;
    }
    public function get()
    {
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,  1);
        $this->response = curl_exec($this->curl);
        curl_close($this->curl);
        return $this;
    }
    /**
     * 检查SSL证书 默认不检查
     *
     * @return void
     */
    public function checkSSL()
    {
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, true);
    }
}

// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_REFERER, $url);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
// curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
// curl_setopt($ch, CURLOPT_COOKIE, $cookies);
// curl_setopt($ch, CURLOPT_POST, 1);
// if ($timeout) {
//     //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,$timeout);
//     curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
// }
// if (!empty($proxy)) {
//     curl_setopt($ch, CURLOPT_PROXY, $proxy['ip']);
//     curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
//     curl_setopt($ch, CURLOPT_PROXYUSERPWD, "taras:taras-ss5");
// }
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, $isBackGround ? 0 : 1);
// curl_setopt($ch, CURLOPT_HEADER, $returnHeader ? 1 : 0);
// $output = curl_exec($ch);
// if ($timeout) {
//     if ($output === FALSE) {
//         if (curl_errno($ch) == CURLE_OPERATION_TIMEOUTED) {
//             $output = 'TIMEOUT';
//         } else {
//             $output = 'ERROR';
//         }
//     }
// }
// curl_close($ch);
// return $output;
