<?php declare(strict_types=1);
namespace app\api\admin;
defined('IN_SYSTEM') or die('Access Denied');
use app\api\model\ApiAction;
use app\api\model\ApiCode;
use app\api\model\ApiParam;
use think\facade\Cache;
class Actions extends Common
{
    protected function initialize()
    {
        parent::initialize();
        $this->buiderObj->hiModel = $this->buiderObj->hiValidate = 'ApiAction';
    }

    public function index(){
        $params = $this->request->param();
        $this->buiderObj->hiWhere = ['cid'=>$params['cid']];
        return $this->buiderObj->_table();
    }

    public function add()
    {
        $params = $this->request->param();
        $this->buiderObj->hiWhere = ['cid'=>$params['cid']];
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->_save();
    }

    public function edit(){
        $params = $this->request->param();
        $this->buiderObj->hiWhere = ['cid'=>$params['cid']];
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
                'title'=>'方法名称',
                'tips'=>'该名称会显示在api文档',
                'value'=>'',
            ],
            [
                'name'=>'name',
                'type'=>'input',
                'title'=>'方法名',
                'tips'=>'请使用驼峰式命名',
                'value'=>'',
            ],
            [
                'name'=>'request_type',
                'type'=>'select',
                'title'=>'请求类型',
                'tips'=>'',
                'value'=>'',
                'options'=>['rule'=>'不限', 'get'=>'GET', 'post'=>'POST', 'put'=>'PUT', 'patch'=>'PATCH', 'delete'=>'DELETE']
            ],
            [
                'name'=>'format',
                'type'=>'checkbox',
                'title'=>'数据格式',
                'tips'=>'接口返回的数据格式',
                'value'=>'',
                'options'=>['json'=>'JSON', 'xml'=>'XML']
            ],
            [
                'name'=>'test_auth',
                'type'=>'radio',
                'title'=>'接口测试',
                'tips'=>'是否开启文档的API测试工具',
                'value'=>0,
                'options'=>['关闭', '开启']
            ],
            [
                'name'=>'api_auth',
                'type'=>'radio',
                'title'=>'接口校验',
                'tips'=>'',
                'value'=>0,
                'options'=>['关闭', '开启']
            ],
            [
                'name'=>'user_auth',
                'type'=>'radio',
                'title'=>'会员鉴权(oauth)',
                'tips'=>'',
                'value'=>0,
                'options'=>['关闭', '开启']
            ],
            [
                'name'=>'jwt_auth',
                'type'=>'radio',
                'title'=>'会员鉴权(jwt)',
                'tips'=>'',
                'value'=>0,
                'options'=>['关闭', '开启']
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
                'name'=>'doc_def',
                'type'=>'radio',
                'title'=>'文档默认首页',
                'tips'=>'',
                'value'=>0,
                'options'=>['否', '是']
            ],
            [
                'name'=>'cid',
                'type'=>'hidden',
                'value'=>$params['cid'],
            ]
        ];
        return $result;
    }

    public function buildTable()
    {
        $params = $this->request->param();
        $result = $this->buiderObj->buildData();
        $this->buiderObj->jsCode = <<<EOF
layui.use(['layer','form'], function(){
            var $ = layui.jquery,layer = layui.layer,form = layui.form;
            form.on('switch(switchAuth)', function(data) {
                let that = $(this), status = data.elem.checked == false ? 0 : 1;
                $.get(that.attr('data-href'), {}, function(res) {
                    if (res.code == 0) {
                        that.trigger('click');
                        form.render('checkbox');
                    }
                });
            })
            form.on('switch(switchUserAuth)', function(data) {
                let that = $(this),status = data.elem.checked == false ? 0 : 1;
                $.get(that.attr('data-href'), {}, function(res) {
                    if (res.code == 0) {
                        that.trigger('click');
                        form.render('checkbox');
                    }
                });
            })
        });
EOF;
        $result['buildTable'] = [
            'toolbar' => [
                [
                    'title' => '添加',
                    'url' => 'add?cid='.$params['cid'],
                    'class' => 'layui-btn layui-btn-sm layui-btn-normal hi-iframe-pop',
                    'data'=>[
                        'title'=>'添加方法',
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
                        'title' => '方法名称',
                    ],
                    [
                        'field' => 'name',
                        'title' => '方法名',
                    ],
                    [
                        'field' => 'request_type',
                        'title' => '请求类型',
                    ],
                    [
                        'field' => 'api_auth',
                        'title' => '接口校验',
                        'templet' => '#switchAuthTpl',
                        'type'=>'switch',
                        'operate' => [
                            'filter'=>'switchAuth',
                            'url'=>url('apiAuth'),
                            'text'=>"开启|关闭"
                        ],
                    ],
                    [
                        'field' => 'user_auth',
                        'title' => '会员鉴权(oauth)',
                        'templet' => '#switchUserAuthTpl',
                        'type'=>'switch',
                        'operate' => [
                            'filter'=>'switchUserAuth',
                            'url'=>url('UserAuth'),
                            'text'=>"开启|关闭"
                        ],
                    ],
                    [
                        'field' => 'jwt_auth',
                        'title' => '会员鉴权(jwt)',
                        'templet' => '#switchJwtAuthTpl',
                        'type'=>'switch',
                        'operate' => [
                            'filter'=>'switchUserAuth',
                            'url'=>url('UserAuth'),
                            'text'=>"开启|关闭"
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
                                'text'=>'请求参数',
                                'url'=>url('params/index').'?aid={{d.id}}&type=0',
                                'class'=>"hi-iframe-pop",
                                'data'=>[
                                    'title'=>'请求参数',
                                    'width'=>'100%',
                                    'height'=>'100%',
                                ]
                            ],
                            [
                                'text'=>'返回参数',
                                'url'=>url('params/index').'?aid={{d.id}}&type=1',
                                'class'=>"layui-bg-cyan hi-iframe-pop",
                                'data'=>[
                                    'title'=>'返回参数',
                                    'width'=>'100%',
                                    'height'=>'100%',
                                ]
                            ],
                            [
                                'text'=>'状态码管理',
                                'url'=>url('codes/index').'?aid={{d.id}}',
                                'class'=>"layui-btn-warm hi-iframe-pop",
                                'data'=>[
                                    'title'=>'状态码管理',
                                    'width'=>'100%',
                                    'height'=>'100%',
                                ],
                            ],
                            [
                                'text'=>'编辑',
                                'url'=>url('edit').'?id={{d.id}}&cid='.$params['cid'],
                                'class'=>"layui-btn-normal hi-iframe-pop",
                                'data'=>[
                                    'title'=>'修改方法',
                                ]
                            ],
                            [
                                'text'=>'删除',
                                'url'=>url('remove').'?id={{d.id}}',
                                'class'=>"layui-btn-danger j-tr-del"
                            ]
                        ],
                        'width' => 350,
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
        if(null !== ApiParam::where('aid', $id)->find()){
            return $this->response(0, '请先删除该方法下的数据');
        }
        if(null !== ApiCode::where('aid', $id)->find()){
            return $this->response(0, '请先删除该方法下的数据');
        }
        Cache::tag(self::$appTag)->clear();
        return $this->buiderObj->_remove();
    }

    public function apiAuth($v, $id){
        try{
            $obj = new ApiAction;
            $obj->update(['api_auth'=>$v], ['id' => $id]);
            return $this->response(1, '修改成功');
        } catch (\Exception $e) {
            return $this->response(1, $e->getMessage());
        }
    }

    public function userAuth($v, $id){
        try{
            $obj = new ApiAction;
            $obj->update(['user_auth'=>$v], ['id' => $id]);
            return $this->response(1, '修改成功');
        } catch (\Exception $e) {
            return $this->response(1, $e->getMessage());
        }
    }

    public function jwtAuth($v, $id){
        try{
            $obj = new ApiAction;
            $obj->update(['jwt_auth'=>$v], ['id' => $id]);
            return $this->response(1, '修改成功');
        } catch (\Exception $e) {
            return $this->response(1, $e->getMessage());
        }
    }


}
