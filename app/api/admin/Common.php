<?php declare(strict_types=1);
namespace app\api\admin;
defined('IN_SYSTEM') or die('Access Denied');
use app\system\admin\Base;
use plugins\builder\builder;
class Common extends Base
{
    protected static $error;
    protected static $ds = DIRECTORY_SEPARATOR;
    protected static $appTag = 'api_module';

    protected function initialize()
    {
        checkPluginDepends(['builder']);
        $this->buiderObj = new builder($this);
        $this->configs = configs('api');
    }

    public function getError()
    {
        return self::$error;
    }

}