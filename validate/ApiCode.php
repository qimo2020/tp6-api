<?php declare(strict_types=1);
namespace app\api\validate;
use think\Validate;
class ApiCode extends Validate
{
    protected $rule = [
        'aid|所属方法' => 'require',
        'title|返回码说明' => 'require',
        'code|返回码' => 'require',
    ];

}
