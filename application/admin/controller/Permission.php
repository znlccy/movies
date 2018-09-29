<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:11
 * Comment: 权限控制器
 */

namespace app\admin\controller;

use app\admin\model\Permission as PermissionModel;
use app\admin\validate\Permission as PermissionValidate;
use think\Request;

class Permission extends BasisController {

    /* 权限模型 */
    protected $permission_model;

    /* 权限验证器 */
    protected $permission_validate;

    /* 权限分页器 */
    protected $permission_page;

    /* 默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->permission_model = new PermissionModel();
        $this->permission_validate = new PermissionValidate();
        $this->permission_page = config('pagination');
    }

    public function entry() {

    }

    /* 权限添加更新 */
    public function save() {

        /* 接收参数 */
        $id = request()->param('id');

    }

    /* 权限详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->permission_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->permission_validate->getError()
            ]);
        }

        /* 返回结果 */
        $detail = $this->permission_model->where('id', $id)->find();

        if ($detail) {
            return json([
                'code'      => '200',
                'message'   => '查询消息成功',
                'data'      => $detail
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询消息失败'
            ]);
        }
    }

    /* 权限删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->permission_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->permission_validate->getError()
            ]);
        }

        /* 返回结果 */
        $delete = $this->permission_model->where('id', $id)->delete();

        if ($delete) {
            return json([
                'code'      => '200',
                'message'   => '删除数据成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '删除数据失败'
            ]);
        }
    }

}