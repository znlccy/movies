<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:11
 * Comment: 管理员控制器
 */

namespace app\admin\controller;

use think\Request;
use app\admin\model\Admin as AdminModel;
use app\admin\validate\Admin as AdminValidate;

class Admin extends BasisController {

    /* 声明管理员模型 */
    protected $admin_model;

    /* 声明管理员验证器 */
    protected $admin_validate;

    /* 声明管理员分页器 */
    protected $admin_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->admin_model = new AdminModel();
        $this->admin_validate = new AdminValidate();
        $this->admin_page = config('pagination');
    }

    public function entry() {

    }

    public function save() {

    }

    public function detail() {

    }

    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证数据 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->admin_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->admin_validate->getError()
            ]);
        }

        /* 返回结果 */
        $delete = $this->admin_model->where('id', $id)->delete();

        if ($delete) {
            return json([
                'code'      => '200',
                'message'   => '删除管理员成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '删除管理员失败'
            ]);
        }

    }

}