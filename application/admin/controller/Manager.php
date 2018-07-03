<?php
namespace app\admin\controller;
use think\Controller;
use think\session;
use think\Db;
class Manager extends \app\admin\controller\CommonController
{
    private $excel='';//这里声明
    private $str='';
    public function index(){
        if(request()->isPost()){
            $SearchItem = input('param.search');
            $stuffs=Db::name('Stuff')->where('StuffName','like','%'.$SearchItem.'%')
                ->paginate(20);
            $this->assign("slist",$stuffs);
            return $this->fetch();

        }
        $stuffs=Db::name("Stuff")->paginate(5);
        $this->assign("slist",$stuffs);
        return $this->fetch();
    }

    public function edit(){
        $StuffID=input('param.StuffID');
        //select用于查询0到的多条记录
        //find用于查询一条记录
        $stuff=Db::name("Stuff")->find($StuffID);

        echo json_encode($stuff);
    }

    public function update(){
        if(request()->isPost()){
            $data=[
                "StuffID"=>input('param.stuffId'),
                "StuffName"=>input('param.stuffName'),
                "StuffDepartment"=>input('param.stuffDept'),
                "Stuff_Phone"=>input('param.stuffPhone'),
                "StuffEmail"=>input('param.stuffEmail'),
            ];
            //存入数据库
            //$data=['typename'=>$typename];
            $res=Db::name("Stuff")->update($data);
            if($res){
                echo "<script>
                        history.back(-1);
                        alert('修改员工信息成功');
                        history.back(-1);
//                        $(#my-popups).modal('hide');
                        </script>";
            }else{
                echo "<script>alert('修改员工信息失败');
                        history.back(-1);
                        </script>";
            }
        }
    }
    public function add(){
        if(request()->isPOST()){
            $stuffName=input('param.stuffName');
            $num=Db::name("Stuff")->where("StuffName='$stuffName'")->count();
            if($num>0) {
                echo "<script>alert('员工已存在，或重名');
                        history.back(-1);
                        </script>";
            }
            $data=[
                'StuffName'=>input('param.stuffName'),
                'StuffEmail'=>input('param.stuffEmail'),
                'StuffDepartment'=>input('param.stuffDept'),
            ];
            $res=Db::name("Stuff")->insert($data);
            if($res){
                echo "<script>alert('您已成功添加一名员工');
                        history.back(-1);
                        </script>";
            }else{
                echo "<script>alert('添加员工失败');
                        history.back(-1);
                        </script>";
            }

        }

    }

    public function batchImport(){
        $file = request()->file('excel');
        if($file){
            $info = $file->rule('datea')->move('static/Excel/');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
//                echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $this->excel = $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
//                echo $info->getFilename();
                vendor("PHPExcel");
                $objPHPExcel = \PHPExcel_IOFactory::load(ROOT_PATH.'public'.DS.'static'.DS.'excel'.DS.$this->excel);//读取上传的文件
//                $arrExcel = $objPHPExcel->getSheet(0)->toArray();//获取其中的数据
//                dump($arrExcel);
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
                    $sql = "INSERT INTO stuff (StuffName,StuffDepartment,StuffEmail) VALUES('".$strs[0]."','".$strs[1]."','".$strs[2]."')";
//                    echo $sql;
                    if(Db::query($sql)){
                       echo '插入失败';
                    }
                    else{
                        echo '插入成功<br>';
                    }
                    $this->str = "";
                }
                unlink(ROOT_PATH.'public'.DS.'static'.DS.'excel'.DS.$this->excel); //删除上传的excel文件
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }

}
    public function delete($id){
        //先删除头像：find方法和unlink

        if(Db::name("Stuff")->delete($id))
        {
            //Db::name("videos")->getLastSql();exit;
            $this->success('删除成功');
        }else $this->error('删除失败');


    }

    public function logout(){
        Session::destroy();
        $this->redirect('index/index');
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
        $sql = Db::name("Stuff")->select();

        // 设置表头信息
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '员工编号')
            ->setCellValue('B1', '员工名字')
            ->setCellValue('C1', '所属部门')
            ->setCellValue('D1', '员工手机号')
            ->setCellValue('E1', '员工邮箱')
            ->setCellValue('F1', '最后登录');
        //$i=2;  //定义一个i变量，目的是在循环输出数据是控制行数
        $count = count($sql);  //计算有多少条数据
        for ($i = 2; $i <= $count+1; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $sql[$i-2]['StuffID']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $sql[$i-2]['StuffName']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $sql[$i-2]['StuffDepartment']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $sql[$i-2]['StuffEmail']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $sql[$i-2]['Stuff_Phone']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $sql[$i-2]['LastLogin']);
        }
        $outputFileName="Stuff.xls";
        ob_clean();
        ob_start();
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $outputFileName);
        header("Content-Disposition:attachment;filename=$outputFileName");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
        echo "<script>alert('导出数据成功');
                        history.back(-1);
                        </script>";
    }
}