<?php
/* * *********************************************************
* 数据列表构建
* @Author  linhecheng<linhechengbush@live.com>
* @Date: 2017/1/17 16:43
* *********************************************************** */

namespace adminbase\Service\Builder;

use Cml\Cml;
use Cml\View;

class GridBuildService extends Base
{
    /**
     * 本类只构建列表页面中的搜索表单及数据列表表格的表头。数据是通过这边配置的地址异步加载
     *
     * @var string
     */
    private $ajaxGetDataUrl = '';

    /**
     * 要执行的js
     *
     * @var string
     */
    private $toPageJs = <<<js
layui.use('form', function(){
  var form = layui.form;
});
js;

    /**
     * 页面顶部的标题
     *
     * @var string
     */
    private $title = '数据列表';

    /**
     * 列表顶部搜索表单项
     *
     * @var array
     */
    private $searchField = [];

    /**
     * 列表顶部搜索表单旁边的按钮。如:新增
     *
     * @var array
     */
    private $topButton = [];

    /**
     * 数据列表的表格信息
     *
     * @var array
     */
    private $table = [];

    /**
     * 是否有批量处理
     *
     * @var bool
     */
    private $checkAll = false;

    /**
     * 本类只构建列表页面中的搜索表单及数据列表表格的表头。数据是通过这边配置的地址异步加载
     *
     * @param string $ajaxGetDataUrl //输出的时候会自动使用 url模板标签输出
     * @param string $title 页面的标题
     */
    public function __construct($ajaxGetDataUrl, $title = '')
    {
        if (empty($ajaxGetDataUrl)) {
            throw new \InvalidArgumentException('数据加载地址必须填写');
        }
        $this->ajaxGetDataUrl = $ajaxGetDataUrl;
        $title && $this->title = $title;
    }

    /**
     * 添加一个列表顶部搜索表单项
     *
     * @param string $name 表单的name属性
     * @param string $placeholder 表单的$placeholder属性
     * @param string $type 表单的type
     * @param mixed $val value值 为数组的时候是select的option选项 如：[['1’, '是', 'selected']] 生成的html <option value="1" selected >是</option>
     * @param string $other 其它信息。直接输出如disable。
     * @param string $class 表单元素额外的class
     *
     * 例子: ->addSearchItem('name', '请输入用户组名称')
     *
     * @return $this
     */
    public function addSearchItem($name = '', $placeholder = '', $type = FormBuildService::INPUT_TEXT, $val = '', $other = '', $class = '')
    {

        if (is_array($val)) {
            $option = '';
            foreach ($val as $item) {
                $option .= "<option value='{$item[0]}' {$item[2]} >{$item[1]}</option>";
            }
            $val = $option;
        }

        if ($type == FormBuildService::INPUT_EDITOR) {
            $this->toPageJs .= <<<str
layui.use('layedit', function(){
  var layedit = layui.layedit;
  layedit.build('editor_{$name}');
});
str;
        } else if ($type == FormBuildService::INPUT_DATE) {
            $type = 'text';//用date类型laydate有bug
            $class .= ' laydate_select_' . $name;
            $this->toPageJs .= <<<str
layui.use('laydate', function(){
    layui.laydate.render({elem: '.laydate_select_{$name}', type: 'date', format: 'yyyy-MM-dd'})
});
str;
        } else if ($type == FormBuildService::INPUT_DATETIME) {
            $class .= ' laydate_select_' . $name;
            $this->toPageJs .= <<<str
layui.use('laydate', function(){
    layui.laydate.render({elem: '.laydate_select_{$name}', type: 'datetime', format: 'yyyy-MM-dd HH:mm:ss'})
});
str;
        }

        $this->searchField[] = [
            'name' => $name,
            'placeholder' => $placeholder,
            'type' => $type,
            'val' => $val,
            'other' => $other,
            'class' => $class
        ];
        return $this;
    }

    /**
     * 添加一个列表顶部搜索旁边的按钮。
     *
     * @param string $text 按钮的text内容
     * @param string $type 类型
     * @param string $newPageUrl 打开的页面的url 输出的时候会自动使用 url模板标签输出
     * @param string $newPageTitle 打开的页面的title type=del时这个参数传id type=disable时为提示语
     * @param string $saveDateUrl 打开的页面中数据保存的地址 输出的时候会自动使用 url模板标签输出 type=del时这个参数为msg。type=disable时为状态的字段如status。
     * @param string $class 额外的按钮的样式
     * @param string $other 其它信息。直接输出如disable。
     * @param mixed $layerWidth add/edit时弹出层的宽高
     *
     * 例子： 如:新增 ->addTopButton('新增', FormBuildService::BUTTON_ADD, 'adminbase/Acl/Groups/add', '新增用户组', 'adminbase/Acl/Groups/save')
     *
     * @return $this
     */
    public function addTopButton($text = '', $type = FormBuildService::BUTTON_ADD, $newPageUrl = '', $newPageTitle = '', $saveDateUrl = '', $class = '', $other = '', $layerWidth = false)
    {
        $this->topButton[] = [
            'text' => $text,
            'type' => $type,
            'url' => $newPageUrl,
            'title' => $newPageTitle,
            'saveUrl' => $saveDateUrl,
            'class' => $class,
            'other' => $other,
            'width' => $layerWidth
        ];
        return $this;
    }

    /**
     * 添加数据列
     *
     * @param string $text 列的文字
     * @param string $name 对应数据列的字段
     * @param string $other 其它信息如 直接输出到标签属性
     * @param int $type 类型1为文本 | 2为打勾打x。当值为>0时打勾。<=0时打x | 3为按钮(直接调用addButtonColumn)  |  5为checkbox(直接调用addCheckBoxColumn) | 6为图片  | 7为a标签 | 8为html
     *
     * 例子：->addColumn('用户组名称', 'name')
     *
     * @return $this
     */
    public function addColumn($text = '', $name = '', $other = '', $type = 1)
    {
        $this->table[] = [
            'text' => $text,
            'name' => $name,
            'other' => $other,
            'type' => $type
        ];
        return $this;
    }

    /**
     * 添加全选/反选批量操作列
     *
     * @param string $name 对应数据列的字段
     * @param array $buttons 按钮组... 参考本类addTopButton方法的注释。
     *
     * 例子：->addCheckBoxColumn('id', [
     * [
     * 'text' => '删除',
     * 'type' => FormBuildService::BUTTON_DEL,
     * 'url' => 'adminbase/Acl/Users/del',
     * 'class' => 'layui-btn-danger',
     * 'other' => ''
     * ],
     * [
     * 'text' => "禁用",
     * 'type' => FormBuildService::BUTTON_DISABLE,
     * 'url' => 'adminbase/Acl/Users/disable',
     * 'class' => "layui-btn-warm",
     * 'other' => ''
     * ]
     * ])
     *
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function addCheckBoxColumn($name = '', $buttons = [])
    {
        $allowType = [FormBuildService::BUTTON_DISABLE, FormBuildService::BUTTON_DEL];
        foreach ($buttons as $button) {
            if (!in_array($button['type'], $allowType)) {
                throw new \InvalidArgumentException('addCheckBoxColumn类型的按钮只能为' . json_encode($allowType));
            }
        }
        $this->table[] = [
            'text' => '<input type="checkbox"  v-model="cCheckAll">',
            'name' => $name,
            'type' => 5
        ];
        $this->checkAll = [
            'name' => $name,
            'buttons' => $buttons
        ];
        return $this;
    }

    /**
     * 添加按钮列。
     *
     * @param string $text 列的文字
     * @param string $id 传值给要打开的页面 遍历数组的时候会自动将每一条的$id字段的值传给请求地址
     * @param array $buttons 按钮组... 参考本类addTopButton方法的注释。
     * @param string $other 其它信息如 直接输出到标签属性
     *
     * 例子:
     * ->addButtonColumn('操作', 'id', [
     * [
     * 'text' => '编辑',
     * 'type' => FormBuildService::BUTTON_EDIT,
     * 'url' => 'adminbase/Acl/Users/edit',
     * 'title' => '编辑用户',
     * 'saveUrl' => 'adminbase/Acl/Users/save',
     * 'class' => ''
     * ],
     * [
     * 'text' => '删除',
     * 'type' => FormBuildService::BUTTON_DEL,
     * 'url' => 'adminbase/Acl/Users/del',
     * 'class' => 'layui-btn-danger'
     * ],
     * [
     * 'text' => "{{item.status == 1 ? '禁用' : '启用'}}",
     * 'type' => FormBuildService::BUTTON_DISABLE,
     * 'url' => 'adminbase/Acl/Users/disable',
     * 'class' => '',
     * 'status' => 'status',
     * 'other' => ":class=\"item.status==1 ? 'layui-btn-warm' : 'layui-btn-normal'\"",
     * ],
     * [
     * 'text' => '授权',
     * 'type' => FormBuildService::BUTTON_ADD,
     * 'url' => 'adminbase/Acl/Acl/user',
     * 'title' => '用户组授权',
     * 'saveUrl' => 'adminbase/Acl/Acl/save/type/1',
     * 'class' => 'layui-btn-primary',
     * 'width' => "['100%', '100%']"
     * ]
     * ])
     *
     * @return $this
     */
    public function addButtonColumn($text = '', $id = 'id', $buttons = [], $other = '')
    {
        foreach ($buttons as &$val) {
            isset($val['status']) && $val['saveUrl'] = $val['status'];
        }
        $this->table[] = [
            'id' => $id,
            'text' => $text,
            'buttons' => $buttons,
            'type' => 3,
            'other' => $other
        ];
        return $this;
    }

    /**
     * 可使用$inst->setLayerWidthHeight("['100%', '100%']");设置页面中按钮打开layer弹出层的宽高
     * 渲染模板并输出
     *
     * @param string $layout 布局
     * @param string $layoutInOtherApp 布局所在的应用名
     * @param string $tpl 使用的模板
     */
    public function display($layout = 'regional', $layoutInOtherApp = 'adminbase', $tpl = 'list')
    {
        if (empty($this->getLayerWidthHeight())) {
            $this->setLayerWidthHeight("'500px'");
        }

        parent::display($layout, $layoutInOtherApp);

        echo View::getEngine('html')
            ->assignByRef('search', $this->searchField)
            ->assignByRef('buttons', $this->topButton)
            ->assignByRef('table', $this->table)
            ->assignByRef('ajaxUrl', $this->ajaxGetDataUrl)
            ->assignByRef('topTitle', $this->title)
            ->assignByRef('toPageJs', $this->toPageJs)
            ->assignByRef('checkAll', $this->checkAll)
            ->fetch($tpl, false, true, true);
        Cml::cmlStop();
    }

    /**
     * 本类只构建列表页面中的搜索表单及数据列表表格的表头。数据是通过这边配置的地址异步加载
     *
     * @param string $ajaxGetDataUrl //输出的时候会自动使用 url模板标签输出
     * @param string $title 页面的标题
     *
     * @return $this
     */
    public static function create($ajaxGetDataUrl = '', $title = '')
    {
        return new self($ajaxGetDataUrl, $title);
    }
}
