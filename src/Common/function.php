<?php
/**
 * get lion config
 * @param $key
 * @return mixed
 */
function lion_config($key){
    $vendor_path=dirname(dirname(dirname(dirname(__DIR__))));
    $file=$vendor_path.'/config/LionConfig.php';
    if(file_exists($file)){
        $config=require $file;
    }else{
        exit('config file not exist');
    }
    return $config[$key];
}

/**
 * get unique id
 * @return string
 */
function lion_get_uniqid(){
    $prefix=mt_rand();
    return crc32(uniqid($prefix,true));
}