<?php
/**
 * Created by PhpStorm.
 * User: albertshepherd
 * Date: 2018/4/25
 * Time: 13:17
 */
namespace app\admin\model;

use think\Db;
use think\Model;
use think\Session;

class login extends Model{
    public function login($adminname,$password){

        $stuff=Db::name('Stuff')->where('StuffName',$adminname)
            ->find();
        if(!$stuff){
            return 1;
            //echo "<script>alert('账号不存在');</script>";
        }
        elseif (md5($password)!=$stuff['StuffPWD']){
            return 2;
            // echo "<script>alert('密码错误');</script>";
        }else{
            Session::set('login_admin',$adminname);
            Session::set('login_id',$stuff['StuffID']);
            Session::set('Dept_id',$stuff['StuffDepartment']);
            //$this->redirect('video/index');
            return 3;
        }
    }
}