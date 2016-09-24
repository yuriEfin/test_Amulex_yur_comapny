<?php

namespace app;

abstract class Base
{

    private $_provider;

    abstract function getProvider();

    abstract function setProvider(interfaces\IProvider $provider);
}
