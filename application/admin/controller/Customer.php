<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Customer extends CommonController
{
    public function index(){
        if(request()->isPost()){
            $SearchItem = input('param.key');//获得传过来的值
            $customer=Db::name('logistics')->where('lid','like','%'.$SearchItem.'%')
                ->paginate(20);//根据姓名模糊搜索
            $this->assign("llist",$customer);//将查询结果转化为模版
            return $this->fetch(); //显示界面
        }//搜索时用到改分支
        $customer=Db::name("logistics")->paginate(20);//查询数据分页，每页20个数据
        $this->assign("llist",$customer);//将查询结果转化为模版
        return $this->fetch();//正常显示
    }


    public function delete($id){
        if(Db::name("logistics")->delete($id))//执行删除sql
        {
            //Db::name("videos")->getLastSql();exit;
            $this->success('删除成功');//删除成功
        }else $this->error('删除失败');//删除失败
    }//删除函数

    public function logout(){
        Session::destroy();//注销session
        $this->redirect('index/index');//返回登录页
    }//登出函数


    public function update(){
        if(request()->isPost()){
            $data=[
                "lid"=>input('param.lId'),
                "lnumber"=>input('param.lnumber'),
                "date"=>input('param.date'),
                "receivedate"=>input('param.receivedate'),
                "content"=>input('param.content'),
            ];//将传过来的数据转化为数组 这里必须写ID 否则缺少更新条件
            //存入数据库
            //$data=['typename'=>$typename];
            $res=Db::name("logistics")->update($data);//更新sql语句
            if($res){
                echo "<script>
                        history.back(-1);
                        alert('修改物流信息成功');
                        history.back(-1);
//                        $(#my-popups).modal('hide');
                        </script>";//成功执行
            }else{
                echo "<script>alert('修改物流信息失败');
                        history.back(-1);
                        </script>";//失败执行
            }
        }
    }//更新函数
    public function add(){
        if(request()->isPOST()){
            $DName=input('param.lid');//获得传入的医生名
            $num=Db::name("logistics")->where("DName='$DName'")->count();
            if($num>0) {
                echo "<script>alert('物流已存在');
                        history.back(-1);
                        </script>";
            }
            $data=[
                "lid"=>input('param.lId'),
                "lnumber"=>input('param.lnumber'),
                "date"=>input('param.date'),
                "receivedate"=>input('param.receivedate'),
            ];//数据 这里可以不用写ID
            $res=Db::name("logistics")->insert($data);
            if($res){
                echo "<script>alert('您已成功添加一条物流信息');
                        history.back(-1);
                        </script>";//成功时执行
                echo "<script>alert('添加物流信息失败');
                        history.back(-1);
                        </script>";//失败时执行
            }
            return $this->fetch();//正常显示
        }
    }//添加函数
    public function detail(){
        $Lid=input('param.lid');//获得Did
        //select用于查询0到的多条记录
        //find用于查询一条记录
        $Customer=Db::name("logistics")->find($Lid);//根据Did查询数据库

        echo json_encode($Customer);//以JSON格式返回
    }//对应index.html中的AJAX


    public function batchImport(){
        $file = request()->file('excel');
        if($file){
            $info = $file->rule('datea')->move('static/Excel/');
            if($info){
                $this->excel = $info->getSaveName();
                vendor("PHPExcel");
                $objPHPExcel = \PHPExcel_IOFactory::load(ROOT_PATH.'public'.DS.'static'.DS.'excel'.DS.$this->excel);//读取上传的文件
                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow(); // 取得总行数
                $highestColumn = $sheet->getHighestColumn(); // 取得总列数
                //循环读取excel文件,读取一条,插入一条
                for($j=2;$j<=$highestRow;$j++){
                    for($k='A';$k<=$highestColumn;$k++){
                        $this->str .= iconv("UTF-8","gbk",$objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue()).'\\';//读取单元格
                    }
                    $strs = explode("\\",$this->str);
                    Db::query("set names 'gb2312'");//这就是指定数据库字符集，一般放在连接数据库后面就系了
                    $sql = "INSERT INTO logistics (lnumber,date,receivedate,content) VALUES('".$strs[0]."','".$strs[1]."','".$strs[2]."','".$strs[3]."')";
//                    echo $sql;
                    if(Db::query($sql)){
                        echo '插入失败';
                    }
                    else{
                        echo '插入成功<br>';
                    }
                    $this->str= "";
                }
                unlink(ROOT_PATH.'public'.DS.'static'.DS.'excel'.DS.$this->excel); //删除上传的excel文件
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }

    }
    public  function Db2Excel(){
        vendor("PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $sheet = $objPHPExcel->getActiveSheet();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw");
        $objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
        $objPHPExcel->getProperties()->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.");
        $objPHPExcel->getProperties()->setKeywords("office 2007 openxml php");
        $objPHPExcel->getProperties()->setCategory("Test result file");
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);
        $sheet->getColumnDimension('L')->setWidth(40);
        // 实例化完了之后就先把数据库里面的数据查出来
        $sql = Db::name("logistics")->select();

        // 设置表头信息
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '日期')
            ->setCellValue('C1', '接收日期')
            ->setCellValue('D1', '预约工单内容');
        //$i=2;  //定义一个i变量，目的是在循环输出数据是控制行数
        $count = count($sql);  //计算有多少条数据
        for ($i = 2; $i <= $count+1; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $sql[$i-2]['lnumber']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $sql[$i-2]['date']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $sql[$i-2]['receivedate']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $sql[$i-2]['content']);
        }
        $outputFileName="Customer.xls";
        ob_clean();
        ob_start();
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $outputFileName);
        header("Content-Disposition:attachment;filename=$outputFileName");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
        echo "<script>
                       alert('导出数据成功');
                        window.open();
                        history.back(-1);
                        </script>";
    }
}