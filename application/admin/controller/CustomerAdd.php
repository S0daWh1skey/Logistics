<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class CustomerAdd extends CommonController
{
    private $excel='';//这里声明
    private $str='';
    public function index(){
        if(request()->isPost()){
            $SearchItem = input('param.search');//获得传过来的值
            $doctors=Db::name('Doctor')->where('DName','like','%'.$SearchItem.'%')
                ->paginate(20);//根据姓名模糊搜索
            $this->assign("dlist",$doctors);//将查询结果转化为模版
            return $this->fetch(); //显示界面
        }//搜索时用到改分支
        $doctors=array();
        $this->assign("dlist",$doctors);//将查询结果转化为模版
        return $this->fetch();//正常显示
    }

    public function insert($Did){
        dump($Did);
    }

    /**
     *
     */
    public function add(){
        $arr = $_POST["Dids"];
        if(request()->isPOST()){
            $cnt = 0;
            $total = 0;
            foreach($arr as $Did){
                $DName=input('param.lnumber');
                $num=Db::name("logistics")->where("lnumber='$DName'")->count();
                $data=[
                    "Did" => $Did,
                    "lnumber"=>input('param.lnumber'),//"数据库字段"=>input('param.Name') <input name="Name" id="" class=""></input>
                    "date"=>input('param.date'),
                    "receivedate"=>input('param.receivedate'),
                    "content"=>input('param.content'),
                ];//数据 这里可以不用写ID
                $res=Db::name("logistics")->insert($data);
                $userId = Db::name('logistics')->getLastInsID();
                if($res){
                    $cnt++;
                    $total++;
                }
                if($cnt>0){
                    $data1=[
                        "lid"=>$userId,
                    ];
                    Db::name("lab")->insert($data1);
                    Db::name("log1")->insert($data1);
                    echo "<script>alert('您已成功添加一条物流信息');
                        history.back(-1);
                        </script>";//成功时执行
                }
                else if($total>0){
                    echo "<script>alert('添加物流信息失败');
                        history.back(-1);
                        </script>";//失败时执行
                }
            }
        }
    }



    public function logout(){
        Session::destroy();//注销session
        $this->redirect('index/index');//返回登录页
    }//登出函数


    public function multiplyAdd(){
        if(request() -> isPost()){
            $lid =input('param.lnumber');
            dump($lid);
        }
    }
}