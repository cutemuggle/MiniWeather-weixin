<?php
namespace app\index\controller;
class Index
{
public function index()
{
echo "您好小仙女： " . cookie('user_name') . ', <a href="' . url('login/loginout') . '">退出</a>';
} 
  
public function reg()
{
echo "注册成功： " . cookie('user_name') . ', <a href="' . url('register') . '">退出</a>';
}   

}

