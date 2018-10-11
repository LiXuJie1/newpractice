<?php
namespace adminbase\Controller\System;

use adminbase\Service\Builder\FormBuildService;
use adminbase\Service\Builder\GridBuildService;
use Cml\Config;
use adminbase\Controller\CommonController;
use adminbase\Model\System\SystemLogModel;
use adminbase\Service\SearchService;

class SystemLogController extends CommonController
{
    public function index()
    {
        GridBuildService::create('adminbase/System/SystemLog/ajaxPage', '系统操作日志')
            ->addSearchItem('userid', '请输入用户id')
            ->addSearchItem('url', '请输入URL')
            ->addSearchItem('startTime', '开始时间', FormBuildService::INPUT_DATETIME)
            ->addSearchItem('endTime', '结束时间', FormBuildService::INPUT_DATETIME)

            ->addColumn('id', 'id')
            ->addColumn('userid', 'userid')
            ->addColumn('用户名', 'username')
            ->addColumn('URL', 'url')
            ->addColumn('操作的菜单名', 'action')
            ->addColumn('GET参数', 'get', 'style="width:250px;word-wrap: break-word;"')
            ->addColumn('POST参数', 'post', 'style="width:250px;word-wrap: break-word;"')
            ->addColumn('Ip', 'ip')
            ->addColumn('创建时间', 'ctime')
            ->display();
    }

    /**
     * ajax请求分页
     *
     * @acljump adminbase/System/SystemLog/index
     */
    public function ajaxPage()
    {
        $systemLogModel = new SystemLogModel();
        SearchService::processSearch([
            'userid' => '',
            'url' => 'like',
            'startTime' => '>',
            'endTime' => '<'
        ], $systemLogModel, true);
        $totalCount = $systemLogModel->paramsAutoReset(false, true)->getTotalNums();

        $list = $systemLogModel->getListByPaginate(Config::get('page_num'));
        array_walk($list, function(&$item) {
            $item['ctime'] = date('Y-m-d H:i:s', $item['ctime']);
        });

        $this->renderJson(0, ['list' => $list, 'totalCount' => $totalCount]);
    }
}
