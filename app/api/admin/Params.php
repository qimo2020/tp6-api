<?php declare(strict_types=1);
namespace app\api\admin;
use think\facade\Cache;

defined('IN_SYSTEM') or die('Access Denied');

class Params extends Common
{
    protected function initialize()
    {
        parent::initialize();
        $this->buiderObj->hiModel = $this->buiderObj->hiValidate = 'ApiParam';
    }

    public function index(){
        $params = $this->request->param();
        $this->buiderObj->hiWhere = ['aid'=>$params['aid'], 'param_type'=>$params['type']];
        return $this->buiderObj->_table();
    }

    public function add()
    {
        $params = $this->request->param();
        $this->buiderObj->hiWhere = ['aid'=>$params['aid']];
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->_save();
    }

    public function edit(){
        $params = $this->request->param();
        $this->buiderObj->hiWhere = ['aid'=>$params['aid']];
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->_save([], 'edit');
    }

    public function buildForm($op = 'add'){
        $params = $this->request->param();
        $result = $this->buiderObj->buildData();
        $result['buildForm']['hiData']['pop'] = true;
        $result['buildForm']['items'] = [
            [
                'name'=>'title',
                'type'=>'input',
                'title'=>'参数名称',
                'tips'=>'',
                'value'=>'',
                'placeholder'=>'该名称会显示在api文档'
            ],
            [
                'name'=>'name',
                'type'=>'input',
                'title'=>'参数名',
                'tips'=>'',
                'value'=>'',
            ],
            [
                'name'=>'data_type',
                'type'=>'select',
                'title'=>'数据类型',
                'tips'=>'',
                'value'=>'',
                'options'=>['string'=>'String', 'interger'=>'Interger', 'array'=>'Array', 'multiarray'=>'MultiArray', 'object'=>'Object', 'float'=>'Float', 'boolean'=>'Boolean', 'file'=>'File']
            ],
            [
                'name'=>'is_need',
                'type'=>'radio',
                'title'=>'是否必须',
                'tips'=>'',
                'value'=>'',
                'options'=>['否','是']
            ],
            [
                'name'=>'def_val',
                'type'=>'input',
                'title'=>'默认值',
                'tips'=>'(选填)',
                'value'=>'',
                'placeholder'=>'设置了该值,则前端请求地址中即使不带该参数,系统也会默认带有此参数和值'
            ],
            [
                'name'=>'rule',
                'type'=>'input',
                'title'=>'验证规则',
                'tips'=>'(选填)',
                'value'=>'',
                'placeholder'=>'填入PHP的正则规则语法即可'
            ],
            [
                'name'=>'intro',
                'type'=>'text',
                'title'=>'参数说明',
                'tips'=>'(选填)',
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
                'type'=>'radio',
                'title'=>'状态',
                'tips'=>'',
                'value'=>1,
                'options'=>['关闭', '开启']
            ],
            [
                'name'=>'aid',
                'type'=>'hidden',
                'value'=>$params['aid'],
            ]
        ];
        if(isset($params['type'])){
            $paramType = ['name'=>'param_type','type'=>'hidden'];
            $paramType['value'] = $params['type'] ?: 0;
            $result['buildForm']['items'][] = $paramType;
        }
        return $result;
    }

    public function buildTable()
    {
        $params = $this->request->param();
        $result = $this->buiderObj->buildData();
        $result['buildTable'] = [
            'toolbar' => [
                [
                    'title' => '添加',
                    'url' => 'add?aid='.$params['aid'].'&type='.$params['type'],
                    'class' => 'layui-btn layui-btn-sm layui-btn-normal hi-iframe-pop',
                    'data'=>[
                        'title'=>'添加参数',
                        'width'=>'100%',
                        'height'=>'100%',
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
                        'title'=>'删除参数',
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
                        'width' => 80,
                    ],
                    [
                        'field' => 'title',
                        'title' => '参数名称',
                    ],
                    [
                        'field' => 'name',
                        'title' => '参数名',
                    ],
                    [
                        'field' => 'data_type',
                        'title' => '数据类型',
                    ],
                    [
                        'field' => 'is_need',
                        'title' => '必须',
                        'templet' => '#isNeedTpl',
                        'type'=>'text',
                        'operate' => [
                            [
                                'text'=>"{{d.is_need == 0 ? '否':'是'}}"
                            ]
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
                    ],
                    [
                        'field' => 'sort',
                        'title' => '排序',
                    ],
                    [
                        'title' => '操作',
                        'templet' => '#perateTpl',
                        'type'=>'button',
                        'operate' => [
                            [
                                'text'=>'编辑',
                                'url'=>url('edit').'?id={{d.id}}&aid='.$params['aid'].'&type='.$params['type'],
                                'class'=>"layui-btn-normal hi-iframe-pop",
                                'data'=>[
                                    'title'=>'修改参数',
                                    'width'=>'100%',
                                    'height'=>'100%',
                                ]
                            ],
                            [
                                'text'=>'删除',
                                'url'=>url('remove').'?id={{d.id}}',
                                'class'=>"layui-btn-danger j-tr-del"
                            ]
                        ],
                        'width' => 300,
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
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->_remove();
    }

}
