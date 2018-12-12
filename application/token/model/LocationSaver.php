<?php
/**
 * Created by PhpStorm.
 * User: liyuemeng
 * Date: 2018/11/28
 * Time: 22:11
 */

namespace app\token\logic;


use DOMDocument;

class LocationSaver
{
    public function saveLocation($usrName, $Latitude, $Longitude)
    {
        $url = "http://api.map.baidu.com/geocoder?location=" . $Latitude . "," . $Longitude . "&output=xml&key=28bcdd84fae25699606ffad27f8da77b";
        $content = file_get_contents($url);
        $doc = new DOMDocument();
        $success = $doc->loadXML($content);
        if (!$success) {
            return "无法解析地理信息";
        }

        $city = $doc->getElementsByTagName('city')->item(0)->nodeValue;
        $city = preg_replace("(市|乡|县|村|区)", "", $city);
        $this->insertOrUpdate($usrName, $city);
        return "当前城市为" . $city;
    }


    private function insertOrUpdate($usrName, $city)
    {
        $resultSet = Db('location')->where('name', $usrName)->find();
        if ($resultSet != null) {
            Db('location')->where('name',$usrName)->update(
                [
                    'city' => $city
                ]
            );
        } else {
            Db('location')->insert(
                [
                    'name' => $usrName,
                    'city' => $city
                ]
            );
        }
    }
}
    