<?php
namespace libs;
use Yii;
/*
Curl请求类
*/
class Curl
{
    private static $headers = [];
    private static $cookie = null;


    public static function get($url, $param = [])
    {
        return self::curl($url, $param, "get");
    }

    public static function post($url, $param, $extra = [])
    {
        return self::curl($url, $param, "post");
    }

    public static function setHeader($header)
    {
        self::$headers = $header;
    }

    public static function setCookie($cookie)
    {
        self::$cookie = $cookie;
    }


    protected static function curl($url, $param, $method = 'post')
    {
        $calculate_time1 = microtime(true);
        // 初始华
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CERTINFO, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($curl, CURLOPT_VERBOSE, true); //打印日志

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);//函数中加入下面这条语句

        if (isset(\Yii::$app->params['curl']) && isset(\Yii::$app->params['curl']['timeout'])) {
            curl_setopt($curl, CURLOPT_TIMEOUT, \Yii::$app->params['curl']['timeout']);
        } else {
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        }

        if (array_key_exists("HTTP_USER_AGENT", $_SERVER)) {
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        }

        if ( ! empty(self::$headers)) {
            $headerArr = [];
            foreach (self::$headers as $n => $v) {
                $headerArr[] = $n . ': ' . $v;
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArr);  //构造IP
        }

        if (self::$cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, self::$cookie);
        }


        // post处理
        if ($method == 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            if (is_array($param)) {
                $param = http_build_query($param);
            }

            curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        } else {
            curl_setopt($curl, CURLOPT_POST, false);
        }

        // 执行输出
        $info = curl_exec($curl);

        //log
        $_errno = curl_errno($curl);
        $_error = '';
        if ($_errno) {
            $_error = curl_error($curl);
        }
        curl_close($curl);
        $calculate_time_span = microtime(true) - $calculate_time1;
        $log = \Yii::$app->getRuntimePath() . DIRECTORY_SEPARATOR . 'curl.log';
        file_put_contents($log, date('Y-m-d H:i:s') . " [ time:{$calculate_time_span} ] url: {$url} \nmethod: {$method} \ndata: " . json_encode($param) . " \nresult: {$info} \nerrorno: {$_errno} error: {$_error} \n", FILE_APPEND);

        if ($_error) {
            return self::_err($_error);
        }

        return $info;
    }

}