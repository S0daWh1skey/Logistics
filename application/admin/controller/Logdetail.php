<?php
namespace app\admin\controller;


namespace app\admin\controller;
use think\console\command\make\Model;
use think\Controller;
use think\Db;
use think\exception\DbException;

class Logdetail extends CommonController
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
    public function update(){
        if(request()->isPost()){
            $data=[
                "lid"=>input('param.lid'),
                "LogisticsNote"=>input('param.LogisticsNote'),
                "LogisticsNote"=>input('param.LogisticsNote'),
                "LogisticsPrescription"=>input('param.LogisticsPrescription'),
                "LogisticsType"=>input('param.LogisticsType'),
                "LogisticsNumber"=>input('param.LogisticsNumber'),
                "LogisticsIsgreen"=>input('param.LogisticsIsgreen'),
                "LogisticsIsreceive"=>input('param.LogisticsIsreceive'),
                "LogisticsAbnormal"=>input('param.LogisticsAbnormal'),
            ];//将传过来的数据转化为数组 这里必须写ID 否则缺少更新条件
            //存入数据库

            $res=Db::name("log1")->update($data);//更新sql语句
            if($res){
                echo "<script>
                        history.back(-1);
                        alert('修改信息成功');
                        history.back(-1);
                        </script>";//成功执行
            }else{
                echo "<script>alert('修改信息失败');
                        history.back(-1);
                        </script>";//失败执行
            }
        }
    }

    public function detail(){
        $Did=input('param.lid');
        $Doctor=Db::name("log1")->find($Did);//根据Did查询数据库
        echo json_encode($Doctor);//以JSON格式返回
    }

    public function logout(){
        Session::destroy();//注销session
        $this->redirect('index/index');//返回登录页
    }//登出函数

}