<?php

namespace app\api\model;

use think\Model;

use think\Db;

class City extends Model{

  public function getCitycode($county_name='北京')

  {

    $res=Db::name('ins_county')->where('county_name',$county_name)->select();

    return $res;

  }

  public function getCityList(){

    $res=Db::name('ins_county')->select();

    return $res;

  }

}