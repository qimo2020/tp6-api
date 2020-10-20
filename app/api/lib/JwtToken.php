<?php declare(strict_types=1);

namespace app\api\lib;

use \Firebase\JWT\JWT;

class JwtToken
{
    private $data = [];
    private static $error = '';
    private static $config;

    public function __construct()
    {
        self::$config = configs('api');
    }

    public static function create($account, $uuid)
    {
        $key = self::$config['jwt_key'];
        $data = [
            "iss"=>$account,  //签发者 可以为空
            "aud"=>"", //面象的用户，可以为空
            "iat" => time(), //签发时间
            "nbf" => time()+100, //在什么时候jwt开始生效  （这里表示生成100秒后才生效）
            "exp" => time()+ self::$config['jwt_expire'] * 3600, //过期时间
            'uuid' => $uuid,
        ];
        return JWT::encode($data, $key,"HS256");
    }

    public function check($token)
    {
        try {
            JWT::$leeway = 60;//当前时间减去60，把时间留点余地
            return JWT::decode($token, self::$config['jwt_key'], ['HS256']);//HS256方式，这里要和签发的时候对应
        } catch(\Firebase\JWT\SignatureInvalidException $e) { //签名不正确
            self::$error = "签名不正确";
            return false;
        }catch(\Firebase\JWT\BeforeValidException $e) { // 签名在某个时间点之后才能用
            self::$error = "token失效";
            return false;
        }catch(\Firebase\JWT\ExpiredException $e) { // token过期
            self::$error = "token失效";
            return false;
        }catch(\Exception $e) { //其他错误
            self::$error = "未知错误";
            return false;
        }
    }

    public static function getError()
    {
        return self::$error;
    }

}