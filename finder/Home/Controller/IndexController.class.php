<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }
    
    public function ll(){
        $path = isset($_POST['path']) ? $_POST['path'] : '';
        $res = array();
        $obj = D('finder');
        $rows = $obj->ll($path);
        if(!isset($rows['dir']))
            $rows['dir'] = array();
        if(!isset($rows['file']))
            $rows['file'] = array();
        $res['rows'] = array_merge($rows['dir'], $rows['file']);
        if(empty($res['rows'])){
            $res['rows'][] = array(
                'basename' => '<空文件夹>',
                'type'=>'-',
            );
        }
        $res['path'] = $path;
        echo json_encode($res);
    }

    public function test()
    {
        $path = '';
        $res = array();
        $obj = D('finder');
        $rows = $obj->ll($path);
        $res['rows'] = array_merge($rows['dir'], $rows['file']);
        if(empty($res['rows'])){
            $res['rows'][] = array(
                'basename' => '空文件夹',
                'type'=>'-',
            );
        }
        $res['path'] = $path;
        echo json_encode($res);
    }

    public function upload(){
        //$session['yes'] = $_POST['id'];
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->rootPath  =     './Public/root/'.$_POST['dirname'].'/'; // 设置附件上传根目录
        //$upload->savePath  =     $_POST['dirname']; // 设置附件上传（子）目录
        $upload->autoSub = false;
        $upload->saveName = '';
        //$upload->autoSub   =     false;
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            //echo json_encode($res['error'] = 'no');
        }else{// 上传成功
            echo json_encode("ddd");
        }
    }

    public function newfolder(){
        $res =array();
        $name = json_decode($_POST['fname']);
        $dirname = json_decode($_POST['dname']);
        if(empty($name)){
            $res['success'] = false;
            $res['error'] = '文件夹名不能为空';
        }else{
            $target = './Public/root/'.$dirname.'/'.$name;
            if(file_exists($target)){
                $res['success'] = false;
                $res['error'] = '文件夹已存在';
            }else{
                mkdir($target);
                $res['success'] = true;
            }
        }
        echo json_encode($res);
    }

    public function rm(){
        $items = json_decode($_POST['items'], true);
        var_dump($items);
        $val = array();
        foreach($items as $key => $v) {
            preg_match('/&nbsp&nbsp(.*)<\/a>/U', $v['basename'], $val);
            if ($v['type'] == 'file') {
                unlink($v['dirname'] . '/' . $val[1]);
            } else if ($v['type'] == 'dir') {
                $dir = $v['dirname'] . '/' . $val[1];
                $this->deldir($dir);
            }
        }
        echo json_encode("yes");
    }

    private function deldir($dir) {
        //先删除目录下的文件：
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
}

