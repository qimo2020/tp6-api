<?php declare(strict_types=1);
namespace app\api\admin;
defined('IN_SYSTEM') or die('Access Denied');
class Codes extends Common
{
    protected function initialize()
    {
        parent::initialize();
        $this->buiderObj->hiModel = $this->buiderObj->hiValidate = 'ApiCode';
    }

    public function index(){
        $params = $this->request->param();
        $this->buiderObj->hiWhere = ['aid'=>$params['aid']];
        return $this->buiderObj->_table();
    }

    public function add()
    {
        $params = $this->request->param();
        $this->buiderObj->hiWhere = ['aid'=>$params['aid']];
        return $this->buiderObj->_save();
    }

    public function edit(){
        $params = $this->request->param();
        $this->buiderObj->hiWhere = ['aid'=>$params['aid']];
        return $this->buiderObj->_save([], 'edit');
    }

    public function buildForm($op = 'add'){
        $params = $this->request->param();
        $result = $this->buiderObj->buildData();
        $result['buildForm']['hiData']['pop'] = true;
        $result['buildForm']['items'] = [
            [
                'name'=>'code',
                'type'=>'input',
                'title'=>'返回码',
                'tips'=>'',
                'value'=>'',
            ],
            [
                'name'=>'title',
                'type'=>'textarea',
                'title'=>'返回码说明',
                'tips'=>'',
                'value'=>'',
            ],
            [
                'name'=>'solution',
                'type'=>'textarea',
                'title'=>'解决方案',
                'tips'=>'',
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
                'name'=>'aid',
                'type'=>'hidden',
                'value'=>$params['aid'],
            ]
        ];
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
                    'url' => 'add?aid='.$params['aid'],
                    'class' => 'layui-btn layui-btn-sm layui-btn-normal hi-iframe-pop',
                    'data'=>[
                        'title'=>'添加返回码',
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
                        'title'=>'删除返回码',
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
                        'field' => 'code',
                        'title' => '返回码',
                    ],
                    [
                        'field' => 'title',
                        'title' => '说明',
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
                                'url'=>url('edit').'?id={{d.id}}&aid='.$params['aid'],
                                'class'=>"layui-btn-normal hi-iframe-pop",
                                'data'=>[
                                    'title'=>'修改返回码',
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
        return $this->buiderObj->status();
    }

    public function remove()
    {
        return $this->buiderObj->_remove();
    }

}
