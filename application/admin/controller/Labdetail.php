<?php
namespace app\admin\controller;


namespace app\admin\controller;
use think\console\command\make\Model;
use think\Controller;
use think\Db;

class Labdetail extends CommonController
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
                "Lab_code"=>input('param.Lab_code'),
                "Lab_patientName"=>input('param.Lab_patientName'),
                "Lab_patientPhone"=>input('param.Lab_patientPhone'),
                "Lab_date"=>input('param.Lab_date'),
                "Lab_recipient"=>input('param.Lab_recipient'),
                "Lab_estimatedTime"=>input('param.Lab_estimatedTime'),
                "Lab_reportTime"=>input('param.Lab_reportTime'),
                "Lab_reportWay"=>input('param.Lab_reportWay'),
                "Lab_reportName"=>input('param.Lab_reportName'),
                "Lab_reportMail"=>input('param.Lab_reportMail'),
            ];//将传过来的数据转化为数组 这里必须写ID 否则缺少更新条件
            //存入数据库

            $res=Db::name("lab")->update($data);//更新sql语句
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
        $Doctor=Db::name("lab")->find($Did);//根据Did查询数据库
        echo json_encode($Doctor);//以JSON格式返回
    }

    public function logout(){
        Session::destroy();//注销session
        $this->redirect('index/index');//返回登录页
    }//登出函数

}