<?php declare(strict_types=1);
namespace app\api\middleware;
defined('IN_SYSTEM') or die('Access Denied');
class UserAuth
{
    public function handle($request, \Closure $next, $app)
    {
        $headers = \think\facade\Request::header();
        if(!isset($headers['timestamp']) || !isset($headers['nonce'])){
            return json(['result' =>0, 'error_code'=>10001, 'msg'=>'缺少公共请求参数']);
        }
        if(strtotime(date('Y-m-d H:i:s', (int)$headers['timestamp'])) != $headers['timestamp']) {
            return json(['result' =>0, 'error_code'=>10001, 'msg'=>'请求参数格式错误']);
        }
        if(!isset($headers['uuid']) || !isset($headers['token'])){
            return json(['result' =>0, 'error_code'=>10001, 'msg'=>'TOKEN参数错误']);
        }
        $obj = new \app\api\lib\UserToken($headers);
        if(!$obj->check()){
            return json(['result' =>0, 'error_code'=>10001, 'msg'=>$obj->getError()]);
        }
        return $next($request);
    }
} 