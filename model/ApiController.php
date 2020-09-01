<?php declare(strict_types=1);
namespace app\api\model;
defined('IN_SYSTEM') or die('Access Denied');
use think\Model;
class ApiController extends Model
{
    protected static $appTag = 'api_module';

    public static function items($appid){
        $tag = 'api_controller';
        $items = cache($tag);
        if(!$items){
            $items = self::where(['status'=>1, 'app_id'=>$appid])->order('sort')->select()->toArray();
            cache($tag, $items, null, self::$appTag);
        }
        return $items;
    }
}