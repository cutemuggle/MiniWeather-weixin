<?php
/**
 * Created by PhpStorm.
 * User: cutem
 * Date: 2018/12/12
 * Time: 11:14
 */

namespace app\token\controller;


class Oauth
{
    public function echoCode()
    {
        if(isset($_GET['code'])){
            echo $_GET['code'];

        }else{
            echo "No code";
        }
    }
}