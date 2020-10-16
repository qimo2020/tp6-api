<?php declare(strict_types=1);
namespace app\api\home;
defined('IN_SYSTEM') or die('Access Denied');
use app\api\model\ApiApp;
class Index extends Base
{
    public function index(){
        $apps = ApiApp::where('status', 1)->select();
        $this->assign('apps', $apps);
        return $this->view();
    }
}
