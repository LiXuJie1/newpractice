<?php
/**
 * 用户管理
 *
 * @authon linhecheng
 */

namespace adminbase\Controller\Acl;

use adminbase\Model\Acl\AccessModel;
use adminbase\Service\AclService;
use adminbase\Service\Builder\FormBuildService;
use adminbase\Service\Builder\GridBuildService;
use adminbase\Service\System\LogService;
use Cml\Plugin;
use Cml\Vendor\Acl;
use Cml\Cml;
use Cml\Config;
use Cml\Http\Input;
use adminbase\Controller\CommonController;
use adminbase\Model\Acl\GroupsModel;
use adminbase\Model\Acl\UsersModel;
use Cml\Vendor\Validate;
use adminbase\Service\SearchService;

class UsersController extends CommonController
{
    //用户列表
    public function index()
    {
        GridBuildService::create('adminbase/Acl/Users/ajaxPage', '用户列表')
            ->addSearchItem('username', '请输入用户名')
            ->addSearchItem('nickname', '请输入用户昵称')
            ->addTopButton('新增用户', FormBuildService::BUTTON_ADD, 'adminbase/Acl/Users/add', '新增用户', 'adminbase/Acl/Users/save')
            ->addColumn('id', 'id')
            ->addColumn('用户名', 'username')
            ->addColumn('昵称', 'nickname')
            ->addColumn('用户组', 'name')
            ->addColumn('状态', 'status', '', 2)
            ->addColumn('最后登录时间', 'lastlogin')
            ->addColumn('创建时间', 'ctime')
            ->addColumn('修改时间', 'stime')
            ->addButtonColumn('操作', 'id', [
                [
                    'text' => '编辑',
                    'type' => FormBuildService::BUTTON_EDIT,
                    'url' => 'adminbase/Acl/Users/edit',
                    'title' => '编辑用户',
                    'saveUrl' => 'adminbase/Acl/Users/save',
                    'class' => ''
                ],
                [
                    'text' => '删除',
                    'type' => FormBuildService::BUTTON_DEL,
                    'url' => 'adminbase/Acl/Users/del',
                    'class' => 'layui-btn-danger'
                ],
                [
                    'text' => "{{item.status == 1 ? '禁用' : '启用'}}",
                    'type' => FormBuildService::BUTTON_DISABLE,
                    'url' => 'adminbase/Acl/Users/disable',
                    'class' => '',
                    'status' => 'status',
                    'other' => ":class=\"item.status==1 ? 'layui-btn-warm' : 'layui-btn-normal'\"",
                ],
                [
                    'text' => '授权',
                    'type' => FormBuildService::BUTTON_ADD,
                    'url' => 'adminbase/Acl/Acl/user',
                    'title' => '用户授权',
                    'saveUrl' => 'adminbase/Acl/Acl/save/type/1',
                    'class' => 'layui-btn-primary'
                ]
            ])
            ->display();
    }

    /**
     * ajax请求分页
     *
     * @acljump adminbase/Acl/Users/index
     */
    public function ajaxPage()
    {
        $usersModel = new UsersModel();

        Acl::isSuperUser() || $usersModel->whereNot('u.id', Config::get('administratorid'));
        SearchService::processSearch([
            'username' => 'like',
            'nickname' => 'like'
        ], $usersModel, true);
        $totalCount = $usersModel->paramsAutoReset(false, true)->getTotalNums();

        $list = $usersModel->getUsersList(Config::get('page_num'));
        array_walk($list, function (&$item) {
            $item['lastlogin'] = date('Y-m-d H:i:s', $item['lastlogin']);
            $item['ctime'] = date('Y-m-d H:i:s', $item['ctime']);
            $item['stime'] = date('Y-m-d H:i:s', $item['stime']);
        });
        $this->renderJson(0, ['list' => $list, 'totalCount' => $totalCount]);
    }


    /**
     * 新增用户
     *
     * @param array $user 编辑的时候传入用户信息
     */
    public function add($user = [])
    {
        $showField = Plugin::hook('before_add_or_edit_user', $user ? [$user['from_type']] : []);

        $inst = FormBuildService::create($user ? '修改用户信息' : '新增用户')
            ->addFormItem('用户名', 'username', (isset($showField['username']) ? $showField['username'] : '请输入用户名'), '',
                ' lay-verify="length" lay-min="3" lay-max="10" '
            );

        if (!$showField || $showField['nickname']) {
            $inst->addFormItem('昵 称', 'nickname', '请输入昵称', '',
                ' lay-verify="length" lay-min="3" lay-max="20" '
            );
        }

        if (!$showField || $showField['password']) {
            $inst->addFormItem('密码', 'password', '请输入新密码', 'use_password', '', FormBuildService::INPUT_PASSWORD)
                ->addFormItem('重复密码', 'checkpassword', '请重复输入密码', '', ' lay-verify="repeat" lay-repeat=".use_password" lay-repeat-text="值必须和密码一样" ', FormBuildService::INPUT_PASSWORD);
        }

        $groupOptions = [];
        $group = GroupsModel::getInstance()->getAllGroups();

        foreach ($group as $item) {
            $groupOptions[] = [$item['id'], $item['name'], ($user && $user['groupid'] == $item['id']) ? 'selected' : ''];
            $inst->addFormItem('用户组', 'groupid[]', $item['name'], '', '', FormBuildService::INPUT_CHECKBOX, $item['id']);
        }
        //$inst->addFormItem('用户组', 'groupid', '', '', '', FormBuildService::INPUT_SELECT, $groupOptions);

        $inst->addFormItem('备注', 'remark', '备注信息', '', '', FormBuildService::INPUT_TEXTAREA);

        $user && $inst->addFormItem('', 'id', '', '', '', FormBuildService::INPUT_HIDDEN);
        $user && $user['groupid[]'] = explode('|', trim($user['groupid'], '|'));

        $inst->withData($user)->display();
    }

    /**
     * 编辑用户
     *
     */
    public function edit()
    {
        $id = Input::getInt('id');

        AclService::currentLoginUserIsHadPermisionToOpUser($id) || exit('您所有的用户组没有操作该用户[组]的权限!');

        $this->add(UsersModel::getInstance()->getByColumn($id));
    }

    /**
     * 保存用户
     *
     * @acljump adminbase/Acl/Users/add|adminbase/Acl/Users/edit
     *
     */
    public function save()
    {
        $data = [];
        $id = Input::postInt('id');
        $id > 0 && (AclService::currentLoginUserIsHadPermisionToOpUser($id) || $this->renderJson(10100));//'您所有的用户组没有操作该用户[组]的权限!'

        $username = Input::postString('username');
        $data['nickname'] = Input::postString('nickname');
        $data['password'] = Input::postString('password');
        $data['checkpassword'] = Input::postString('checkpassword');
        $data['groupid'] = Input::postInt('groupid');
        $data['remark'] = Input::postString('remark', '');

        if ($data['groupid']) {
            $newGroupInfo = GroupsModel::getInstance()->mapDbAndTable()->whereIn('id', $data['groupid'])->plunk('name', 'id');

            foreach($newGroupInfo as $groupId => $groupName) {
                if (!AclService::currentLoginUserIsHadPermisionToOpGroup($groupId)) {
                    $this->renderJson(10100, "您所有的用户组没有权限将该用户的用户组设为【{$groupName}】!");
                }
            }
        }

        if (!is_null($data['nickname'])) {
            if (mb_strlen($data['nickname']) < 3 || mb_strlen($data['nickname']) > 20) {
                $this->renderJson(-1, '昵称长度必须大于3小于20！');
            }
        } else {
            unset($data['nickname']);
        }

        if (isset($data['password'])) {
            $data['password'] != $data['checkpassword'] && $this->renderJson(-1, '两次密码输入不正确，请重新输入！');
        } else {
            unset($data['password']);
        }

        unset($data['checkpassword']);

        if (is_null($data['password']) || empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = md5(md5($data['password']) . Config::get('password_salt'));
        }

        $usersModel = new UsersModel();

        $data['groupid'] = $data['groupid'] ? implode('|', $data['groupid']) : '';

        if (is_null($id)) {//新增
            //第三方登录挂载点
            $otherUserInfo = Plugin::hook('before_add_user_save', [$username]);
            //判断是否已有同名用户
            $equalName = $usersModel->getByColumn($username, 'username');
            $equalName && $this->renderJson(1, '用户名已存在');

            is_array($otherUserInfo) && $data = array_merge($data, $otherUserInfo);
            $data['username'] = $username;
            $data['ctime'] = Cml::$nowTime;
            $res = $usersModel->set($data);
            LogService::addActionLog("添加了用户" . json_encode($data, JSON_UNESCAPED_UNICODE));
        } else {
            $data['stime'] = Cml::$nowTime;

            LogService::addActionLog("修改了用户[{$id}]的信息" . json_encode($data, JSON_UNESCAPED_UNICODE));
            $res = $usersModel->updateByColumn($id, $data);
        }
        $this->renderJson($res ? 0 : 1);
    }

    /**
     * 删除用户
     *
     */
    public function del()
    {
        $id = Input::getInt('id');
        $id < 1 && $this->renderJson(1);
        $users = new UsersModel();

        AclService::currentLoginUserIsHadPermisionToOpUser($id) || $this->renderJson(10100);

        if ($id === intval(Config::get('administratorid'))) {
            $this->renderJson(1, '不能删除超管');
        }

        if ($users->delByColumn($id)) {
            LogService::addActionLog("删除了用户[{$id}]");
            //删除对应的权限
            $accessModel = new AccessModel();
            $accessModel->delByColumn($id, 'userid');
            $this->renderJson(0);
        } else {
            $this->renderJson(1);
        }
    }

    /**
     * 禁用/解禁用户
     *
     */
    public function disable()
    {
        $data = [];
        $id = Input::getInt('id');

        AclService::currentLoginUserIsHadPermisionToOpUser($id) || $this->renderJson(10100);

        $status = Input::getInt('status');

        $data['status'] = $status ? 0 : 1;
        $id < 1 && $this->renderJson(1);
        $users = new UsersModel();
        if ($id === intval(Config::get('administratorid'))) {
            $this->renderJson(1, '不能操作超管');
        }

        if ($res = $users->updateByColumn($id, $data)) {
            LogService::addActionLog("禁用了用户[{$id}]");
            $this->renderJson(0);
        } else {
            $this->renderJson(1);
        }
    }

    /**
     * 修改个人资料
     * @noacl
     */
    public function editSelfInfo()
    {
        $user = Acl::getLoginInfo();
        $user = UsersModel::getInstance()->getByColumn($user['id']);
        $showField = Plugin::hook('before_add_or_edit_user', [$user['from_type']]);

        $inst = FormBuildService::create('个人资料')
            ->addFormItem('昵 称', 'nickname', '请输入昵称', '',
                'lay-verify="length" lay-min="3" lay-max="20" ' . isset($showField['nickname']) ? '' : ' disabled '
            );
        if (!$showField || $showField['password']) {
            $inst->addFormItem('密 码', 'oldpwd', '请输入旧密码', '', '', FormBuildService::INPUT_PASSWORD)
                ->addFormItem('新密码', 'pwd', '请输入新密码', 'use_password', '', FormBuildService::INPUT_PASSWORD)
                ->addFormItem('重复新密码', 'repwd', '重复新密码', '', ' lay-verify="repeat" lay-repeat=".use_password" lay-repeat-text="值必须和新密码一样" ', FormBuildService::INPUT_PASSWORD);
        }
        $inst->withData(['nickname' => $user['nickname']])
            ->display();
    }

    /**
     * 修改个人资料 - 保存
     *
     * @noacl
     *
     */
    public function saveSelfInfo()
    {
        $user = Acl::getLoginInfo();
        $userModel = new UsersModel();
        $user = $userModel->getByColumn($user['id']);

        $data = [];
        $data['nickname'] = Input::postString('nickname');
        $data['stime'] = Cml::$nowTime;

        if (isset($_POST['oldpwd']) && Validate::isLength($_POST['oldpwd'], 6, 20)) {
            if ($user['password'] != md5(md5($_POST['oldpwd']) . Config::get('password_salt'))) {
                $this->renderJson(10201);
            }

            if ($_POST['pwd'] != $_POST['repwd']) {
                $this->renderJson(10202);
            }

            if (!Validate::isLength($_POST['pwd'], 6, 20)) {
                $this->renderJson(2, '新密码长度必须为6-20个字符！');
            }
            $data['password'] = md5(md5($_POST['pwd']) . Config::get('password_salt'));
            Acl::logout();
        }

        if ($userModel->updateByColumn($user['id'], $data)) {
            $this->renderJson(0);
        }
    }
}