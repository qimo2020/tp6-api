<?php declare(strict_types=1);
namespace app\api\model;
defined('IN_SYSTEM') or die('Access Denied');
use think\Model;
use think\facade\Db;
class ApiParam extends Model
{
    protected $datetime_format = false;

    protected static $appTag = 'api_module';

    public static function items($aid){
        $tag = 'api_param_'.$aid;
        $params = cache($tag);
        if(!$params){
            $action = Db::name('api_action')->alias('a')->leftJoin('api_controller c', 'a.cid=c.id')->where('a.id', $aid)->field('c.version')->find();
            $params = self::where(['aid'=>$aid, 'status'=>1])->field('title,name,data_type,param_type,intro,is_need,def_val,rule,"'.$action['version'].'" as version')->order('sort')->select()->toArray();
            cache($tag, $params, null, self::$appTag);
        }
        return $params;
    }

}