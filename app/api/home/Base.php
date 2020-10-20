<?php declare(strict_types=1);
namespace app\api\home;
defined('IN_SYSTEM') or die('Access Denied');
use app\common\controller\Common;
use think\exception\HttpResponseException;
class Base extends Common
{
    public $messages = [];
    protected static $error;

    protected function initialize()
    {
        parent::initialize();
        $this->configs = configs('api');
    }

    protected function getError(){
        return self::$error;
    }

}