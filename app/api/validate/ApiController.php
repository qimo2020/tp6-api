<?php declare(strict_types=1);
namespace app\api\validate;
use think\Validate;
class ApiController extends Validate
{
    protected $rule = [
        'app_id|所属应用' => 'require',
        'title|控制器名称' => 'require',
        'name|控制器名' => 'require',
    ];

}
