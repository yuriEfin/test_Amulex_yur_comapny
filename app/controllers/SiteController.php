<?php

namespace app\controllers;

use app\components\Provider;

/**
 * @author gambit
 */
class SiteController extends BaseController
{

    public function runAction()
    {
        $view = 'index';
        $viewResponse = 'indexResonse';
        $response = null;
        if (!empty($_POST)) {
            extract($_POST);
            if (isset($username) && isset($password) && isset($provider)) {
                $provider = \app\components\FactoryProvider::createProvider($provider, ['username' => $username, 'password' => $password]);
                if ($provider->getIsAuth()) {
                    header('Content-type:application/json;charset=utf-8');
                    $response = json_encode($provider->response);
                }
            }
        }
        if (!$response) {
            echo $this->render($view);
        } else {
            echo $this->render($viewResponse, ['response' => $response]);
        }
    }
}
