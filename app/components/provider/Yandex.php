<?php

namespace app\components\provider;

use app\components\Curl;

/**
 * Description of Yandex
 *
 * @author gambit
 */
class Yandex extends \app\components\Provider
{

    use TraitXpath;

    private $_doc;
    private $_xpath;

    /**
     * Is Auth User 
     * @var boolean
     */
    private $_isAuth = false;

    /**
     * Name this component
     * @var type 
     */
    public $name = 'Yandex';

    /**
     * Auth Provider Url
     * @var type 
     */
    public $urlAuth = 'https://passport.yandex.ru/passport?mode=auth';
    public $urlResponse = 'https://mail.yandex.ru/?uid={uid}&login=efin2012';

    public function __construct($params)
    {
        extract($params);
        if (!isset($username) && !isset($password)) {
            throw new Exception('Not init Username and Password for auth by provider :' . $this->name);
        }
        $this->username = $username;
        $this->password = $password;
        $this->init();
    }

    public function init()
    {
        $headers = $this->auth();
        if ($this->_isAuth) {
            $uid = $this->getToken($headers);
            $this->urlResponse = strtr($this->urlResponse, ['{uid}' => $uid]);
            $this->getData($uid);
        }
    }

    public function auth()
    {
        $header = Curl::auth($this);
    }

    public function getData($uid)
    {
        $html = Curl::getData($uid, $this);

        $xpath = $this->createXpath($html, true);

        $emailList['from'] = $this->query('//*[@id="main"]/div/div/span[2]/a[1]/span[1]/span/text()');
        $emailList['header'] = $this->query('//*[@id="main"]/div/div/span/a[2]/span[1]/span/text()');
        $emailList['date'] = $this->query('//*[@id="main"]/div/div/span[1]/span/text()');

        $this->response = $emailList;
    }

    public function getToken($headerText)
    {
        if (preg_match('/Session_id=(.*)/', $headerText, $m)) {
            $data = explode('.', $m[1]);
            $data2 = explode('|', $data[4]);
            $uid = $data2[1];
            return $uid;
        }
    }

    public function getIsAuth()
    {
        return $this->_isAuth;
    }

    public function setIsAuth($value)
    {
        return $this->_isAuth = $value;
    }
}
