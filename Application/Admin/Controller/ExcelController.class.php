<?php
/**
 * Created by PhpStorm.
 * User: l.wang
 * Date: 2017/4/10
 * Time: 15:52
 */

namespace Admin\Controller;


use Think\Controller;

class ExcelController extends Controller
{
    public function upload()
    {
        header("Content-Type:text/html;charset=utf-8");

        if ($_POST) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 314572813456;// 设置附件上传大小
            $upload->exts = array('xls', 'xlsx', 'csv');// 设置附件上传类
            $upload->savePath = '/Excel/'; // 设置附件上传目录
            // 上传文件
            $info = $upload->uploadOne($_FILES['files']);
//			dump($info);exit;
            $filename = './Uploads' . $info['savepath'] . $info['savename'];
            $exts = $info['ext'];
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            } else {// 上传成功
                vendor('PHPExcel.PHPExcel');
                $phpexcel = new \phpexcel();
                if ($exts == "xls") {
                    dump($this->import_excelxls($filename));
                } elseif ($exts == "xlsx") {
                    $this->import_excelxlsx($filename);
                }
            }
        } else {
            $this->display();

        }


    }

    public function import_excelxls($filename)
    {
        if (!file_exists($filename)) {
            exit("文件内容不能为空!");
        } else {
            vendor("PHPExcel.Reader.Excel5");
            $phpexcelReader = new \PHPExcel_Reader_Excel5();
            $phpexcel = $phpexcelReader->load($filename);
            // 获取工作表(及当前活动的sheet)
            $currentSheet = $phpexcel->getSheet(0);
            // 获取总列数
            $allColumn = $currentSheet->getHighestColumn();
            // 获取总行数
            $allRow = $currentSheet->getHighestRow();
            //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
            for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
                //从哪列开始，A表示第一列
                for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                    //数据坐标
                    $address = $currentColumn . $currentRow;
                    //读取到的数据，保存到数组$arr中
                    $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
                }
            }
            $count = count($arr);
            dump($arr[1]['A']);
            dump($arr);
            exit;
            $table = M("baidu_calldetailinfo");
            for ($i = 3; $i < $count + 1; $i++) {
                $data['call_date'] = $arr[$i]['A'];
                $data['data_count'] = $arr[$i]['B'];
                $data['data_click'] = $arr[$i]['C'];
                $data['call_count'] = $arr[$i]['D'];
                $data['call_tel'] = $arr[$i]['E'];
                $data['call_time'] = $arr[$i]['F'];
                $data['call_content'] = $arr[$i]['G'];
                $table->data($data)->add();

            }
            return $arr;
        }

    }

    public function import_excelxlsx($filename)
    {
        if (file_exists($filename)) {
            vendor("PHPExcel.Reader.Excel2007");
            $phpexcelReader = new \PHPExcel_Reader_Excel2007();
            $phpexcel = $phpexcelReader->load($filename);
            // 获取工作表(及当前活动的sheet)
            $currentSheet = $phpexcel->getSheet(0);
            // 获取总列数
            $allColumn = $currentSheet->getHighestColumn();
            // 获取总行数
            $allRow = $currentSheet->getHighestRow();
            //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
            for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
                //从哪列开始，A表示第一列
                for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                    //数据坐标
                    $address = $currentColumn . $currentRow;
                    //读取到的数据，保存到数组$arr中
                    $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
                }
            }
            $count = count($arr);

            dump($arr);
            exit;
//            $table = M("baidu_calldetailinfo");
//            for ($i = 3; $i < $count + 1; $i++) {
//                $data['call_date'] = $arr[$i]['A'];
//                $data['data_count'] = $arr[$i]['B'];
//                $data['data_click'] = $arr[$i]['C'];
//                $data['call_count'] = $arr[$i]['D'];
//                $data['call_tel'] = $arr[$i]['E'];
//                $data['call_time'] = $arr[$i]['F'];
//                $data['call_content'] = $arr[$i]['G'];
//                $table->data($data)->add();
//            }
        } else {
            exit("文件不存在");
        }

    }
}