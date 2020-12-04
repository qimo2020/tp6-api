<?php declare(strict_types=1);
namespace app\api\middleware;
defined('IN_SYSTEM') or die('Access Denied');
use app\api\model\ApiApp;
class ApiAuth
{
    public function handle($request, \Closure $next, $app)
    {
        $headers = \think\facade\Request::header();
        if(!isset($headers['timestamp'])){
            return json(['result' =>0, 'error_code'=>20001, 'msg'=>'缺少公共请求参数']);
        }
        if(strtotime(date('Y-m-d H:i:s', (int)$headers['timestamp'])) != $headers['timestamp']) {
            return json(['result' =>0, 'error_code'=>20001, 'msg'=>'请求参数格式错误']);
        }
        $pubParams = \app\api\lib\ApiSign::params();
        if($pubParams){
            foreach ($pubParams as $v){
                if($v['is_need'] && !isset($headers[$v['name']]) || empty($headers[$v['name']])){
                    return json(['result' =>0, 'error_code'=>20001, 'msg'=>$v['name'].'不能为空']);
                }
            }
        }
        $params = request()->param();
        $appInfo = ApiApp::first($app[0]);
        $obj = new \app\api\lib\ApiSign($params, $appInfo);
        if(!$check = $obj->check()){
            return json(['result' =>0, 'error_code'=>20001, 'msg'=>$obj->getError()]);
        }
        return $next($request);
    }
} 