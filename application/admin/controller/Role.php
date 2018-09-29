<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:11
 * Comment: 角色控制器
 */

namespace app\admin\controller;

use app\admin\model\Role as RoleModel;
use app\admin\validate\Role as RoleValidate;
use think\Request;

class Role extends BasisController {

    /* 声明角色模型 */
    protected $role_model;

    /* 声明角色验证器 */
    protected $role_validate;

    /* 声明角色分页器 */
    protected $role_page;

    /* 声明默认构造函数 */
    public function __construct(Request $request = null) {
        parent::__construct($request);
        $this->role_model = new RoleModel();
        $this->role_validate = new RoleValidate();
    }

    /* 角色列表 */
    public function entry() {

    }

    /* 角色添加更新 */
    public function save() {

    }

    /* 角色详情 */
    public function detail() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->role_validate->scene('detail')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->role_validate->getError()
            ]);
        }

        /* 返回结果 */
        $detail = $this->role_model->where('id', $id)->find();

        if ($detail) {
            return json([
                'code'      => '200',
                'message'   => '查询角色成功',
                'data'      => $detail
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '查询角色失败'
            ]);
        }
    }

    /* 角色删除 */
    public function delete() {

        /* 接收参数 */
        $id = request()->param('id');

        /* 验证参数 */
        $validate_data = [
            'id'        => $id
        ];

        /* 验证结果 */
        $result = $this->role_validate->scene('delete')->check($validate_data);

        if (true !== $result) {
            return json([
                'code'      => '401',
                'message'   => $this->role_validate->getError()
            ]);
        }

        /* 返回结果 */
        $delete = $this->role_model->where('id', $id)->delete();

        if ($delete) {
            return json([
                'code'      => '200',
                'message'   => '删除角色成功'
            ]);
        } else {
            return json([
                'code'      => '404',
                'message'   => '删除角色失败'
            ]);
        }
    }


}