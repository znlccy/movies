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

    }


}