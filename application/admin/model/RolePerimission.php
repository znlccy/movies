<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/8
 * Time: 16:15
 * Comment: 角色权限模型
 */

namespace app\admin\model;

class RolePerimission extends BasisModel {

    /* 读写时间 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应的表 */
    protected $table = 'tb_role_permission';

}