<?php
namespace adminbase\Controller\System;

use adminbase\Service\Builder\FormBuildService;
use adminbase\Service\Builder\GridBuildService;
use Cml\Config;
use adminbase\Controller\CommonController;
use adminbase\Model\System\LoginLogModel;
use adminbase\Service\SearchService;

class LoginLogController extends CommonController
{
    public function index()
    {
        GridBuildService::create('adminbase/System/LoginLog/ajaxPage', '登录日志')
            ->addSearchItem('userid', '请输入用户id')
            ->addSearchItem('startTime', '开始时间', FormBuildService::INPUT_DATETIME)
            ->addSearchItem('endTime', '结束时间', FormBuildService::INPUT_DATETIME)

            ->addColumn('id', 'id')
            ->addColumn('userid', 'userid')
            ->addColumn('用户名', 'username')
            ->addColumn('昵称', 'nickname')
            ->addColumn('Ip', 'ip')
            ->addColumn('登录时间', 'ctime')
            ->display();
    }

    /**
     * ajax请求分页
     *
     * @acljump adminbase/System/LoginLog/index
     */
    public function ajaxPage()
    {
        $loginLogModel = new LoginLogModel();
        SearchService::processSearch([
            'userid' => '',
            'startTime' => '>',
            'endTime' => '<'
        ], $loginLogModel, true);

        $totalCount = $loginLogModel->paramsAutoReset(false, true)->getTotalNums();
        $list = $loginLogModel->getListByPaginate(Config::get('page_num'));
        array_walk($list, function(&$item) {
            $item['ctime'] = date('Y-m-d H:i:s', $item['ctime']);
        });

        $this->renderJson(0, ['list' => $list, 'totalCount' => $totalCount]);
    }
}