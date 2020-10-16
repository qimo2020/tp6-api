<?php declare(strict_types=1);
namespace app\api\model;
defined('IN_SYSTEM') or die('Access Denied');
use think\Model;
class ApiAction extends Model
{
    protected static $appTag = 'api_module';

    public static function items($cid){
        $tag = 'api_actions_'.$cid;
        $actions = cache($tag);
        if(!$actions){
            $actions = [];
            $actionArr = self::where(['status'=>1, 'cid'=>$cid])->order('sort')->select()->toArray();
            foreach ($actionArr as $val){
                $actions[] = $val;
            }
            cache($tag, $actions, null, self::$appTag);
        }
        return $actions;
    }
}