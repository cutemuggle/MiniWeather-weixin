<?php
/**
 * Created by PhpStorm.
 * User: douchengfeng
 * Date: 2018/11/30
 * Time: 19:13
 */

namespace miniweather\model;

use Org\Util\ChinesePinyin;
use think\Cache;

class CityGetter
{

    public function getCityList()
    {
        $name = 'cityList';
        $cityList = Cache::get($name, null);
        if ($cityList == null) {
            $cities = $this->getCitiesFromDB();
            $cityList = [];
            foreach ($cities as $city) {
                array_push($cityList,
                    [
                        'city' => $city
                    ]);
            }
            Cache::set($name, $cityList, null);
        }


        return $cityList;
    }


    private function getCitiesFromDB()
    {
        $resultSet = Db('city')
            ->select('area');

        $cityArray = array();
        $chinesePY = new ChinesePinyin();
        foreach ($resultSet as $item) {
            $area = $item['area'];
            $pinyin = $chinesePY->TransformUcwordsOnlyChar($area);
            array_push($cityArray, $pinyin[0] . " " . $area);
        }

        sort($cityArray, SORT_STRING);
        return $cityArray;
    }
}