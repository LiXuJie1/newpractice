<?php
namespace adminbase\Controller\System;

use Cml\Http\Input;
use Cml\View;
use adminbase\Controller\CommonController;

class IndexController extends CommonController
{
    /**
     * 首页
     *
     */
    public function index()
    {
        $isSub = Input::getInt('type', 0);
        $view = View::getEngine('Html');
        $isSub ? $view->displayWithLayout('System/Index/sub', 'regional') : $view->display('System/Index/index');
    }
}