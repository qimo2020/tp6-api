<?php declare(strict_types=1);
namespace app\api\model;
defined('IN_SYSTEM') or die('Access Denied');
use think\Model;
class ApiController extends Model
{
    protected static $appTag = 'api_module';

    public static function alls(){
        $tag = 'api_controller';
        $items = cache($tag);
        if(!$items){
            $items = self::order('sort')->select()->toArray();
            cache($tag, $items, null, self::$appTag);
        }
        return $items;
    }

    public static function items($appid){
        $controllers = self::alls();
        $items = [];
        if($controllers){
            foreach($controllers as $v){
                if($v['app_id'] == $appid){
                    $items[] = $v;
                }
            }
        }
        return $items;
    }
}