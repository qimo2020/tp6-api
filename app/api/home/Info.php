<?php declare(strict_types=1);
namespace app\api\home;
defined('IN_SYSTEM') or die('Access Denied');
use app\api\lib\Doc;
use app\api\model\ApiAction;
use app\api\model\ApiApp;
use app\api\model\ApiController;
class Info extends Base
{
    private static $appTag = 'api_module';

    public function index()
    {
        if (!$this->request->isPost()) {
            return json(['code' => 0]);
        }
        $post = $this->request->post();
        if (empty($post) || !isset($post['app_id'])) {
            return json(['code' => 0]);
        }
        $defaultId = 0;
        $sidebars = [];
        $controllers = ApiController::items($post['app_id']);
        if ($controllers) {
            foreach ($controllers as $k => $v) {
                $sidebars[$k]['title'] = $v['title'];
                $actions = ApiAction::items($v['id']);
                if ($actions) {
                    foreach ($actions as $key => $val) {
                        $sidebars[$k]['child'][$key]['title'] = $val['title'];
                        $sidebars[$k]['child'][$key]['link'] = $val['id'];
                        if ($val['doc_def']) {
                            $defaultId = $val['id'];
                        }
                    }
                }
            }
        }
        $params = $this->request->param();
        $app = ApiApp::first($params['app_id'] ?? 1);
        $iid = !empty($params) && isset($params['iid']) ? $params['iid'] : $defaultId;
        return json(['code' => 1, 'data' => $sidebars, 'iid' => $iid, 'version' => $app['version'], 'params' => $this->request->param()]);
    }

    public function detail()
    {
        if (!$this->request->isPost()) {
            return json(['code' => 0]);
        }
        $post = $this->request->post();
        if (empty($post) || !isset($post['iid'])) {
            return json(['code' => 0]);
        }
        $return = ['code' => 1];
        $action = ApiAction::find($post['iid']);
        if ($action !== null) {
            $return['iid'] = $post['iid'];
            $apps = ApiApp::items();
            $controller = ApiController::find($action->cid);
            if (null === $controller) {
                return json(['code' => 0, 'test' => $controller]);
            }
            $class = $method = '';
            foreach ($apps as $v) {
                if ($controller->app_id == $v['id']) {
                    $version = $controller->version ? $controller->version . '\\' : '';
                    $class = $v['name'] . '\\api\\' . $version . ucfirst($controller->name);
                    $method = $action->name;
                    break;
                }
            }
            if (!$method) {
                return json(['code' => 0]);
            }
            $this->doc = new Doc();
            $return['content'] = $this->doc->getlist($class, $method);
        } else {
            $return['code'] = 0;
        }
        return json($return);
    }

    public function getSign()
    {
        if(!$this->request->isPost()){
            return json(['code' => 0, 'msg' => '非法请求']);
        }
        $member = session('member');
        if (!$member || !isset($member['uid'])) {
            return json(['code' => 0, 'msg' => '未登陆']);
        }
        $ids = configs('api')['test_member'];
        if (!$ids || !in_array($member['uid'], explode(',', $ids))) {
            return json(['code' => 0, 'msg' => '该用户未授权']);
        }
        try {
            $headers = request()->header();
            $pubParams = \app\api\lib\ApiSign::params();
            if ($pubParams) {
                $publics = [];
                foreach ($pubParams as $v) {
                    if ($v['name'] != 'sign' && (!isset($headers[$v['name']]) || empty($headers[$v['name']]))) {
                        return json(['code' => 0, 'msg' => $v['name'] . '不能为空']);
                    }
                    if ($v['name'] != 'sign') {
                        $publics[$v['name']] = $headers[$v['name']];
                    }
                }
            }
            $params = array_filter($this->request->param());
            $appId = $params['app_id'];
            unset($params['app_id']);
            if (empty(array_filter($params))) {
                return json(['code' => 0, 'msg' => '请求参数不能空']);
            }
            foreach ($publics as $k => $v) {
                $params[$k] = $v;
            }
            $params['ip'] = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : getClientIp();
            $appInfo = ApiApp::first($appId);
            $sign = \hi\Sign::getSign($params, $appInfo['api_secret_key'], true);
            return json(['code' => 1, 'sign' => $sign]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    public function getToken()
    {
        if(!$this->request->isPost()){
            return json(['code' => 0, 'msg' => '非法请求']);
        }
        $member = session('member');
        if (!$member || !isset($member['uid'])) {
            return json(['code' => 0, 'msg' => '未登陆']);
        }
        $ids = configs('api')['test_member'];
        if (!$ids || !in_array($member['uid'], explode(',', $ids))) {
            return json(['code' => 0, 'msg' => '该用户未授权']);
        }
        try {
            $headers = request()->header();
            $params = \app\api\lib\UserToken::params();
            if ($params) {
                foreach ($params as $v) {
                    if ($v['name'] != 'token' && (!isset($headers[$v['name']]) || empty($headers[$v['name']]))) {
                        return json(['code' => 0, 'msg' => $v['name'] . '不能为空']);
                    }
                }
            }
            if ($member['uuid'] != $headers['uuid']) {
                return json(['code' => 0, 'msg' => 'UUID无效']);
            }
            $tag = 'hi_token_' . $headers['client'] . $headers['uuid'];
            $token = cache($tag);
            if (!$token) {
                $token = randomStr(rand(80, 100), 8);
                $expire = config('api.user_token_expire');
                cache($tag, $token, ['expire' => $expire * 60 * 60], self::$appTag);
            }
            $token = md5($token . $headers['timestamp'] . $headers['nonce']);
            return json(['code' => 1, 'token' => $token]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage().$e->getFile().$e->getLine()]);
        }
    }

}
