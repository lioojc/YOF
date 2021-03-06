<?php

class Bootstrap extends Yaf_Bootstrap_Abstract{

    // Load libaray, MySQL model, function
    public function _initCore() {
        // 设置自动加载的目录
        ini_set('yaf.library', LIB_PATH);
        
        // 加载核心组件
        Yaf_Loader::import(CORE_PATH.'/C_Basic.php');
        Yaf_Loader::import(CORE_PATH.'/Helper.php');
        Yaf_Loader::import(CORE_PATH.'/Model.php');
        Yaf_Loader::import(LIB_PATH.'/yar/Yar_Basic.php');

        // 导入 F_Basic.php 与 F_Network.php
        Helper::import('Basic');
        Helper::import('Network');
    }

    // 这里我们添加三种路由，分别为 rewrite, rewrite_category, regex
    // 用于 url rewrite 的讲解
    public function _initRoute() {
        $router = Yaf_Dispatcher::getInstance()->getRouter();

        // rewrite
        $route = new Yaf_Route_Rewrite(
            '/article/detail/:articleID',
            array(
                'controller' => 'article',
                'action'     => 'detail',
            )
        );

        $router->addRoute('rewrite', $route);

        // rewrite_category
        $route = new Yaf_Route_Rewrite(
            '/article/detail/:categoryID/:articleID',
            array(
                'controller' => 'article',
                'action'     => 'detail',
            )
        );

        $router->addRoute('rewrite_category', $route);

        // regex
        $route = new Yaf_Route_Regex(
            '#article/([0-9]+).html#',
            array('controller' => 'article', 'action' => 'detail'),
            array(1 => 'articleID')
        );

        $router->addRoute('regex', $route);
    }

    public function _initRedis() {
        if(extension_loaded('Redis')){
            $config = Yaf_Application::app()->getConfig();
            
            $queue = 'test_queue';
            $host  = $config['redis_host'];
            $port  = $config['redis_port'];

            $redis = new Redis();
            $redis->connect($host, $port);

            Yaf_Registry::set('redis', $redis);
        }
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        $router = new RouterPlugin();
        $dispatcher->registerPlugin($router);

        $admin = new AdminPlugin();
        $dispatcher->registerPlugin($admin);
        Yaf_Registry::set('adminPlugin', $admin);
    }

}
