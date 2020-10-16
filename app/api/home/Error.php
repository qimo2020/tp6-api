<?php declare(strict_types=1);
namespace app\api\home;
defined('IN_SYSTEM') or die('Access Denied');
use app\api\model\ApiApp;
class Error extends Base
{
    public function __call($method, $args)
    {
        $app = $this->request->controller();
        $app = ApiApp::where('name', $app)->field('id,version,title')->find();
        if($app === null){
            return redirect('/api');
        }
        $this->assign('app', $app->toArray());
        return $this->view('index/doc');

    }

}
