<?php

namespace app\token\model;


class TokenSaver
{
    function checkAccessToken($appid, $appsecret)
    {
        $condition = array('appid' => $appid, 'appsecret' => $appsecret);
        $access_token_set = DB('wxtoken')->where($condition)->find();//获取数据
        //var_dump($access_token_set);
        if ($access_token_set) {
            //检查是否超时，超时了重新获取
            if ($access_token_set['AccessExpires'] > time()) {
                //未超时，直接返回access_token
                return $access_token_set['access_token'];
            } else {
                //已超时，重新获取
                $url_get = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret;
                $json = $this->https_request($url_get);
                var_dump($json);
                $access_token = $json['access_token'];
                $AccessExpires = time() + intval($json['expires_in']);
                $data['access_token'] = $access_token;
                $data['AccessExpires'] = $AccessExpires;
                $result = DB('wxtoken')->where($condition)->update($data);//更新数据
                if ($result) {
                    return $access_token;
                } else {
                    return $access_token;
                }
            }
        } else {
            echo "appid或appsecret不正确";
            return false;
        }
    }


    function https_request($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $out = curl_exec($ch);
        curl_close($ch);
        return json_decode($out, true);
    }
}
