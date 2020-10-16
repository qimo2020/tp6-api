<?php declare(strict_types=1);
namespace app\api\admin;
use app\api\model\ApiApp;
use app\api\model\ApiController;
use think\facade\Cache;

class Apps extends Common
{
    protected function initialize()
    {
        parent::initialize();
        $this->buiderObj->hiModel = 'ApiApp';
        $this->buiderObj->hiValidate = 'Apps';
    }

    public function index(){
        return $this->buiderObj->_table();
    }

    public function add()
    {
        return $this->buiderObj->_save();
    }

    public function edit(){
        return $this->buiderObj->_save([], 'edit');
    }

    public function buildForm($op = 'add'){
        $appId = randomStr(8);
        $appSecret = randomStr(32,7);
        $result = $this->buiderObj->buildData();
        $result['buildForm']['hiData']['pop'] = true;
        $result['buildForm']['items'] = [
            [
                'name'=>'title',
                'type'=>'input',
                'title'=>'应用名称',
                'tips'=>'',
                'value'=>'',
            ],
            [
                'name'=>'name',
                'type'=>'input',
                'title'=>'应用名',
                'tips'=>'',
                'value'=>'',
            ],
            [
                'name'=>'app_id',
                'type'=>'input',
                'title'=>'AppId',
                'tips'=>'',
                'value'=>$appId,
            ],
            [
                'name'=>'app_secret',
                'type'=>'input',
                'title'=>'AppSecret',
                'tips'=>'',
                'value'=>$appSecret,
            ],
            [
                'name'=>'app_type',
                'type'=>'radio',
                'title'=>'类型',
                'tips'=>'',
                'value'=>0,
                'options'=>['模块', '插件']
            ],
            [
                'name'=>'callback_uri',
                'type'=>'input',
                'title'=>'授权回调',
                'tips'=>'必须以http/https开头的完整地址',
                'value'=>'',
            ],
            [
                'name'=>'white_list',
                'type'=>'input',
                'title'=>'白名单',
                'tips'=>'ip白名单, 用","分隔',
                'value'=>'',
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
                'type'=>'switch',
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
                        'title'=>'添加应用',
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
                ],
                [
                    'title' => '删除',
                    'url' => 'remove',
                    'class' => 'layui-btn layui-btn-sm layui-btn-danger j-page-btns',
                    'data'=>[
                        'title'=>'删除应用',
                    ]
                ],
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
                        'width' => 50,
                    ],
                    [
                        'field' => 'title',
                        'title' => '应用名称',
                        'width' => 180,
                    ],
                    [
                        'field' => 'app_id',
                        'title' => 'AppId',
                        'width' => 150,
                    ],
                    [
                        'field' => 'app_secret',
                        'title' => 'AppSecret',
                        'type'=>'button',
                        'templet' => '#refreshTpl',
                        'depend_html'=>'{{d.app_secret}}',
                        'operate' => [
                            [
                                'text'=>'刷新',
                                'confirm'=>'确定刷新秘钥吗',
                                'ajax'=>true,
                                'url'=>url('refresh').'?id={{d.id}}',
                            ],
                        ],
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
                                'text'=>'访问文档',
                                'url'=>'/api/{{d.name}}/',
                                'class'=>"layui-btn-normal",
                                'style'=>"background-color:#666699",
                                'attribute'=>'target="_blank"'
                            ],
                            [
                                'text'=>'编辑',
                                'url'=>url('edit').'?id={{d.id}}',
                                'class'=>"layui-btn-normal hi-iframe-pop",
                                'data'=>[
                                    'title'=>'修改应用',
                                ]
                            ],
                            [
                                'text'=>'备份代码',
                                'url'=>url('build/backup').'?app_id={{d.id}}',
                                'class'=>"hi-ajax",
                                'style'=>"background-color:#FF6666",
                            ],
                            [
                                'text'=>'生成接口',
                                'url'=>url('build/createApi').'?app_id={{d.id}}',
                                'class'=>"hi-ajax",
                                'style'=>'background-color:#6666CC'
                            ],
                            [
                                'text'=>'备份数据',
                                'url'=>url('database/backup').'?app_id={{d.id}}',
                                'class'=>"hi-iframe-pop",
                                'style'=>"background-color:#33CC99",
                                'data'=>['title'=>'备份应用数据']
                            ],
                            [
                                'text'=>'导入数据',
                                'url'=>url('database/import').'?app_id={{d.id}}',
                                'class'=>"hi-iframe-pop",
                                'style'=>"background-color:#FF9933",
                                'data'=>['title'=>'导入应用数据']
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

    public function refresh(){
        $params = $this->request->param();
        if(!isset($params['id']) || !is_numeric($params['id'])){
            return $this->response(0, '刷新失败');
        }
        $app = ApiApp::find($params['id']);
        $app->app_secret = randomStr(32,7);
        $app->save();
        return $this->response(1, '刷新成功');
    }

    public function status()
    {
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->status();
    }

    public function remove()
    {
        $id = input('id');
        if(null !== ApiController::where('app_id', $id)->find()){
            return $this->response(0, '请先删除该应用下的数据');
        }
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->_remove();
    }

}
