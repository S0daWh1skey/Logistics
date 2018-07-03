<?php
/**
 * Created by PhpStorm.
 * User: wangchenyu
 * Date: 2018/5/17
 * Time: 下午5:23
 */

namespace app\admin\controller;
use think\Session;
use think\Controller;

class CommonController extends Controller
{
    public function _initialize(){
        //如果session为空，说明用户未登录直接访问后台
        if(!Session::has('login_admin')){
            $this->redirect('index/index',['err'=>1]);
        }
    }
}