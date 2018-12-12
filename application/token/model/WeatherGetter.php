<?php /** @noinspection ALL */

/**
 * Created by PhpStorm.
 * User: liyuemeng
 * Date: 2018/11/29
 * Time: 22：06
 */

namespace app\token\model;

use DOMDocument;
use think\Db;
use think\Exception;

class WeatherGetter
{
    public function getCurrentCityWeather($usrName)
    {
        $resultSet = Db('location')->where('name', $usrName)->find();
        if ($resultSet == null) {
            return '请开启定位权限，开启后请重新进入页面';
        } else {
            return $this->getWeather($resultSet['city']);
        }
    }

    public function getWeather($cityName)
    {
        try {
            $cityCode = $this->getCityCode($cityName);
            $data = $this->getDataFromDB($cityCode);
            if ($data != null) {
                return $data;
            }

            $url = 'http://wthrcdn.etouch.cn/WeatherApi?citykey=' . $cityCode;
            $content = $this->openUrl($url);

            return $this->parseXML($content, $cityCode);
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
            return '暂无该城市的天气信息';
        }

    }

    private function getCityCode($cityName)
    {
        $result_set = DB::table('city')
            ->where('area', $cityName)
            ->find();
        $cityCode = $result_set['code'];
        return $cityCode;
    }

    private function getDataFromDB($cityCode)
    {
        $curTime = date('Y-m-d H:i:s');
        $result_set = DB::table('weather')
            ->where('cityCode', $cityCode)
            ->find();

        if ($result_set != null) {
            $city = $result_set['city'];
            $updateTime = $result_set['update-time'];
            $wendu = $result_set['wendu'];
            $shidu = $result_set['shidu'];
            $fengli = $result_set['fengli'];
            $fengxiang = $result_set['fengxiang'];


            $timeDiff = $this->timeDiff($updateTime, $curTime);
            if (!$this->dataIsValid($timeDiff)) {
                return null;
            }

            return '城市：' . $city . "\n "
                . '更新时间：' . $updateTime . "\n "
                . '温度：' . $wendu . "\n "
                . '湿度：' . $shidu . "\n "
                . '风力：' . $fengli . "\n "
                . '风向：' . $fengxiang;
        }


        Db('weather')->insert(
            [
                'citycode' => $cityCode,
                'city' => '',
                'wendu' => '',
                'shidu' => '',
                'fengli' => '',
                'fengxiang' => ''
            ]
        );
        return null;
    }

    private function dataIsValid($timeDiff)
    {
        if ($timeDiff['day'] > 0) {
            return false;
        }

        if ($timeDiff['hour'] > 0) {
            return false;
        }

        if ($timeDiff['min'] > 30) {
            return false;
        }
        return true;
    }

    private function parseXML($content, $cityCode)
    {
        $doc = new DOMDocument();
        $success = $doc->loadXML($content);
        if (!$success) {
            throw new Exception('无效的xml文件');
        }
        $city = $this->getSingleNodeValue($doc, 'city');
        $updateTime = $this->getSingleNodeValue($doc, "updatetime");
        $wendu = $this->getSingleNodeValue($doc, "wendu");
        $shidu = $this->getSingleNodeValue($doc, "shidu");
        $fengli = $this->getSingleNodeValue($doc, 'fengli');
        $fengxiang = $this->getSingleNodeValue($doc, 'fengxiang');


        Db('weather')->where('citycode', $cityCode)->update(
            [
                'city' => $city,
                'wendu' => $wendu,
                'shidu' => $shidu,
                'fengli' => $fengli,
                'fengxiang' => $fengxiang
            ]
        );

        $result = '城市：' . $city . "\n "
            . '更新时间：' . $updateTime . "\n "
            . '温度：' . $wendu . "\n "
            . '湿度：' . $shidu . "\n "
            . '风力：' . $fengli . "\n "
            . '风向：' . $fengxiang;


        return $result;
    }

    private function getSingleNodeValue($dom, $tagName)
    {
        return $dom->getElementsByTagName($tagName)->item(0)->nodeValue;
    }


    private function openUrl($url)
    {
        $content = file_get_contents($url);
        return gzdecode($content);
    }

    private function timeDiff($beginTime, $endTime)
    {
        if ($beginTime > $endTime) {
            return [
                'day' => 365,
                'hour' => 60,
                'min' => 60,
                'sec' => 60
            ];
        }


        $timeDiff = strtotime($endTime) - strtotime($beginTime);

        $days = intval($timeDiff / 86400);
        $remain = intval($timeDiff % 86400);
        $hours = intval($remain / 3600);
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        $secs = $remain % 60;
        return [
            'day' => $days,
            'hour' => $hours,
            'min' => $mins,
            'sec' => $secs
        ];
    }
}
                       