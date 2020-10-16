<?php declare(strict_types=1);
namespace app\api\lib;
class Doc
{
    protected $config = [
        'title' => 'Api接口文档',
        'version' => '1.0.0',
        'copyright' => 'Powered By SUCAILONG.COM',
        'password' => '',
        'document' => [
            "explain" => [
                'name' => '说明',
                'list' => [
                    '登录态' => ['1111'],
                    'formId收集' => ['2222', '22222'],
                    '邀请有礼' => ['333', '333333']
                ]
            ],
            "code" => [
                'name' => '返回码',
                'list' => [
                    '0' => '成功',
                    '1' => '失败'
                ]
            ]
        ],
        'header' => [],
        'params' => [],
        'static_path' => '',
        'controller' => [],
        'filter_method' => ['__call'],        # 过滤不需要解析的方法
        'return_format' => [
            'status' => "200/300/301/302",
            'message' => "提示信息",
        ]
    ];

    /**
     * 架构方法 设置参数
     *
     * @access public
     *
     * @param  array $config 配置参数
     */
    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 使用 $this->name 获取配置
     *
     * @access public
     *
     * @param  string $name 配置名称
     *
     * @return mixed    配置值
     */
    public function __get($name = null)
    {
        if ($name) {
            return $this->config[$name];
        } else {
            return $this->config;
        }

    }

    /**
     * 设置
     *
     * @access public
     *
     * @param  string $name  配置名称
     * @param  string $value 配置值
     *
     * @return void
     */
    public function __set($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 检查配置
     *
     * @access public
     *
     * @param  string $name 配置名称
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->config[$name]);
    }


    # 获取接口列表
    public function getlist($class, $method)
    {
        $list = [];
        $class = "app\\" . $class;
        if (class_exists($class)) {
            $reflection = new \ReflectionClass($class);
            $doc_str = $reflection->getDocComment();
            $doc = new DocParser();
            # 解析类
            $class_doc = $doc->parse_class($doc_str);
            $list['class'] = $class;
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            # 过滤不需要解析的方法以及非当前类的方法(父级方法)
            $filter_method = array_merge(['__construct'], $this->config['filter_method']);
            foreach ($methods as $key => $action) {
                if (!in_array($action->name, $filter_method) && $method == $action->name && $action->class === $class) {
                    if ($doc->parse_action($action)){
                        $list['action'] = $doc->parse_action($action);
                    }
                    break;
                }
            }
        }
        return $list;
    }

    /**
     * 获取接口详情
     * @param string $class
     * @param string $action
     *
     * @return array|bool
     */
    public function get_api_detail($class = '', $action = '')
    {
        $method = (new \ReflectionClass($class))->getMethod($action);
        return (new Parser())->parse_action($method);
    }
}