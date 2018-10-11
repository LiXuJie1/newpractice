<?php
namespace adminbase\Controller\System;

use adminbase\Service\Builder\FormBuildService;
use adminbase\Service\Builder\GridBuildService;
use Cml\Config;
use adminbase\Controller\CommonController;
use adminbase\Model\System\ActionLogModel;
use adminbase\Service\SearchService;

class ActionLogController extends CommonController
{
    public function index()
    {
        GridBuildService::create('adminbase/System/ActionLog/ajaxPage', '重要操作日志')
            ->addSearchItem('userid', '请输入用户id')
            ->addSearchItem('startTime', '开始时间', FormBuildService::INPUT_DATETIME)
            ->addSearchItem('endTime', '结束时间', FormBuildService::INPUT_DATETIME)

            ->addColumn('id', 'id')
            ->addColumn('userid', 'userid')
            ->addColumn('用户名', 'username')
            ->addColumn('操作名', 'action')
            ->addColumn('操作时间', 'ctime')
            ->display();
    }


    /**
     * ajax请求分页
     *
     * @acljump adminbase/System/ActionLog/index
     */
    public function ajaxPage()
    {
        $actionLogModel = new ActionLogModel();
        SearchService::processSearch([
            'userid' => '',
            'startTime' => '>',
            'endTime' => '<'
        ], $actionLogModel, true);

        $totalCount = $actionLogModel->paramsAutoReset(false, true)->getTotalNums();

        $list = $actionLogModel->getListByPaginate(Config::get('page_num'));
        array_walk($list, function (&$item) {
            $item['ctime'] = date('Y-m-d H:i:s', $item['ctime']);
        });

        $this->renderJson(0, ['list' => $list, 'totalCount' => $totalCount]);
    }
}