<?php

namespace app\components;

class NotFoundProvider extends \ErrorException
{
    
}

/**
 * Description of FactoryProvider
 *
 * @author gambit
 */
class FactoryProvider
{

    public static $namespaceProvider = 'app\\components\\provider\\';

    public static function createProvider($key, $params)
    {
        $class = self::$namespaceProvider . ucfirst($key);
        if (!class_exists($class)) {
            throw new NotFoundProvider('Not found Provider ' . $key);
        }
        return new $class($params);
    }
}
