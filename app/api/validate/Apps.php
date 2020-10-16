<?php declare(strict_types=1);
namespace app\api\validate;
use think\Validate;
class Apps extends Validate
{
    protected $rule = [
        'title|应用名称' => 'require',
        'name|应用名' => 'require',
        'app_id|应用id' => 'require',
        'app_secret|应用秘钥' => 'require',
        'callback_uri|授权回调' => 'require',
    ];

    protected $message = [

    ];


}
