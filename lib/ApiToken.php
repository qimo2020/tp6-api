<?php declare(strict_types=1);
namespace app\api\lib;
class ApiToken
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
        $newParams['ip'] = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : getClientIp();
        $newParams = array_merge($newParams, $this->data);
        $newParams = array_filter($newParams);
        if(false === $info = $this->getToken()){
            return false;
        }
        $data = $newParams;
        unset($newParams['sign']);
        $sign = \hi\Sign::getSign($newParams, $info['token'], true);
        if($data['sign'] != $sign){
            self::$error = '校验失败';
            return false;
        }
        return true;
    }

    public static function params(){
        $result = [
            ['name'=>'timestamp','data_type'=>'string','is_need'=>1,'def_val'=>'','intro'=>'当前时间戳'],
            ['name'=>'rand','data_type'=>'string','is_need'=>1,'def_val'=>'','intro'=>'8位长度随机数'],
            ['name'=>'format','data_type'=>'string','is_need'=>0,'def_val'=>'json','intro'=>'响应数据格式，json或xml, 默认json'],
            ['name'=>'sign','data_type'=>'string','is_need'=>1,'def_val'=>'','intro'=>'签名'],
        ];
        return $result;
    }
    /**
     * 返回接口数据组,必包含token和expire字段
     */
    public function getToken(){
        $tag = 'api_token_app_'.$this->config['app_id'];
        $result = cache($tag);
        if(!$result){
            $obj = new \app\api\model\ApiToken();
            $infoObj = $obj->where('app_id', $this->config['app_id'])->find();
            if(null === $infoObj){
                self::$error = '该应用不存在或未创建API';
                return false;
            }
            if($infoObj->expire <= time()){
                self::$error = 'TOKEN已失效';
                return false;
            }
            $cache = ['token'=>$infoObj->token, 'expire'=>$infoObj->expire];
            cache($tag, $cache, null, self::$appTag);
            return $cache;
        }
        return $result;
    }

    /**
     * 创建并缓存接口数据, 数据必包含token和expire字段
     */
    public function tokenCreate(){
        try{
            $tag = 'api_token_app_'.$this->config['app_id'];
            $result = cache($tag);
            if(!$result){
                $obj = new \app\api\model\ApiToken();
                $data['app_id'] = $this->config['app_id'];
                $data['token'] = $result['token'] = randomStr(rand(64, 80), 8);
                $day = configs('api')['token_expire'];
                $data['expire'] = $result['expire'] = strtotime($day . " day");
                cache($tag, $data, null, self::$appTag);
                $obj->create($data);
            }
            return $result;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public static function getError(){
        return self::$error;
    }

}