<?php declare(strict_types=1);
namespace app\api\lib;
class ApiSign
{
    private $config = [];
    private $data = [];
    private static $error = '';
    private static $appTag = 'api_module';

    public function __construct($data, $config)
    {
        $this->data = $data;
        $this->config = $config;
    }

    public function check()
    {
        self::$error = '请求参数错误';
        $params = self::params();
        $headers = \think\facade\Request::header();
        foreach ($params as $v){
            $newParams[$v['name']] = $headers[$v['name']];
        }
        $newParams = array_merge($newParams, $this->data);
        $newParams = array_filter($newParams, function($v){
            return is_null($v) || $v === '' ? false : true;
        });
        $data = $newParams;
        unset($newParams['sign']);
        $sign = \hi\Sign::getSign($newParams, $this->config['api_secret_key'], true);
        if($data['sign'] != $sign){
            self::$error = $sign;
            return false;
        }
        return true;
    }

    public static function params(){
        $result = [
            ['name'=>'timestamp','data_type'=>'string','is_need'=>1,'def_val'=>'','intro'=>'当前时间戳'],
            ['name'=>'nonce','data_type'=>'string','is_need'=>1,'def_val'=>'','intro'=>'随机字符串'],
            ['name'=>'format','data_type'=>'string','is_need'=>0,'def_val'=>'json','intro'=>'响应数据格式，json或xml, 默认json'],
            ['name'=>'sign','data_type'=>'string','is_need'=>1,'def_val'=>'','intro'=>'签名'],
        ];
        return $result;
    }

    public static function getError(){
        return self::$error;
    }

}