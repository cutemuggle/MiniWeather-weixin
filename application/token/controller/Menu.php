<?php

namespace app\token\controller;

use app\token\model\TokenSaver;
use app\token\util\GetPostUtil;
use think\Controller;


class Menu extends Controller
{
    public function createMenu()
    {
        $data = '{
       "button":[
       {
            "type":"click",
            "name":"获取当前天气",
            "key":"CurrentWeather"
        },
        {
             "name":"菜单",
             "sub_button":[
             {
                 "type":"view",
                 "name":"天气预报界面Web版",
                 "url":"http://www.ss.pku.edu.cn"
              },
              {
                 "type":"click",
                 "name":"赞一下我们",
                 "key":"V1001_GOOD"
              }]
         }]
      }';
        $token = new Token();
        $access_token = $token->getToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $access_token;
        var_dump($url);
        var_dump($data);
        $result = $this->postcurl($url, $data);
        var_dump($result);
    }


    function postcurl($url, $data = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output = json_decode($output, true);
    }

    public function getUser()
    {
        $token = new Token();
        $saver = new TokenSaver();
        $access_token = $token->getToken();
        $url_get = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $access_token;
        $user_json = $saver->https_request($url_get);
        //var_dump($json);
        $url_get = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $access_token . '&openid=' . $user_json['data']['openid'][0] . '&lang=zh_CN';
        $user_info = $saver->https_request($url_get);
        var_dump($user_info);
    }
}