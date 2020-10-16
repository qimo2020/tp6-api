<?php declare(strict_types=1);

namespace app\api\admin;
defined('IN_SYSTEM') or die('Access Denied');

use app\api\model\ApiAction;
use app\api\model\ApiApp;
use app\api\model\ApiController;
use app\api\model\ApiParam;

class Build extends Common
{

    public function createApi()
    {
        try {
            $params = $this->request->param();
            if (isset($params['id'])) {
                $controller = ApiController::where(['id' => $params['id'], 'status' => 1])->find()->toArray();
                if ($controller === null) {
                    return $this->response(0, '控制器未开启或不存在');
                }
                $appId = $controller['app_id'];
            } else if (isset($params['app_id'])) {
                $controller = ApiController::where(['app_id' => $params['app_id'], 'status' => 1])->select()->toArray();
                if (empty($controller)) {
                    return $this->response(0, '请至少先创建或开启一个控制器');
                }
                $appId = $params['app_id'];
            }

            $app = ApiApp::where(['id' => $appId, 'status' => 1])->find();
            if ($app === null) {
                return $this->response(0, '应用未开启或不存在');
            }
            $path = root_path() . ($app->app_type ? 'plugins' : 'app') . self::$ds . $app->name;
            if (count($controller) == count($controller, 1)) {
                $actions = ApiAction::where('cid', $params['id'])->select()->toArray();
                if (!$actions) {
                    return $this->response(0, '请先创建方法');
                }
                $controllerName = $controller['map_name'] ?: $controller['name'];
                $applications = [];
                foreach ($actions as $k => $v) {
                    $applications[0][$k] = $v;
                    $applications[0][$k]['app_id'] = $controller['app_id'];
                    $applications[0][$k]['controller'] = $controller['name'];
                    $applications[0][$k]['version'] = $controller['version'];
                    $applications[0][$k]['controller_map'] = $controllerName;
                    $applications[0][$k]['controller_map'] = $controllerName;
                    $applications[0][$k]['params'] = null === ApiParam::where('aid', $v['id'])->find() ? 0 : 1;
                }
            } else {
                foreach ($controller as $key => $val) {
                    $actions = ApiAction::where('cid', $val['id'])->select()->toArray();
                    if ($actions) {
                        $controllerName = $val['map_name'] ?: $val['name'];
                        foreach ($actions as $k => $v) {
                            $applications[$key][$k] = $v;
                            $applications[$key][$k]['app_id'] = $val['app_id'];
                            $applications[$key][$k]['controller'] = $val['name'];
                            $applications[$key][$k]['version'] = $val['version'];
                            $applications[$key][$k]['controller_map'] = $controllerName;
                            $applications[$key][$k]['controller_map'] = $controllerName;
                            $applications[$key][$k]['params'] = null === ApiParam::where('aid', $v['id'])->find() ? 0 : 1;
                        }
                    }
                }
            }
            //生成文件
            if (!$this->createFile($app->name, $applications, $path)) {
                return $this->response(0, self::$error);
            }
            //生成应用中间件
            if (!$this->createMiddleware($path)) {
                return $this->response(0, self::$error);
            }
            //生成路由
            if (!$this->createRoute($applications, $path)) {
                return $this->response(0, self::$error);
            }
            $obj = ApiApp::update(['version' => time(), 'id'=>$app->id]);
            return $this->response(0, '已生成接口');
        } catch (\Exception $e) {
            return $this->response(0, $e->getMessage().$e->getFile().$e->getLine());
        }
    }

    public function backup(){
        try{
            $params = $this->request->param();
            if (isset($params['id'])) {
                $controller = ApiController::where(['id' => $params['id']])->find()->toArray();
                if ($controller === null) {
                    return $this->response(0, '控制器不存在');
                }
                $appId = $controller['app_id'];
            } else if (isset($params['app_id'])) {
                $controller = ApiController::where(['app_id' => $params['app_id'], 'status' => 1])->select()->toArray();
                if (empty($controller)) {
                    return $this->response(0, '请至少先创建或开启一个控制器');
                }
                $appId = $params['app_id'];
            }
            $app = ApiApp::where(['id' => $appId])->find();
            if ($app === null) {
                return $this->response(0, '应用不存在');
            }
            $path = root_path() . ($app->app_type ? 'plugins' : 'app') . self::$ds . $app->name;
            $apiDir = $path . self::$ds . 'api';
            if (!is_dir($apiDir)) {
                return $this->response(0, '目录[' . $apiDir . ']不存在');
            }
            if (count($controller) == count($controller, 1)) {
                $actions = ApiAction::where('cid', $params['id'])->select()->toArray();
                if (!$actions) {
                    return $this->response(0, '未创建方法');
                }
                if ($controller['version'] && !is_dir($apiDir = $apiDir . self::$ds . $controller['version'])) {
                    return $this->response(0, '目录[' . $apiDir . ']不存在');
                }
                if (!is_file($file = $apiDir . self::$ds . ucfirst($controller['name']) . '.php')) {
                    return $this->response(0, '文件[' . $file . ']不存在');
                }
                if(false === $content = file_get_contents($file)){
                    return $this->response(0, '读取文件[' . $file . ']失败,请检查目录权限');
                }
                foreach ($actions as $v){
                    $preg = preg_match('/'.$v['name'].'\(.*?\)\{(.*?json\((.*?)\);).*?\}\n\n/is', $content, $code);
                    if($preg && isset($code[1]) && !empty($code[1])){
                        ApiAction::where('id', $v['id'])->update(['codes'=>$code[1]]);
                    }else{
                        return $this->response(0, $file.'['.$v['name'].']方法备份失败');
                    }
                }
            }else{
                foreach ($controller as $key => $val) {
                    $actions = ApiAction::where('cid', $val['id'])->select()->toArray();
                    if (!$actions) {
                        return $this->response(0, '未创建方法');
                    }
                    if ($val['version'] && !is_dir($apiDir = $apiDir . self::$ds . $val['version'])) {
                        return $this->response(0, '目录[' . $apiDir . ']不存在');
                    }
                    if (!is_file($file = $apiDir . self::$ds . ucfirst($val['name']) . '.php')) {
                        return $this->response(0, '文件[' . $file . ']不存在');
                    }
                    if(false === $content = file_get_contents($file)){
                        return $this->response(0, '读取文件[' . $file . ']失败,请检查目录权限');
                    }
                    foreach ($actions as $v){
                        $preg = preg_match('/'.$v['name'].'\(.*?\)\{(.*?json\((.*?)\);).*?\}\n\n/is', $content, $code);
                        if($preg && isset($code[1]) && !empty($code[1])){
                            ApiAction::where('id', $v['id'])->update(['codes'=>$code[1]]);
                        }else{
                            //return $this->response(0, $file.'['.$v['name'].']方法备份失败');
                        }
                    }
                }
            }
            return $this->response(1, '备份完成');
        } catch (\Exception $e) {
            return $this->response(0, $e->getMessage());
        }

    }

    protected function createMiddleware($path)
    {
        try {
            if (!is_dir($configDir = $path . self::$ds . 'config')) {
                if (false === mkdir($configDir, 0755, true)) {
                    self::$error = '创建目录[' . $configDir . ']失败';
                    return false;
                }
            }
            if (!is_file($mwFile = $configDir . self::$ds . 'middleware.php')) {
                $mwConfigs = config('middleware');
                if (!isset($mwConfigs['alias']) || !isset($mwConfigs['alias']['ApiAuth']) || !isset($mwConfigs['alias']['ApiAuth'])) {
                    $mwConfigs['alias']['ApiAuth'] = '\app\api\middleware\ApiAuth::class';
                    $mwConfigs['alias']['UserAuth'] = '\app\api\middleware\UserAuth::class';
                    $mwConfigs['alias']['ParamCheck'] = '\app\api\middleware\ParamCheck::class';
                    $config = repToFileArr($mwConfigs);
                    $code = <<<INFO
<?php
return {$config};
INFO;
                    fileCreate($code, $mwFile);
                }
                return true;
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function createRoute($info, $path)
    {
        try {
            if (!is_dir($routeDir = $path . self::$ds . 'route')) {
                if (false === mkdir($routeDir, 0755, true)) {
                    self::$error = '创建目录[' . $routeDir . ']失败';
                    return false;
                }
            }
            if ($info) {
                foreach ($info as $v) {
                    $str = "<?php\n";
                    $str .= "use think\\facade\\Route;\n\n";
                    if ($v) {
                        $keySortArr = array_column($v, 'sort');
                        array_multisort($keySortArr, SORT_ASC, $v);
                        foreach ($v as $val) {
                            $middleware = $params = '';
                            if ($val['params']) {
                                $middleware .= ",'ParamCheck'";
                            }
                            if ($val['api_auth']) {
                                $middleware .= ",'ApiAuth'";
                            }
                            if ($val['user_auth']) {
                                $middleware .= ",'UserAuth'";
                            }
                            if ($middleware) {
                                if ($val['params']) {
                                    $middleware = ltrim($middleware, ',');
                                    $middleware = '[' . $middleware . "],[" . $val['app_id'] . "," . $val['id'] . "]";
                                } else {
                                    $middleware = trim($middleware, ',');
                                    $middleware = '[' . $middleware . '],[' . $val['app_id'] . "]";
                                }
                                $middleware = '->middleware(' . $middleware . ')';
                            }
                            $str .= "Route::" . $val['request_type'] . "('api/";
                            $str .= ($val['version'] ? ':' . $val['version'] . '/' : '');
                            $str .= $val['controller_map'] . "/" . $val['name'] . "', 'api/";
                            $str .= ($val['version'] ? ':' . $val['version'] . '.' : '');
                            $str .= $val['controller'] . "/" . $val['name'] . "')" . $middleware;
                            if (isset($this->configs['https']) && $this->configs['https']) {
                                $str .= '->https()';
                            }
                            $str .= ";	//" . $val['title'] . ";\n";
                        }
                        fileCreate($str, $routeDir . self::$ds . 'api_' . $v[0]['controller'] . '.php');
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function createFile($app, $info, $path)
    {
        try {
            $apiDir = $path . self::$ds . 'api';
            if (!is_dir($apiDir) && false === mkdir($apiDir, 0755, true)) {
                self::$error = '创建目录[' . $apiDir . ']失败';
                return false;
            }
            if ($info) {
                foreach ($info as $v) {
                    if ($v) {
                        $str = "<?php declare(strict_types=1);\n";
                        $str .= "namespace app\\" . $app . "\\api";
                        $str .= $v[0]['version'] ? "\\" . $v[0]['version'] . ";\n" : ";\n";
                        $str .= "defined('IN_SYSTEM') or die('Access Denied');\n";
                        $str .= "use app\common\controller\Common;\n";
                        $keySortArr = array_column($v, 'sort');
                        array_multisort($keySortArr, SORT_ASC, $v);
                        $str .= "class " . ucfirst($v[0]['controller']) . " extends Common\n{\n\n";
                        $str .= "    public function __call(\$method, \$args){\n";
                        $str .= "       return json(['code'=>0, 'msg'=>'404']);\n";
                        $str .= "    }\n\n";
                        foreach ($v as $val) {
                            $str .= "	/**\n";
                            $str .= " 	* @title " . $val['title'] . "\n";
                            $str .= " 	* @desc " . $val['title'] . "\n";
                            $str .= " 	* @url " . $app . '/api/' . ($val['version'] ? $val['version'] . '/' : '') . $val['controller'] . '/' . $val['name'] ."\n";
                            $str .= " 	* @method " . strtoupper($val['request_type']) . "\n";
                            $pubParams = \app\api\lib\ApiToken::params();
                            if($val['api_auth'] && !empty($pubParams)){
                                foreach ($pubParams as $value) {
                                    $str .= " 	* @public " . $this->dataTypeParse($value['data_type']) . " \$" . $value['name'] . " " . $value['is_need'] . " " . $this->valFilter($value['def_val']) . " " . $this->valFilter($value['intro']) . "\n";
                                }
                            }
                            $pubParams = \app\api\lib\UserToken::params();
                            if($val['user_auth'] && !empty($pubParams)){
                                foreach ($pubParams as $value) {
                                    $str .= " 	* @public " . $this->dataTypeParse($value['data_type']) . " \$" . $value['name'] . " " . $value['is_need'] . " " . $this->valFilter($value['def_val']) . " " . $this->valFilter($value['intro']) . "\n";
                                }
                            }
                            $params = ApiParam::items($val['id']);
                            foreach ($params as $value) {
                                if ($value['param_type'] == 0) {
                                    $str .= " 	* @param " . $this->dataTypeParse($value['data_type']) . " \$" . $value['name'] . " " . $value['is_need'] . " " . $this->valFilter($value['def_val']) . " " . $this->valFilter($value['intro']) . "\n";
                                }
                            }
                            foreach ($params as $value) {
                                if ($value['param_type'] == 1) {
                                    $str .= " 	* @return " . $this->dataTypeParse($value['data_type']) . " \$" . $value['name'] . " " . $value['is_need'] . " " . $this->valFilter($value['def_val']) . " " . $this->valFilter($value['intro']) . "\n";
                                }
                            }
                            $str .= " 	* @test " . $val['test_auth'] . "\n";
                            $str .= " 	*/\n";
                            $str .= "    public function " . $val['name'] . "(){";
                            $str .= !empty($val['codes']) ? $val['codes'] : "\n       return json(['code'=>1, 'msg'=>'" . $app . self::$ds . 'api' . ($val['version'] ? self::$ds . $val['version'] : '') . self::$ds . $val['controller'] . self::$ds . $val['name'] . "']);";
                            $str .= "\n    }\n\n";
                        }
                        $str .= "\n\n}";
                        $apiDir = $path . self::$ds . 'api';
                        if ($v[0]['version'] && !is_dir($apiDir = $apiDir . self::$ds . $v[0]['version'])) {
                            if (false === mkdir($apiDir, 0755, true)) {
                                continue;
                            }
                        }
                        \hi\Dir::delDir($str, $apiDir . self::$ds . ucfirst($val['controller']) . '.php');
                        fileCreate($str, $apiDir . self::$ds . ucfirst($val['controller']) . '.php');
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function dataTypeParse($type){
        switch ($type){
            case 'interger':
                $type = 'int';
                break;
        }
        return $type;
    }

    protected function valFilter($val){
        if(!$val){
            return '-';
        }
        return $val;
    }



}
