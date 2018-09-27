<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:18
 * Comment: 角色模型
 */

namespace app\admin\model;

class Role extends BasisModel {

    /* 时间戳 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应表 */
    protected $table = 'tb_role';

    /* 关联表 */
    public function admins() {
        return $this->belongsToMany('Admin','tb_admin_role','user_id','role_id');
    }

    /* 关联表 */
    public function permissions() {
        return $this->belongsToMany('Permission','tb_role_permission','permission_id','role_id');
    }
}