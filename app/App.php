<?php

namespace app;

class App extends Base
{

    /**
     * кофигурационный массив 
     * @var array
     */
    public $config = [
    ];

    /**
     * Instance application
     * @var self() 
     */
    private static $_app = null;

    public static function createApp($config)
    {
        if (!self::$_app) {
            self::$_app = new self();
        }
        return self::$_app->init($config);
    }

    public function run()
    {
        return (new controllers\SiteController())->runAction();
    }

    public function getProvider()
    {
        return $this->_provider;
    }

    public function setProvider(interfaces\IProvider $provider)
    {
        $this->_provider = $provider;
    }

    public function init($config)
    {
        $this->config = $config;

        return $this;
    }
}
