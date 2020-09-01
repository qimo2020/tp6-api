<?php declare(strict_types=1);
namespace app\api\middleware;
defined('IN_SYSTEM') or die('Access Denied');
class UserAuth
{
    public function handle($request, \Closure $next, $app)
    {
        $headers = \think\facade\Request::header();
        if(!isset($headers['timestamp']) || !isset($headers['rand'])){
            return json(['code'=>0, 'msg'=>'缺少公共请求参数']);
        }
        if(strtotime(date('Y-m-d H:i:s', (int)$headers['timestamp'])) != $headers['timestamp']) {
            return json(['code'=>0, 'msg'=>'请求参数格式错误']);
        }
        $type = config('api.user_auth_type');
        if($type == 'local'){
            if(!isset($headers['uuid']) || !isset($headers['user_token'])){
                return json(['code'=>0, 'msg'=>'TOKEN参数错误']);
            }
            $obj = new \app\api\lib\UserToken($headers);
            if(!$obj->check()){
                return json(['code'=>0, 'msg'=>$obj->getError()]);
            }
        }else{
            $result = runHook($type, $headers, true);
            foreach ($result as $v){
                if (0 == $v['code']){
                    return json(['code'=>0, 'msg'=>$v['msg']]);
                }
            }
        }
        return $next($request);
    }
} 