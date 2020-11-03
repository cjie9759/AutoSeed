<?php

namespace app\index\controller;

// use think\Controller;

class Curl extends Base
{
    //
    public function curllog($url, $data)
    {
        $cookie = NULL;
        $data = http_build_query($data);
        $curlobj = curl_init();            // 初始化

        date_default_timezone_set('PRC'); // 使用Cookie时，必须先设置时区
        $options = [
            CURLOPT_POST           => 1,
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HEADER         => 1,
            CURLOPT_NOBODY         => 0,
            CURLOPT_FOLLOWLOCATION => 0,
            // CURLOPT_MAXREDIRS=>3,
            CURLINFO_HEADER_OUT    => 0,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0',
            CURLOPT_REFERER        => '/',
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_POSTFIELDS     => $data,
        ];
        curl_setopt_array($curlobj, $options);
        $tconent = curl_exec($curlobj);    // 执行
        // $tconent=curl_getinfo($curlobj);
        // dump($tconent);
        // return 1;
        preg_match_all('/set\-cookie:\ ([^\ ]*)/i', $tconent, $matches);
        for ($i = 0; $i < count($matches[1]); $i++) {
            $cookie = $cookie . $matches[1][$i];
        }
        //var_dump($cookie);
        return $cookie;
    }

    public function mulGet($array, $cookie, $timeout = 60)
    {
        $res = [];
        $mh = curl_multi_init();//创建多个curl语柄
        foreach ($array as $key => $value) {
            $k = $key;
            $conn[$k] = curl_init();
            date_default_timezone_set('PRC'); // 使用Cookie时，必须先设置时区
            $options = [
                CURLOPT_URL            => $value,
                CURLOPT_TIMEOUT        => $timeout,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HEADER         => 0,
                CURLOPT_COOKIE         => $cookie,
                CURLOPT_FOLLOWLOCATION => 0,
                CURLOPT_NOBODY         => 0,
                CURLINFO_HEADER_OUT    => 1,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0',
                CURLOPT_REFERER        => '/',

            ];
            curl_setopt_array($conn[$k], $options);
            curl_multi_add_handle($mh, $conn[$k]);
        }
        do {
            usleep(10000);
            curl_multi_exec($mh, $running);
        } while ($running > 0);
        foreach ($array as $key => $value) {
            $k = $key;
            $res[$k] = curl_multi_getcontent($conn[$k]);//获得返回信息
            $header[$k] = curl_getinfo($conn[$k]);//返回头信息
            curl_multi_remove_handle($mh, $conn[$k]);//释放资源
        }
        curl_multi_close($mh);
        return $res;
    }

    public function mulDown($array, $cookie, $path1 = './torrents', $timeout = 30)
    {

        !file_exists($path1) && mkdir($path1);
        // $res = array();
        $mh = curl_multi_init();//创建多个curl语柄
        foreach ($array as $key => $value) {
            $k = $key;
            $conn[$k] = curl_init();
            date_default_timezone_set('PRC'); // 使用Cookie时，必须先设置时区
            $options=[
                CURLOPT_URL            => $value,
                CURLOPT_TIMEOUT        => $timeout,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HEADER         => 0,
                CURLOPT_COOKIE         => $cookie,
                CURLOPT_FOLLOWLOCATION => 0,
                CURLOPT_NOBODY         => 0,
                CURLINFO_HEADER_OUT    => 1,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0',
                CURLOPT_REFERER        => '/',
            ];
            curl_setopt_array($conn[$k], $options);
            curl_multi_add_handle($mh, $conn[$k]);
        }//./torrents/$k.torrent
        do {
            usleep(10000);
            curl_multi_exec($mh, $running);
        } while ($running > 0);
        foreach ($array as $key => $value) {
            $k = $key;
            $path = $path1 . '/' . $k . '.torrent';
            $res[$k] = curl_multi_getcontent($conn[$k]);//获得返回信息
            $header[$k] = curl_getinfo($conn[$k]);//返回头信息
            $fd = fopen("$path", 'w');
            fwrite($fd, $res[$k]);  //将curl的结果写入文件里
            fclose($fd);
            curl_multi_remove_handle($mh, $conn[$k]);//释放资源
        }
        curl_multi_close($mh);
        return 1;
    }

    public function mulPost($url, $data, $cookie, $timeout = 60,$is_head=0,$is_nobody=0,$is_loca=0)
    {
        $res = array();
        $a = time();
        $mh = curl_multi_init();//创建多个curl语柄
        //curl_multi_setopt($mh, CURLMOPT_MAXCONNECTS,150);
        foreach ($data as $key => $value) {
            $k = $key;
            $conn[$k] = curl_init();
            date_default_timezone_set('PRC'); // 使用Cookie时，必须先设置时区
            // curl_setopt($conn[$k], CURLOPT_POST, 1);
            $options = [
                CURLOPT_POST           => 1,
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HEADER         => $is_head,
                CURLOPT_NOBODY         => $is_nobody,
                // 
                CURLOPT_FOLLOWLOCATION => $is_loca,
                // CURLOPT_MAXREDIRS=>3,
                CURLINFO_HEADER_OUT    => 0,
                CURLOPT_COOKIE         => $cookie,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:76.0) Gecko/20100101 Firefox/76.0',
                CURLOPT_REFERER        => '/',
                CURLOPT_TIMEOUT        => 10,
                // CURLOPT_POSTFIELDS     => http_build_query($value),
                CURLOPT_POSTFIELDS     => $value,
            ];
            // dump($options);dump($conn[$k]);
            // dump($data);exit();
            curl_setopt_array($conn[$k], $options);

            curl_multi_add_handle($mh, $conn[$k]);
        }
        do {
            usleep(1000000);
            // $b = time() - $a;
            curl_multi_exec($mh, $running);
            // echo "$b" . ':' . "$running" . PHP_EOL;
        } while ($running > 0);
        // echo 'multi curl返回值' . "$running" . PHP_EOL;


        foreach ($data as $key => $value) {
            $k = $key;
            $res[$k] = curl_multi_getcontent($conn[$k]);//获得返回信息
	    $res1[$k] = curl_getinfo($conn[$k]);//返回头信息
//	    dump($res1);
            curl_multi_remove_handle($mh, $conn[$k]);//释放资源
        }
        curl_multi_close($mh);
        return $res;
    }
}
