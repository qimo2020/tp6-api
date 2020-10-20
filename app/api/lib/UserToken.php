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
        $tag = 'hi_token_' . $this->data['client'] .'_'. $this->data['uuid'];
        $token = cache($tag);
        if ($token) {
            if ($this->data['token'] != md5($token . $this->data['timestamp'] . $this->data['nonce'])) {
                return false;
            }
            if (config('api.user_token_keep') && $expire = config('api.user_token_expire')) {
                cache($tag, $token, ['expire' => $expire * 60 * 60], self::$appTag);
            }
            return true;
        }
        return false;
    }

    public static function params(){
        $result[] = ['name'=>'token','data_type'=>'string','is_need'=>1,'def_val'=>'','intro'=>'签名'];
        return $result;
    }

    public static function getError()
    {
        return self::$error;
    }

}