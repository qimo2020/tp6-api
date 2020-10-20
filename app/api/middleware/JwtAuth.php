<?php declare(strict_types=1);
namespace app\api\middleware;
defined('IN_SYSTEM') or die('Access Denied');
class JwtAuth
{
    public function handle($request, \Closure $next, $app)
    {
        $headers = \think\facade\Request::header();
        $token = trim($headers['token']);
        if(!isset($token) || !$token){
            return json(['code'=>0, 'msg'=>'缺少TOKEN']);
        }
        $jwt = new \app\api\lib\JwtToken();
        if(false === $jwt::check($token)){
            return json(['code'=>0, 'msg'=>$jwt::getError()]);
        }
        return $next($request);
    }
} 