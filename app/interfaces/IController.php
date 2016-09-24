<?php

namespace app\interfaces;

/**
 *
 * @author gambit
 */
interface IController
{

    public function render($view, $params = []);

    public function renderFile($file, $params = []);

    public function findViewFile($view);
}
