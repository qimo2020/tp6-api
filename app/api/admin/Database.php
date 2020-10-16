<?php declare(strict_types=1);

namespace app\api\admin;
defined('IN_SYSTEM') or die('Access Denied');

use think\exception\HttpException;
use think\facade\Db;
use app\api\model\ApiApp;
use hi\Dir;
class Database extends Common
{
    public function __call($method, $args)
    {
        throw new HttpException(404, 'error');
    }

    public function backup()
    {
        try {
            if ($this->request->isAjax()) {
                $appId = input('app_id');
                if(null === $app = ApiApp::find($appId)){
                    return $this->response(0, '应用['.$app->name.']不存在');
                }
                list($controllerIds, $cXml) = $this->getItems('api_controller', ['app_id' => $appId]);
                list($actionIds, $aXml) = $this->getItems('api_action', [['cid', 'in', $controllerIds]]);
                list($paramIds, $pXml) = $this->getItems('api_param', [['aid', 'in', $actionIds]]);
                list($codeIds, $cdXml) = $this->getItems('api_code', [['aid', 'in', $actionIds]]);
                header("Content-Type: text/xml");
                $controllerXml = $actionXml = $paramXml = $codeXml = "<?xml version=\"1.0\"  encoding=\"utf-8\" ?>\n";
                $controllerXml .= "<xml>\n" . $cXml . "</xml>";
                $actionXml .= "<xml>\n" . $aXml . "</xml>";
                $paramXml .= "<xml>\n" . $pXml . "</xml>";
                $codeXml .= "<xml>\n" . $cdXml . "</xml>";
                $dirName = 'uploads' . self::$ds . 'temp' . self::$ds . 'api' . self::$ds . $app->name.time();
                if (!is_dir($dir = public_path() . $dirName)) {
                    if (false === mkdir($dir, 0755, true)) {
                        return $this->response(0, '创建目录[' . $dir . ']失败');
                    }
                }
                $res1 = file_put_contents($dir . self::$ds . 'api_controller.xml', $controllerXml);
                $res2 = file_put_contents($dir . self::$ds . 'api_action.xml', $actionXml);
                $res3 = file_put_contents($dir . self::$ds . 'api_param.xml', $paramXml);
                $res4 = file_put_contents($dir . self::$ds . 'api_code.xml', $codeXml);
                if ($res1 && $res2 && $res3 & $res4) {
                    $archive = new \hi\PclZip();
                    $archive->PclZip($dir . '.zip');
                    $pcl = $archive->create('./' . $dirName . '/api_controller.xml,' . './' . $dirName . '/api_action.xml,' . './' . $dirName . '/api_param.xml,' . './' . $dirName . '/api_code.xml', PCLZIP_OPT_REMOVE_PATH, $dirName . '/', PCLZIP_OPT_ADD_PATH, 'api');
                    if (!$pcl) {
                        return $this->response(0, $archive->errorInfo(true));
                    }
                    Dir::delDir($dir);
                }
                return $this->response(1, '已生成', '', ['file'=>str_replace(public_path(), '/', $dir).'.zip']);
            }
        } catch (\Exception $e) {
            return $this->response(0, $e->getMessage());
        }
        return $this->view();
    }

    public function delzip()
    {
        $zip = input('zip');
        $file = str_replace('/uploads', './uploads', $zip);
        if(!is_file($file)){
            return $this->response(0, '文件不存在');
        }
        @unlink($file);
        return $this->response(1, '删除成功');
    }

    private function getItems($table, $where)
    {
        $items = Db::name($table)->where($where)->select()->toArray();
        $fields = $this->getFields($table);
        $itemsIds = [];
        $itemsXml = '';
        if ($items) {
            foreach ($items as $k => $v) {
                $itemsIds[] = $v['id'];
                $itemsXml .= "\t<items id='" . $k . "'>\n";
                foreach ($fields as $filed) {
                    $itemsXml .= "\t<item id='" . $filed . "'><![CDATA[" . $v[$filed] . "]]></item>\n";
                }
                $itemsXml .= "\t</items>\n";
            }
        }
        return [$itemsIds, $itemsXml];

    }

    private function getFields($table)
    {
        $prefix = config('database.connections.mysql.prefix');
        $res = Db::query("SELECT COLUMN_NAME FROM information_schema.columns WHERE TABLE_NAME='{$prefix}" . $table . "'");
        return array_column($res, "COLUMN_NAME");
    }

    public function import()
    {
        try{
            if ($this->request->isPost()) {
                $appId = input('app_id');
                $app = Db::name('api_app')->where('id', $appId)->find();
                if(!$app){
                    return $this->response(0, json_encode($app));
                }
                $_file = $this->request->param('file');
                if (empty($_file)) {
                    return $this->response(0, '请上传数据文件');
                }
                $file = realpath('.' . $_file);
                if (!file_exists($file)) {
                    return $this->response(0, '上传文件无效');
                }
                $decomPath = '.' . trim($_file, '.zip');
                $archive = new \hi\PclZip();
                $archive->PclZip($file);
                if (!$archive->extract(PCLZIP_OPT_PATH, $decomPath, PCLZIP_OPT_REPLACE_NEWER)) {
                    Dir::delDir($decomPath);
                    return $this->response(0,'导入失败(' . $archive->error_string . ')');
                }
                if (!is_dir($decomPath . self::$ds . 'api')) {
                    Dir::delDir($decomPath);
                    return $this->response(0,'导入失败，文件包不完整(-1)');
                }
                @unlink($file);
                $this->xmlPath = $decomPath;
                $cids = $this->importData($app['id'], 'api_controller', 'id', 'app_id');
                if($cids){
                    $aids = $this->importData($cids, 'api_action', 'id', 'cid');
                }
                if(isset($aids) && $aids){
                    $this->importData($aids, 'api_param', 'id', 'aid');
                    $this->importData($aids, 'api_code', 'id', 'aid');
                }
                return $this->response(0, '导入成功', (string)url('', ['app_id'=>$appId]));
            }
        }catch (\Exception $e) {
            return $this->response(0, $e->getMessage());
        }
        return $this->view();
    }

    private function importData($ids, $table, $keyFile, $pKeyField){
        Db::startTrans();
        try {
            $items = $this->getXml($table.'.xml');
            if(false === $items){
                return false;
            }
            if(is_array($ids)){
                foreach ($ids as $k=>$v){
                    $del = Db::name($table)->where($pKeyField, $k)->delete();
                }
            }else{
                $del = Db::name($table)->where($pKeyField, $ids)->delete();
            }
            $idsRes = [];
            foreach ($items as $v){
                if(!is_array($ids) && is_numeric($ids)){
                    $v[$pKeyField] = $ids;
                }else if(array_key_exists($v[$pKeyField], $ids)){
                    $v[$pKeyField] = $ids[$v[$pKeyField]];
                }
                $id = $v[$keyFile];
                unset($v[$keyFile]);
                $idsRes[$id] = Db::name($table)->insertGetId($v);
            }
            Db::commit();
            return $idsRes;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    private function getXml($file){
        $content = file_get_contents($this->xmlPath . self::$ds . 'api' . self::$ds . $file);
        $data = xml2array($content);
        if(!$data){
            self::$error = '数据为空';
            return false;
        }
        return $data;
    }

}
