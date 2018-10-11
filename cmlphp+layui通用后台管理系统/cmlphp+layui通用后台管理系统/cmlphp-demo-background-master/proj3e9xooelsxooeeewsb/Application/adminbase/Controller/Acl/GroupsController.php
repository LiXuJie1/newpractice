<?php
/**
 * 用户组管理
 *
 * @authon linhecheng
 */
namespace adminbase\Controller\Acl;

use adminbase\Service\AclService;
use adminbase\Service\Builder\FormBuildService;
use adminbase\Service\Builder\GridBuildService;
use adminbase\Service\System\LogService;
use Cml\Config;
use Cml\Http\Input;
use adminbase\Controller\CommonController;
use adminbase\Model\Acl\AccessModel;
use adminbase\Model\Acl\GroupsModel;
use adminbase\Service\SearchService;

class GroupsController extends CommonController
{
    //用户组列表
    public function index()
    {
        GridBuildService::create('adminbase/Acl/Groups/ajaxPage', '用户组列表')
            ->addSearchItem('name', '请输入用户组名称')
            ->addTopButton('新增', FormBuildService::BUTTON_ADD, 'adminbase/Acl/Groups/add', '新增用户组', 'adminbase/Acl/Groups/save')
            ->addColumn('id', 'id')
            ->addColumn('用户组名称', 'name')
            ->addColumn('备注', 'remark')
            ->addButtonColumn('操作', 'id', [
                [
                    'text' => '编辑',
                    'type' => FormBuildService::BUTTON_EDIT,
                    'url' => 'adminbase/Acl/Groups/edit',
                    'title' => '编辑用户组',
                    'saveUrl' => 'adminbase/Acl/Groups/save',
                    'class' => 'layui-btn-normal'
                ],
                [
                    'text' => '删除',
                    'type' => FormBuildService::BUTTON_DEL,
                    'url' => 'adminbase/Acl/Groups/del',
                    'class' => 'layui-btn-danger'
                ],
                [
                    'text' => '授权',
                    'type' => FormBuildService::BUTTON_ADD,
                    'url' => 'adminbase/Acl/Acl/group',
                    'title' => '用户组授权',
                    'saveUrl' => 'adminbase/Acl/Acl/save/type/2',
                    'class' => ''
                ]
            ])
            ->display();
    }

    /**
     * ajax请求分页
     *
     * @acljump adminbase/Acl/Groups/index
     */
    public function ajaxPage()
    {
        $groupsModel = new GroupsModel();

        SearchService::processSearch([
            'name' => 'like'
        ], $groupsModel, true);
        $totalCount = $groupsModel->paramsAutoReset(false, true)->getTotalNums();
        $list = $groupsModel->getGroupsList(Config::get('page_num'));

        $this->renderJson(0, ['list' => $list, 'totalCount' => $totalCount]);
    }


    /**
     * 新增用户组
     *
     * @param array $group 编辑的时候传入用户组信息
     */
    public function add($group = [])
    {
        $inst = FormBuildService::create($group ? '修改用户组信息' : '新增用户组')
            ->addFormItem('用户组名', 'name', '请输入用户组名', '',
                ' lay-verify="required" '
            )
            ->addFormItem('备注', 'remark', '请输入备注信息', '', '', FormBuildService::INPUT_TEXTAREA, 'adminbase/Public/upload')
            ->withData($group);
        $group && $inst->addFormItem('', 'id', '', '', '', FormBuildService::INPUT_HIDDEN);
        $inst->display();
    }

    /**
     * 编辑用户组
     *
     */
    public function edit()
    {
        $id = Input::getInt('id');
        AclService::currentLoginUserIsHadPermisionToOpGroup($id) || $this->renderJson(10100);//您所有的用户组没有操作该用户[组]的权限!

        $this->add(GroupsModel::getInstance()->getByColumn($id));
    }

    /**
     * 保存用户组
     *
     * @acljump adminbase/Acl/Groups/add|adminbase/Acl/Groups/edit
     *
     */
    public function save()
    {
        $data = [];
        $id = Input::postInt('id');

        $data['name'] = Input::postString('name', '');
        $data['remark'] = Input::postString('remark', '');
        $data['name'] || $this->renderJson(2, '用户组名称不能为空!');

        $groupsModel = new GroupsModel();
        if (is_null($id)) {//新增
            $res = $groupsModel->set($data);
        } else {
            AclService::currentLoginUserIsHadPermisionToOpGroup($id) || $this->renderJson(10100);//您所有的用户组没有操作该用户[组]的权限!
            LogService::addActionLog("修改了用户组[{$id}]的信息" . json_encode($data));
            $res = $groupsModel->updateByColumn($id, $data);
        }
        $this->renderJson($res ? 0 : 1);
    }


    /**
     * 删除用户组
     *
     */
    public function del()
    {
        $id = Input::getInt('id');
        $id < 1 && $this->renderJson(1);

        AclService::currentLoginUserIsHadPermisionToOpGroup($id) || $this->renderJson(10100);//您所有的用户组没有操作该用户[组]的权限!

        $groupsModel = new GroupsModel();
        $id === intval(Config::get('administratorid')) && $this->renderJson(1, '不能删除超管');
        if ($groupsModel->delByColumn($id)) {
            LogService::addActionLog("删除了用户组[{$id}]!");
            //删除对应的权限
            $accessModel = new AccessModel();
            $accessModel->delByColumn($id, 'groupid');
            $this->renderJson(0);
        } else {
            $this->renderJson(1);
        }
    }
}