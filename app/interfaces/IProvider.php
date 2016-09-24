<?php

namespace app\interfaces;

/**
 *
 * @author gambit
 */
interface IProvider
{

    public function init();

    public function auth();

    public function getData($uid);

    public function getToken($headerText);

    public function getIsAuth();

    public function setIsAuth($value);
}
