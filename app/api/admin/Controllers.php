<?php declare(strict_types=1);
namespace app\api\admin;
use app\api\model\ApiAction;
use think\facade\Cache;

defined('IN_SYSTEM') or die('Access Denied');
class Controllers extends Common
{
    protected function initialize()
    {
        parent::initialize();
        $this->buiderObj->hiModel = $this->buiderObj->hiValidate = 'ApiController';
    }

    public function index(){
        return $this->buiderObj->_table();
    }

    public function add()
    {
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->_save();
    }

    public function edit(){
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->_save([], 'edit');
    }

    public function buildForm($op = 'add'){
        $apps = \app\api\model\ApiApp::where('status', 1)->select();
        $appList = [];
        foreach($apps as $key=>$app){
            $appList[$app->id] = $app->title;
        }
        $result = $this->buiderObj->buildData();
        $result['buildForm']['hiData']['pop'] = true;
        $result['buildForm']['items'] = [
            [
                'name'=>'app_id',
                'type'=>'select',
                'title'=>'所属应用',
                'tips'=>'',
                'value'=>'',
                'options'=>$appList
            ],
            [
                'name'=>'title',
                'type'=>'input',
                'title'=>'控制器名称',
                'tips'=>'',
                'value'=>'',
                'placeholder'=>'该名称会显示在api文档'
            ],
            [
                'name'=>'name',
                'type'=>'input',
                'title'=>'控制器名',
                'tips'=>'请使用驼峰式命名',
                'value'=>'',
            ],
            [
                'name'=>'map_name',
                'type'=>'input',
                'title'=>'接口映射名',
                'tips'=>'(选填)',
                'value'=>'',
                'placeholder'=>'留空则接口默认使用控制器名'
            ],
            [
                'name'=>'version',
                'type'=>'input',
                'title'=>'版本',
                'tips'=>'(选填)',
                'value'=>'',
                'placeholder'=>'可填入如:v1,v2等'
            ],
            [
                'name'=>'sort',
                'type'=>'input',
                'title'=>'排序',
                'tips'=>'',
                'value'=>0,
            ],
            [
                'name'=>'status',
                'type'=>'radio',
                'title'=>'状态',
                'tips'=>'',
                'value'=>1,
                'options'=>['关闭', '开启']
            ]
        ];
        return $result;
    }

    public function buildTable()
    {
        $result = $this->buiderObj->buildData();
        $result['buildTable'] = [
            'toolbar' => [
                [
                    'title' => '添加',
                    'url' => 'add',
                    'class' => 'layui-btn layui-btn-sm layui-btn-normal hi-iframe-pop',
                    'data'=>[
                        'title'=>'添加控制器',
                    ]
                ],
                [
                    'title' => '启用',
                    'url' => 'status?v=1',
                    'class' => 'layui-btn layui-btn-sm hi-table-ajax',
                ],
                [
                    'title' => '禁用',
                    'url' => 'status?v=0',
                    'class' => 'layui-btn layui-btn-sm layui-btn-warm hi-table-ajax',
                ]
            ],
            'config' => [
                'page' => true,
                'limit' => 20,
                'cols' => [
                    [
                        'type' => 'checkbox',
                    ],
                    [
                        'field' => 'id',
                        'title' => 'ID',
                        'width' => 80,
                    ],
                    [
                        'field' => 'title',
                        'title' => '控制器名称',
                    ],
                    [
                        'field' => 'name',
                        'title' => '控制器名',
                        'width' => 150,
                    ],
                    [
                        'field' => 'map_name',
                        'title' => '接口映射名',
                        'width' => 150,
                    ],
                    [
                        'field' => 'version',
                        'title' => '版本',
                        'width' => 80,
                    ],
                    [
                        'field' => 'sort',
                        'title' => '排序',
                    ],
                    [
                        'field' => 'status',
                        'title' => '状态',
                        'templet' => '#switchStatusTpl',
                        'type'=>'switch',
                        'operate' => [
                            'filter'=>'switchStatus',
                            'url'=>url('status'),
                            'text'=>"开启|关闭"
                        ],
                        'width' => 100,
                    ],
                    [
                        'title' => '操作',
                        'templet' => '#perateTpl',
                        'type'=>'button',
                        'operate' => [
                            [
                                'text'=>'方法管理',
                                'url'=>url('actions/index').'?cid={{d.id}}',
                                'class'=>"hi-iframe-pop",
                                'data'=>[
                                    'title'=>'方法管理',
                                    'width'=>'90%',
                                ]
                            ],
                            [
                                'text'=>'编辑',
                                'url'=>url('edit').'?id={{d.id}}',
                                'class'=>"layui-btn-normal hi-iframe-pop",
                                'data'=>[
                                    'title'=>'修改控制器',
                                ]
                            ],
                            [
                                'text'=>'生成接口',
                                'url'=>url('build/createApi').'?id={{d.id}}',
                                'class'=>"layui-btn-warm hi-ajax",
                            ],
                            [
                                'text'=>'删除',
                                'url'=>url('remove').'?id={{d.id}}',
                                'class'=>"layui-btn-danger j-tr-del"
                            ]
                        ],
                    ],
                ],
            ],
        ];
        return $result;
    }

    public function status()
    {
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->status();
    }

    public function remove()
    {
        $id = input('id');
        if(null !== ApiAction::where('cid', $id)->find()){
            return $this->response(0, '请先删除该控制器下的数据');
        }
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->_remove();
    }

}
