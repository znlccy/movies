<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:18
 * Comment: 权限模型
 */

namespace app\admin\model;

class Permission extends BasisModel {

    /* 时间戳 */
    protected $autoWriteTimestamp = 'datetime';

    /* 对应表 */
    protected $table = 'tb_permission';

    /* 关联表 */
    public function roles() {
        return $this->belongsToMany('Role','tb_role_permission','role_id','permission_id');
    }
}