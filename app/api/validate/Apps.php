<?php declare(strict_types=1);
namespace app\api\validate;
use think\Validate;
class Apps extends Validate
{
    protected $rule = [
        'title|应用名称' => 'require',
        'name|应用名' => 'require',
        'api_secret_key|签名秘钥' => 'require',
    ];

    protected $message = [

    ];


}
