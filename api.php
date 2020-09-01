<?php
namespace app\api;
defined('IN_SYSTEM') or die('Access Denied');
use app\common\controller\Module;
class api extends Module
{
    /**
     * 安装前的业务处理，可在此方法实现，默认返回true
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 安装后的业务处理，可在此方法实现，默认返回true
     * @return bool
     */
    public function installAfter()
    {
        return true;
    }
    /**
     * 升级前
     * @return mixed
     */
    public function upgrade(){
        return true;
    }
    /**
     * 升级后
     * @return mixed
     */
    public function upgradeAfter(){
        return true;
    }
    /**
     * 卸载前的业务处理，可在此方法实现，默认返回true
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    /**
     * 卸载后的业务处理，可在此方法实现，默认返回true
     * @return bool
     */
    public function uninstallAfter()
    {
        return true;
    }

}