<?php declare(strict_types=1);

namespace app\api\lib;
class UserToken
{
    private $data = [];
    private static $error = '';
    protected static $appTag = 'api_module';

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function check()
    {
        self::$error = 'TOKEN无效';
        if(!isset($this->data['client'])){
            self::$error = '客户端类型参数错误';
            return false;
        }
        //注意: $tokenInfo里面存有token和uid, 而uid可用于查询用户
        $tag = 'token_' . $this->data['client'] .'_'. $this->data['uuid'];
        $tokenInfo = cache($tag);
        if ($tokenInfo) {
            if ($this->data['token'] != md5($tokenInfo['token'] . $this->data['timestamp'] . $this->data['nonce'])) {
                self::$error = $tokenInfo['token'] .'-'. $this->data['timestamp'] .'-'. $this->data['nonce'];
                return false;
            }
            if (config('api.user_token_keep') && $expire = config('api.user_token_expire')) {
                cache($tag, $tokenInfo, ['expire' => $expire * 60 * 60], self::$appTag);
            }
            return true;
        }
        self::$error = 'TOKEN无效';
        return false;
    }

    public static function params(){
        $result[] = ['name'=>'token','data_type'=>'string','is_need'=>1,'def_val'=>'','intro'=>'Auth鉴权'];
        return $result;
    }

    public static function getError()
    {
        return self::$error;
    }

}