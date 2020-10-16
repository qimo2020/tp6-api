<?php

if (!function_exists('fileCreate')) {
    /* 文件生成
    * @param string $type 1 为生成控制器
    */
    function fileCreate($content, $filepath)
    {
        ob_start();
        echo $content;
        $_cache = ob_get_contents();
        ob_end_clean();
        if ($_cache) {
            $File = new \think\template\driver\File();
            $File->write($filepath, $_cache);
        }
    }
}

if (!function_exists('repToFileArr')) {
    /* 转换为可写入文件的数组格式的字符串
    */
    function repToFileArr($array)
    {
        $str = "[\n";
        foreach ($array as $k=>$v){
            $str .= is_numeric($k) ? is_array($v) ? repToFileArr($v).",\n" : $v . ",\n" : "'" . $k . "'=>" . (is_array($v) ? repToFileArr($v) . ",\n" : $v . ",\n");
        }
        $str .= "]";
        return $str;
    }
}