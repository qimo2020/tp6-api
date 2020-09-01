<?php declare(strict_types=1);
namespace app\api\model;
defined('IN_SYSTEM') or die('Access Denied');
use think\Model;
class ApiApp extends Model
{
    protected static $appTag = 'api_module';

    public static function items(){
        $tag = 'api_apps';
        $apps = cache($tag);
        if(!$apps){
            $apps = self::select()->toArray();
            cache($tag, $apps, null, self::$appTag);
        }
        return $apps;
    }

    public static function first($id){
        $apps = self::items();
        foreach ($apps as $v){
            if($v['id'] == $id){
                return $v;
            }
        }
        return false;
    }
}