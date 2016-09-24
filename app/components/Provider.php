<?php

namespace app\components;

use app\interfaces\IProvider;

/**
 * Интерфейс для провайдеров
 *
 * @author gambit
 */
abstract class Provider implements IProvider
{

    private $_xpath;

    /**
     * Auth Provider Url
     * @var type 
     */
    public $urlAuth;

    /**
     * response Url - parsing data
     * @var type 
     */
    public $urlResponse;

    /**
     * name Provider 
     * @var type 
     */
    public $name;

    /**
     * Auth username
     * @var string
     */
    public $username;

    /**
     * Auth password
     * @var string
     */
    public $password;

    /**
     * response JSON
     * @var array 
     */
    public $response;

    /**
     * format response
     * @var type 
     */
    public $format = 'json';

}
