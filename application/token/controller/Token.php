<?php
/**
 * Created by PhpStorm.
 * User: liyuemeng
 * Date: 2018/11/30
 * Time: 15：07
 */

namespace app\token\controller;

use think\Controller;

class Token extends Controller
{

    public function responseToken()
    {
        $echostr = $_GET['echostr'];
        if ($this->checkSignature()) {
            echo $echostr;
            exit;
        }
    }

    function getToken()
    {
        return model('TokenSaver')->checkAccessToken("wx1dbbcab060690f7e", "4cdd83ee2c3de3c2246242bd347b8b64");
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);
            switch ($RX_TYPE) {
                case "text":
                    $resultStr = $this->receiveText($postObj);
                    break;
                case "event":
                    $resultStr = $this->receiveEvent($postObj);
                    break;
                default:
                    $resultStr = "";
                    break;
            }
            echo $this->transmitText($postObj, $resultStr);
        } else {
            echo '听不懂';
            exit;
        }
    }

    private function receiveText($postObj)
    {
        $keyword = trim($postObj->Content);
        if (!empty($keyword)) {
            $contentStr = model('WeatherGetter')->getWeather($keyword);
            return $contentStr;
        } else {
            return '听不懂';
        }
    }

    private function transmitText($object, $content, $funcFlag = 0)
    {
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[text]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>%d</FuncFlag>
                    </xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $funcFlag);
        return $resultStr;
    }

    public function showLocation(){
        $Latitude = $_GET['latitude'];
        $Longitude = $_GET['longitude'];
        $usrName = $_GET['usrName'];
        echo model('LocationSaver')->saveLocation($usrName, $Latitude, $Longitude);
    }

    private function receiveEvent($postObj)
    {
        switch ($postObj->Event) {
            case "subscribe":
                return "欢迎关注我的订阅号";
            case "CLICK":
                switch ($postObj->EventKey) {
                    case 'CurrentWeather':
                        return model('WeatherGetter')->getCurrentCityWeather($postObj->FromUserName);
                    default:
                        break;
                }
                break;
            case "LOCATION":
                return model('LocationSaver')->saveLocation($postObj->FromUserName, $postObj->Latitude, $postObj->Longitude);


        }

        return '无效的事件';
    }

    public function showWeather()
    {
        $city = $_GET['city'];
        return model('WeatherGetter')->getWeather($city);
    }


    private function checkSignature()
    {
        $nonce = $_GET['nonce'];
        $token = 'demo';
        $timestamp = $_GET['timestamp'];
        $signature = $_GET['signature'];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        return $tmpStr == $signature;
    }
}