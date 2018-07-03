<?php
namespace app\admin\controller;
use think\console\command\make\Model;
use think\Controller;
use think\Db;
use think\exception\DbException;

class Guest extends CommonController

{
    public function index(){
        if(request()->isPost()){
            $SearchItem = input('param.key');//获得传过来的值
            $doctors=Db::name('Doctor')->where('DName','like','%'.$SearchItem.'%')
                ->paginate(20);//根据姓名模糊搜索
            $this->assign("dlist",$doctors);//将查询结果转化为模版
            return $this->fetch(); //显示界面
        }//搜索时用到改分支
        //$doctors=Db::name("logistics")->paginate(20);//查询数据分页，每页20个数据
        //$doctors = Db::name("logistics , doctor")->where('logistics.Did= doctor.Did')->paginate();
        $doctors=Db::name("logistics")->join("doctor","logistics.Did=doctor.Did")->join("lab","lab.lid=logistics.lid")->join("log1","log1.lid=logistics.lid")->paginate(10);
        //dump($doctors);
        $this->assign("llist",$doctors);//将查询结果转化为模版
        return $this->fetch();//正常显示
    }
    public function logout(){
        Session::destroy();//注销session
        $this->redirect('index/index');//返回登录页
    }//登出函数
}