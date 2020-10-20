<?php declare(strict_types=1);
namespace app\api\validate;
use think\Validate;
class ApiParam extends Validate
{
    protected $rule = [
        'aid|所属方法' => 'require',
        'title|参数名称' => 'require',
        'name|参数名' => 'require',
        'data_type|数据类型' => 'require',
    ];

}
