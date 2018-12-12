<?php
/**
 * Created by PhpStorm.
 * User: douchengfeng
 * Date: 2018/11/23
 * Time: 9:09
 */

namespace miniweather\controller;


use think\Controller;

class Weather extends Controller
{
    public function showWeather()
    {
        $this->assign('curCity', '北京');
        $this->assign('city', $this->setCityList());
        return view('weather');
    }

    public function setCityList()
    {
        return model('CityGetter')->getCityList();
    }
}