<?php declare(strict_types=1);
namespace app\api\validate;
use think\Validate;
class ApiAction extends Validate
{
    protected $rule = [
        'cid|所属控制器' => 'require',
        'title|方法名称' => 'require',
        'name|方法名' => 'require',
        'request_type|请求类型' => 'require',
    ];

}
