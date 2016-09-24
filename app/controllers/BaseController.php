<?php

namespace app\controllers;

use app\interfaces\IController;

/**
 * @author gambit
 */
class BaseController implements IController
{

    public $ext = '.php';
    public $dir = '';

    public function getDirView()
    {
        return dirname(__DIR__) . '/views/';
    }

    public function render($view, $params = [])
    {
        $viewFile = $this->findViewFile($view);
       
        ob_start();
        ob_implicit_flush(false);

        echo $this->renderFile($viewFile, $params);

        return ob_get_clean();
    }

    public function renderFile($_file_, $_params_ = [])
    {
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        require($_file_);

        return ob_get_clean();
    }

    public function findViewFile($view)
    {
        $view = $this->getDirView() . $view . $this->ext;
        if (!file_exists($view)) {
            throw new \ErrorException('File template (' . $view . ') does not exists');
        }
        return $view;
    }
}
