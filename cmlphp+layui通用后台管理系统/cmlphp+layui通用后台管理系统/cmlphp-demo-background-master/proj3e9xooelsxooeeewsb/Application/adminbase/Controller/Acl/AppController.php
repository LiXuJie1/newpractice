<?php
/* * *********************************************************
* 应用管理
* @Author  linhecheng<linhechengbush@live.com>
* @Date: 2017/5/08 10:33
* *********************************************************** */
namespace adminbase\Controller\Acl;

use adminbase\Model\Acl\AppModel;
use adminbase\Model\Acl\MenusModel;
use adminbase\Service\Builder\FormBuildService;
use adminbase\Service\Builder\GridBuildService;
use adminbase\Service\System\LogService;
use Cml\Config;
use Cml\Http\Input;
use adminbase\Controller\CommonController;

class AppController extends CommonController
{
    //应用列表
    public function index()
    {
        GridBuildService::create('adminbase/Acl/App/ajaxPage', '应用列表')
            ->addTopButton('新增', FormBuildService::BUTTON_ADD, 'adminbase/Acl/App/add', '新增应用', 'adminbase/Acl/App/save')
            ->addColumn('id', 'id')
            ->addColumn('应用名称', 'name')
            ->addButtonColumn('操作', 'id', [
                [
                    'text' => '编辑',
                    'type' => FormBuildService::BUTTON_EDIT,
                    'url' => 'adminbase/Acl/App/edit',
                    'title' => '编辑应用',
                    'saveUrl' => 'adminbase/Acl/App/save',
                    'class' => 'layui-btn-normal'
                ],
                [
                    'text' => '删除',
                    'type' => FormBuildService::BUTTON_DEL,
                    'url' => 'adminbase/Acl/App/del',
                    'class' => 'layui-btn-danger'
                ]
            ])
            ->display();
    }

    /**
     * ajax请求分页
     *
     * @acljump adminbase/Acl/App/index
     */
    public function ajaxPage()
    {
        $appModel = new AppModel();

        $totalCount = $appModel->paramsAutoReset(false, true)->getTotalNums();
        $list = $appModel->getListByPaginate(Config::get('page_num'));

        $this->renderJson(0, ['list' => $list, 'totalCount' => $totalCount]);
    }


    /**
     * 新增应用
     *
     * @param array $app 编辑的时候传入应用信息
     */
    public function add($app = [])
    {
        $inst = FormBuildService::create($app ? '修改应用信息' : '新增应用')
            ->addFormItem('应用名', 'name', '请输入应用名', '',
                ' lay-verify="required" '
            )
            ->withData($app);
        $app && $inst->addFormItem('', 'id', '', '', '', FormBuildService::INPUT_HIDDEN);
        $inst->display();
    }

    /**
     * 编辑应用
     *
     */
    public function edit()
    {
        $id = Input::getInt('id');

        $this->add(AppModel::getInstance()->getByColumn($id));
    }

    /**
     * 保存应用
     *
     * @acljump adminbase/Acl/App/add|adminbase/Acl/App/edit
     *
     */
    public function save()
    {
        $data = [];
        $id = Input::postInt('id');

        $data['name'] = Input::postString('name', '');
        $data['name'] || $this->renderJson(2, '应用名称不能为空!');

        $appsModel = new AppModel();
        if (is_null($id)) {//新增
            $res = $appsModel->set($data);
        } else {
            LogService::addActionLog("修改了应用[{$id}]的信息" . json_encode($data));
            $res = $appsModel->updateByColumn($id, $data);
        }
        $this->renderJson($res ? 0 : 1);
    }


    /**
     * 删除应用
     *
     */
    public function del()
    {
        $id = Input::getInt('id');
        $id < 1 && $this->renderJson(1);

        //判断该应用下有无菜单，有则提示不能删除
        MenusModel::getInstance()->getByColumn($id, 'app') && $this->renderJson(10101);
        $appsModel = new AppModel();
        if ($appsModel->delByColumn($id)) {
            LogService::addActionLog("删除了应用[{$id}]!");
            $this->renderJson(0);
        } else {
            $this->renderJson(1);
        }
    }
}