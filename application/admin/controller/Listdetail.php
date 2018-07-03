<?php
namespace app\admin\controller;


namespace app\admin\controller;
use think\console\command\make\Model;
use think\Controller;
use think\Db;
use think\exception\DbException;

class Listdetail extends CommonController
{
    public function index(){
        $Lid=input('param.id');//获得Did
        //$doctors=Db::name("logistics")->paginate(20);//查询数据分页，每页20个数据
        $SQLC="SELECT * FROM logistics JOIN doctor ON logistics.Did=doctor.Did WHERE logistics.lid =$Lid ";
        $SQLLog="SELECT * FROM logistics JOIN log1 ON logistics.lid=log1.lid WHERE logistics.lid =$Lid ";
        $SQLLab="SELECT * FROM logistics JOIN lab ON logistics.lid=lab.lid WHERE logistics.lid=$Lid ";
        $Customer=Db::query($SQLC);
        $LOG=Db::query($SQLLog);
        $LAB=Db::query($SQLLab);
        // dump($Customer);
        //dump($Logistics);
        $this->assign("llist",$Customer);//将查询结果转化为模版
        $this->assign("loglist",$LOG);//将查询结果转化为模版
        $this->assign("lablist",$LAB);//将查询结果转化为模版
        return $this->fetch();//正常显示
    }




    public function logout(){
        Session::destroy();//注销session
        $this->redirect('index/index');//返回登录页
    }//登出函数

}