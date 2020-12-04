<?php declare(strict_types=1);
namespace app\api\middleware;
defined('IN_SYSTEM') or die('Access Denied');
use app\api\model\ApiParam;
class ParamCheck
{
    public function handle($request, \Closure $next, $id)
    {
        $params = $request->param();
        $headers = \think\facade\Request::header();
        if (!$data = ApiParam::items($id[1])) {
            return $next($request);
        }
        if ($data[0]['version']){
            unset($params[$data[0]['version']]);
            request()->setParamVal([$data[0]['version']=>'']);
        }
        //$auths = array_column($data, 'name');
        $paramKeys = array_keys($params);
//        foreach ($paramKeys as $v) {
//            if (!in_array($v, $auths) && !array_key_exists($v, $headers)) {
//                return json(['result' => 0, 'error_code'=>40001, 'msg' => '多余参数['.$v.']']);
//            }
//        }
        foreach ($data as $v) {
            if($v['param_type'] == 0){
                if (!in_array($v['name'], $paramKeys) && $v['is_need']) {
                    return json(['result' => 0, 'error_code'=>40001, 'msg' => '缺少参数[' . $v['name'] . ']']);
                }
                if (!empty($v['rule']) && !preg_match($v['rule'], $params[$v['name']])) {
                    return json(['result' => 0, 'error_code'=>40001, 'msg' => '参数[' . $v['name'] . ']格式错误']);
                }
                if(empty($params[$v['name']]) && !empty($v['def_val'])){
                    request()->setParamVal([$v['name']=>$v['def_val']]);
                }
            }
        }
        return $next($request);
    }


} 