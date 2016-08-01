<?php
/**
 * Created by PhpStorm.
 * User: kong
 * Date: 2016/7/29
 * Time: 15:58
 */

namespace Home\Model;


class FinderModel
{
    private $root = './Public/root/'; //根目录

    /**
     * 读取$path路径下的项
     * @param $path
     * @return mixed
     */
    public function ll($path){
        $path = $this->root.$path;  //构造路径
        if(!$this->vailPath($path)) //验证文件夹路径
            return false;
        $handle = opendir($path);
        $items = array();
        while(($item = readdir($handle)) !== false){
            if($item == '.' || $item == '..'){  //忽略'.'与'..'
                continue;
            }else if(is_dir($path . '/' . $item)){
                $items['dir'][] = $this->info($path . '/' . $item);
            }else{
                $items['file'][] = $this->info($path . '/' . $item);
            }
        }
        closedir($handle);
        return $items;
    }

    private function vailPath($path){
        $path = ltrim($path);
        $path = rtrim($path);
        if(stripos($path, '..'))    //不得包含'..'
            return false;
        if(!is_dir($path))          //必须是文件夹
            return false;
        return true;
    }

    /**
     * 返回文件大小
     * @param $path
     * @return string
     */
    private function filesize($path){
        $units = array('B', 'KB', 'MB', 'GB');
        $size = \filesize($path);
        $n = 0;
        while(($size/1024) >= 1 && $n <= 2){
            $size = 1.0 * $size / 1024;
            $n++;
        }
        switch ($n){
            case 1:
                $size = round($size);
                break;
            case 2:
                $size = round($size, 1);
                break;
            case 3:
                $size = round($size, 2);
                break;
        }
        return $size.$units[$n];
    }

    /**
     * 文件或文件夹信息
     * @param $path
     * @return array
     */
    private function info($path){
        $res = array();
        $info = pathinfo($path);
        $res['dirname']     =   $info['dirname'];   //文件夹路径
        $res['basename']    =   $info['basename'];  //文件名或文件夹名
        $res['mtime']       =   date('Y-m-d H:i:s',filemtime($path));   //修改时间
        if(is_file($path)){
            $res['type'] = 'file';
            $res['ext'] = $info['extension'];   //扩展名
            $res['size'] = $this->filesize($path);
        }else{
            $res['type'] = 'dir';
            $res['size'] = '-';
        }
        return $res;
    }
}