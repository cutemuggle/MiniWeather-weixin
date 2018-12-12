<?php
namespace app\index\controller;

use think\Controller;

class Register extends Controller
{
    public function index()
    {
    	return $this->fetch();
    }
  // 处理注册逻辑
    public function doRegister()
    {
    	$param = input('post.');
    	if(empty($param['user_name'])){
    		
    		$this->error('用户名不能为空');
    	}
    	
    	if(empty($param['user_pwd'])){
    		
    		$this->error('密码不能为空');
    	}
    	
    	// 验证用户名
      
    	$has = db('users')->where('user_name', $param['user_name'])->find();
    	if(!empty($has)){
    		
    		$this->error('用户名已存在');
    	}else{
        	$data=['user_name'=>$param['user_name'],'user_pwd'=>md5($param['user_pwd'])];
          	db('users')->insert($data);
        }
      
      	$this->success('注册成功','login/index');
    }
    
    	// 验证密码
      /*
    	if($has['user_pwd'] != md5($param['user_pwd'])){
    		
    		$this->error('用户名密码错误');
    	}
    	
    	// 记录用户登录信息
    	cookie('user_id', $has['id'], 3600);  // 一个小时有效期
    	cookie('user_name', $has['user_name'], 3600);
    	
    	$this->redirect(url('index/index'));   */
  		//处理登陆逻辑
  		public function tzdl(){
          $this->redirect(url('login/index'));
    }
}